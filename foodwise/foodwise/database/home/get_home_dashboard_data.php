<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Impostazioni per la gestione degli errori (produzione)
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_home_errors.log'); // Log degli errori specifici

// Inclusione della connessione al database
if (file_exists(__DIR__ . '/../connection.php')) {
    include_once __DIR__ . '/../connection.php';
} else {
    error_log("get_home_dashboard_data.php: CRITICAL - Cannot find connection.php at " . __DIR__ . "/../");
    // Definisci variabili di default per evitare errori fatali in home.php
    $user_full_name_data = "N/D";
    $user_role_data = "N/D";
    $restaurant_id_data = "N/D";
    $todays_revenue_data = 0.00;
    $active_orders_count_data = 0;
    $occupied_tables_data = 0;
    $total_active_tables_data = 0;
    $current_customers_at_occupied_tables_data = 0;
    $max_capacity_all_active_tables_data = 0;
    $upcoming_reservations_data = [];
    $customer_reviews_data = [];
    $average_rating_data = 0;
    $total_reviews_data = 0;
    $capacity_percentage_data = 0;
    return;
}

$conn = connessione();

// Inizializzazione delle variabili con valori di default
$user_full_name_data = "Ospite";
$user_role_data = "Sconosciuto";
$restaurant_id_data = $_SESSION['login_restaurant'] ?? null;
$current_username_data = $_SESSION['login_username'] ?? null;

$todays_revenue_data = 0.00;
$active_orders_count_data = 0;
$occupied_tables_data = 0;
$total_active_tables_data = 0;
$current_customers_at_occupied_tables_data = 0;
$max_capacity_all_active_tables_data = 0; // Già inizializzata, la nuova query la popolerà
$upcoming_reservations_data = [];
$customer_reviews_data = [];
$average_rating_data = 0;
$total_reviews_data = 0;
$capacity_percentage_data = 0;

if (!$conn) {
    error_log("get_home_dashboard_data.php: Database connection failed.");
} elseif (!$restaurant_id_data) {
    error_log("get_home_dashboard_data.php: Restaurant ID missing from session.");
} else {
    // --- INIZIO SEZIONE MODIFICATA PER IL NOME UTENTE ---
    // 1. Nome e Ruolo Utente
    if (empty($current_username_data) && !empty($restaurant_id_data)) {
        $user_full_name_data = "Ristoratore";
        $user_role_data = "Proprietario";
    }
    elseif (!empty($current_username_data)) {
        $query_user_info = "SELECT full_name, ruolo FROM personale WHERE username = $1 AND id_ristorante = $2";
        $result_user_info = pg_query_params($conn, $query_user_info, [$current_username_data, $restaurant_id_data]);
        if ($result_user_info && pg_num_rows($result_user_info) > 0) {
            $user_info_row = pg_fetch_assoc($result_user_info);
            $user_full_name_data = htmlspecialchars($user_info_row['full_name']);
            $user_role_data = htmlspecialchars($user_info_row['ruolo']);
        } else {
            $user_full_name_data = htmlspecialchars($current_username_data);
            error_log("get_home_dashboard_data.php: User info not found for username '$current_username_data' and restaurant '$restaurant_id_data'. Displaying username as name.");
        }
    }
    // --- FINE SEZIONE MODIFICATA PER IL NOME UTENTE ---

    // 2. Incasso Odierno
    $query_revenue = "SELECT SUM(details_o.quantita * details_o.prezzo_unitario_registrato) AS total_revenue 
                      FROM public.ordini o 
                      JOIN public.dettagli_ordine details_o ON o.id_ordine = details_o.id_ordine 
                      WHERE o.id_ristorante = $1 AND o.pagato = TRUE AND DATE(o.data_ordine) = CURRENT_DATE";
    $result_revenue = pg_query_params($conn, $query_revenue, [$restaurant_id_data]);
    if ($result_revenue && pg_num_rows($result_revenue) > 0) {
        $revenue_data_row = pg_fetch_assoc($result_revenue);
        $todays_revenue_data = $revenue_data_row['total_revenue'] ? (float) $revenue_data_row['total_revenue'] : 0.00;
    } else if (!$result_revenue) {
        error_log("get_home_dashboard_data.php: Errore query incasso odierno: " . pg_last_error($conn));
    }

    // 3. Ordini Attivi (non completati o cancellati)
    $query_active_orders = "SELECT COUNT(*) AS count 
                            FROM public.ordini 
                            WHERE id_ristorante = $1 AND stato NOT IN ('completed', 'cancelled')";
    $result_active_orders = pg_query_params($conn, $query_active_orders, [$restaurant_id_data]);
    if ($result_active_orders && pg_num_rows($result_active_orders) > 0) {
        $active_orders_data_row = pg_fetch_assoc($result_active_orders);
        $active_orders_count_data = (int) $active_orders_data_row['count'];
    } else if (!$result_active_orders) {
        error_log("get_home_dashboard_data.php: Errore query ordini attivi: " . pg_last_error($conn));
    }

    // 4. Dati Tavoli
    // Numero totale di tavoli attivi per il ristorante
    $query_total_active_tables = "SELECT COUNT(*) as num_tavoli
                                  FROM public.tavoli 
                                  WHERE id_ristorante = $1 AND attivo = TRUE";
    $result_total_active_tables = pg_query_params($conn, $query_total_active_tables, [$restaurant_id_data]);
    if ($result_total_active_tables && pg_num_rows($result_total_active_tables) > 0) {
        $total_active_tables_row = pg_fetch_assoc($result_total_active_tables);
        $total_active_tables_data = (int) $total_active_tables_row['num_tavoli'];
    } else if (!$result_total_active_tables) {
        error_log("get_home_dashboard_data.php: Errore query conteggio tavoli attivi: " . pg_last_error($conn));
        $total_active_tables_data = 0; // Assicura che sia 0 in caso di errore
    }

    // MODIFICA: Calcolo della capacità massima totale basata sulla somma della colonna 'clienti' dei tavoli attivi.
    // Si assume che la colonna 'tavoli.clienti' contenga la capacità (numero massimo di posti) di ogni tavolo.
    $query_max_capacity = "SELECT SUM(COALESCE(clienti, 0)) as total_capacity
                           FROM public.tavoli 
                           WHERE id_ristorante = $1 AND attivo = TRUE";
    $result_max_capacity = pg_query_params($conn, $query_max_capacity, [$restaurant_id_data]);
    if ($result_max_capacity && pg_num_rows($result_max_capacity) > 0) {
        $max_capacity_row = pg_fetch_assoc($result_max_capacity);
        // $max_capacity_all_active_tables_data è già inizializzata a 0, qui viene aggiornata
        $max_capacity_all_active_tables_data = $max_capacity_row['total_capacity'] ? (int) $max_capacity_row['total_capacity'] : 0;
    } else if (!$result_max_capacity) {
        error_log("get_home_dashboard_data.php: Errore query capacità massima tavoli: " . pg_last_error($conn));
        // $max_capacity_all_active_tables_data rimane 0 se la query fallisce o non restituisce righe
    }
    // FINE MODIFICA

    // Numero di tavoli attualmente occupati
    $query_occupied_tables = "SELECT COUNT(*) as num_tavoli_occupati 
                              FROM public.tavoli 
                              WHERE id_ristorante = $1 AND attivo = TRUE AND (stato ILIKE 'Occupied' OR stato ILIKE 'occupato')"; // Aggiunto 'occupato' per sicurezza
    $result_occupied_tables = pg_query_params($conn, $query_occupied_tables, [$restaurant_id_data]);
    if ($result_occupied_tables && pg_num_rows($result_occupied_tables) > 0) {
        $occupied_tables_row = pg_fetch_assoc($result_occupied_tables);
        $occupied_tables_data = (int) $occupied_tables_row['num_tavoli_occupati'];
    } else if (!$result_occupied_tables) {
        error_log("get_home_dashboard_data.php: Errore query tavoli occupati: " . pg_last_error($conn));
    }

    // Numero totale di clienti seduti ai tavoli occupati
    // ATTENZIONE: Questa query assume che 'tavoli.clienti' venga aggiornato con il numero EFFETTIVO di clienti 
    // quando un tavolo è 'Occupied'. Se 'tavoli.clienti' rappresenta SEMPRE la capacità del tavolo, 
    // allora questa query sommerà le capacità dei tavoli occupati, non il numero attuale di persone.
    // Questo potrebbe essere un punto da chiarire in base alla logica di gestione del campo 'clienti'.
    $query_customers_at_occupied = "SELECT SUM(COALESCE(clienti, 0)) as clienti_su_tavoli_occupati
                                    FROM public.tavoli 
                                    WHERE id_ristorante = $1 AND attivo = TRUE AND (stato ILIKE 'Occupied' OR stato ILIKE 'occupato')";
    $result_customers_at_occupied = pg_query_params($conn, $query_customers_at_occupied, [$restaurant_id_data]);
    if ($result_customers_at_occupied && pg_num_rows($result_customers_at_occupied) > 0) {
        $customers_row = pg_fetch_assoc($result_customers_at_occupied);
        $current_customers_at_occupied_tables_data = (int) $customers_row['clienti_su_tavoli_occupati'];
    } else if (!$result_customers_at_occupied) {
        error_log("get_home_dashboard_data.php: Errore query clienti ai tavoli occupati: " . pg_last_error($conn));
    }

    // Percentuale di capacità
    // Utilizza la $max_capacity_all_active_tables_data calcolata correttamente
    $capacity_percentage_data = ($max_capacity_all_active_tables_data > 0) ? round(($current_customers_at_occupied_tables_data / $max_capacity_all_active_tables_data) * 100) : 0;

    // 5. Prossime Prenotazioni (ultime 3)
    // Modifica: Aggiunto id_ristorante alla join con tavoli, sebbene non strettamente necessario se id_tavolo è univoco globalmente,
    // ma è buona pratica per coerenza, assumendo che prenotazioni.id_ristorante esista e sia popolato.
    // Dallo schema fornito, la tabella prenotazioni non ha id_ristorante, ma tavoli sì. La join è su id_tavolo.
    // Assumiamo che le prenotazioni siano per il ristorante corrente, quindi filtriamo p.id_ristorante (se esistesse)
    // o implicitamente tramite i tavoli del ristorante. Lo schema attuale non mostra p.id_ristorante.
    // Per ora, la query si basa sul fatto che i tavoli referenziati appartengano al ristorante.
    // Se 'prenotazioni' avesse 'id_ristorante', andrebbe aggiunto: AND p.id_ristorante = $1
    $query_reservations = "SELECT p.nome_prenotazione, p.numero_persone, p.ore, t.numero_tavolo 
                           FROM public.prenotazioni p 
                           JOIN public.tavoli t ON p.id_tavolo = t.id_tavolo
                           WHERE t.id_ristorante = $1 -- Filtra per i tavoli del ristorante corrente
                           ORDER BY p.ore ASC, p.id_prenotazione ASC LIMIT 3";
    $result_reservations = pg_query_params($conn, $query_reservations, [$restaurant_id_data]);
    if ($result_reservations) {
        while ($row_res = pg_fetch_assoc($result_reservations)) {
            $upcoming_reservations_data[] = $row_res;
        }
    } else {
        error_log("get_home_dashboard_data.php: Errore query prenotazioni: " . pg_last_error($conn));
    }

    // 6. Recensioni Clienti (ultime 3)
    $query_reviews = "SELECT nome, descrizione, stelle 
                      FROM public.recensioni 
                      WHERE id_ristorante = $1 
                      ORDER BY data_recensione DESC, id_recensione DESC LIMIT 3"; // Ordina per data e poi ID
    $result_reviews = pg_query_params($conn, $query_reviews, [$restaurant_id_data]);
    if ($result_reviews) {
        while ($row_rev = pg_fetch_assoc($result_reviews)) {
            $customer_reviews_data[] = $row_rev;
        }
    } else {
        error_log("get_home_dashboard_data.php: Errore query recensioni: " . pg_last_error($conn));
    }

    // Media e totale recensioni
    $query_avg_rating = "SELECT AVG(stelle) as avg_rating, COUNT(*) as total_reviews 
                         FROM public.recensioni 
                         WHERE id_ristorante = $1";
    $result_avg_rating = pg_query_params($conn, $query_avg_rating, [$restaurant_id_data]);
    if ($result_avg_rating && pg_num_rows($result_avg_rating) > 0) {
        $rating_data_row = pg_fetch_assoc($result_avg_rating);
        $average_rating_data = $rating_data_row['avg_rating'] ? round((float) $rating_data_row['avg_rating'], 1) : 0;
        $total_reviews_data = (int) $rating_data_row['total_reviews'];
    } else if (!$result_avg_rating) {
        error_log("get_home_dashboard_data.php: Errore query media recensioni: " . pg_last_error($conn));
    }

    if ($conn) {
        pg_close($conn);
    }
}
?>
