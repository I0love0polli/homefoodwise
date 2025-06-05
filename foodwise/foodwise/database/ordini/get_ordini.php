<?php
session_start();

// ----- GESTIONE DEGLI ERRORI -----
ini_set('display_errors', 0); // Non mostrare errori PHP direttamente nell'output
error_reporting(E_ALL);       // Logga tutti gli errori
ini_set('log_errors', 1);     // Abilita il logging degli errori su file
ini_set('error_log', __DIR__.'/php_error.log'); // Specifica il file di log
ob_start();                   // Inizia il buffering dell'output
// ----- FINE GESTIONE ERRORI -----

header('Content-Type: application/json');
include '../connection.php'; // Assicurati che questo percorso sia corretto

if (!isset($_SESSION['login_restaurant']) || empty($_SESSION['login_restaurant'])) {
    if (ob_get_level() > 0) ob_clean();
    echo json_encode(['success' => false, 'message' => 'Ristorante non autenticato.']);
    if (ob_get_level() > 0) ob_end_flush();
    exit;
}
$id_ristorante_sessione = $_SESSION['login_restaurant'];

$conn = connessione();

$ordini_map = [];
$lista_id_ordini = [];
$json_output_sent = false;

// Funzione helper per formattare l'intervallo di tempo da PostgreSQL AGE()
function format_postgres_interval($interval_string) {
    if ($interval_string === null || trim($interval_string) === '') { // Controllo più robusto per stringa vuota o null
        return "poco fa";
    }

    $tempo_trascorso_format = "";
    // Regex migliorata per catturare giorni, ore, minuti, secondi.
    // Esempio formati AGE: "1 day 02:03:04.567", "01:02:03", "2 days", "-3 days -01:00:00" (per date future)
    // Questa regex è un tentativo, potrebbe necessitare di aggiustamenti per tutti i casi limite di AGE.
    // Considera che AGE può restituire anche valori negativi se data_ordine è nel futuro.
    if (strpos($interval_string, '-') === 0) { // Se l'intervallo è negativo (data nel futuro)
        // Potresti voler gestire questo caso diversamente, es. "tra X tempo" o semplicemente mostrare il valore.
        // Per ora, lo trattiamo come se fosse positivo per la formattazione "fa".
        $interval_string = ltrim($interval_string, '-');
    }

    preg_match('/(?:(\d+)\s*days?)?\s*(?:(\d{1,2}):(\d{1,2}):(\d{1,2})(?:\.\d+)?)?/', $interval_string, $matches);
    
    $days = !empty($matches[1]) ? (int)$matches[1] : 0;
    $hours = !empty($matches[2]) ? (int)$matches[2] : 0;
    $minutes = !empty($matches[3]) ? (int)$matches[3] : 0;
    // $seconds = !empty($matches[4]) ? (int)$matches[4] : 0; // Non usato nel formato finale

    if ($days > 0) {
        $tempo_trascorso_format .= $days . ($days === 1 ? "g " : "gg ");
    }
    if ($hours > 0) {
        $tempo_trascorso_format .= $hours . "h ";
    }
    if ($minutes > 0) {
        $tempo_trascorso_format .= $minutes . "min ";
    }
    
    $tempo_trascorso_format = trim($tempo_trascorso_format);

    if (empty($tempo_trascorso_format)) {
        // Se dopo il parsing non c'è nulla (es. solo secondi), consideralo "poco fa"
        return "poco fa";
    } else {
        return $tempo_trascorso_format . " fa";
    }
}

// Funzione helper per eseguire query con parametri compatibile con PHP 8.1+
function execute_query_with_params($conn, $query, $params = []) {
    if (empty($params)) {
        return pg_query($conn, $query);
    } else {
        return pg_query_params($conn, $query, $params);
    }
}

try {
    // 1. Recupera gli ordini principali e calcola l'età direttamente in SQL
    $query_ordini = "SELECT o.id_ordine, o.id_tavolo, t.numero_tavolo, o.data_ordine, 
                            o.stato AS stato_ordine, o.pagato, 
                            AGE(now(), o.data_ordine) AS intervallo_eta
                     FROM public.ordini AS o 
                     LEFT JOIN public.tavoli AS t ON o.id_tavolo = t.id_tavolo AND o.id_ristorante = t.id_ristorante 
                     WHERE o.id_ristorante = $1 AND o.stato NOT IN ('completed', 'cancelled', 'served') 
                     ORDER BY o.data_ordine ASC"; 

    $result_ordini = execute_query_with_params($conn, $query_ordini, [$id_ristorante_sessione]);

    if (!$result_ordini) {
        // Log dell'errore SQL effettivo
        error_log("Errore SQL in get_ordini.php (query_ordini) per Ristorante ID $id_ristorante_sessione: " . pg_last_error($conn));
        throw new Exception("Errore durante il recupero degli ordini."); // Messaggio generico per il client
    }

    while ($ordine = pg_fetch_assoc($result_ordini)) {
        $id_ordine_corrente = $ordine['id_ordine'];
        $lista_id_ordini[] = $id_ordine_corrente;
        
        $ordini_map[$id_ordine_corrente] = [
            'id_ordine' => $ordine['id_ordine'],
            'numero_tavolo' => $ordine['numero_tavolo'] ?? 'Asporto',
            'data_ordine_originale' => $ordine['data_ordine'],
            // ***** RIGA CORRETTA *****
            'tempo_trascorso' => format_postgres_interval($ordine['intervallo_eta']),
            'stato_ordine' => $ordine['stato_ordine'],
            'pagato' => (bool)$ordine['pagato'],
            'items' => [],
            'prezzo_totale_calcolato' => 0,
            'percentuale_completamento' => 0
        ];
    }

    // 2. Recupera tutti i dettagli per gli ordini trovati (se ce ne sono)
    if (!empty($lista_id_ordini)) {
        $placeholders_sql = implode(',', array_map(function($i) { return '$'.($i+1); }, array_keys($lista_id_ordini)));

        $query_dettagli_tutti = "SELECT det.id_ordine, det.id_dettaglio_ordine, prod.nome AS nome_prodotto, 
                                      det.quantita, det.prezzo_unitario_registrato, det.note_riga, det.stato_item
                               FROM public.dettagli_ordine AS det 
                               JOIN public.prodotti AS prod ON det.id_portata = prod.id_portata 
                               WHERE det.id_ordine IN ($placeholders_sql)
                               ORDER BY det.id_ordine, det.id_dettaglio_ordine ASC";
        
        $result_dettagli_tutti = execute_query_with_params($conn, $query_dettagli_tutti, $lista_id_ordini);

        if (!$result_dettagli_tutti) {
            error_log("Errore SQL in get_ordini.php (query_dettagli_tutti) per Ristorante ID $id_ristorante_sessione: " . pg_last_error($conn));
            throw new Exception("Errore durante il recupero dei dettagli degli ordini.");
        }

        $items_buffer = [];
        while ($item = pg_fetch_assoc($result_dettagli_tutti)) {
            $items_buffer[$item['id_ordine']][] = $item;
        }

        foreach ($ordini_map as $id_o => &$ordine_ref) {
            $prezzo_totale_ordine = 0;
            $items_completati = 0;
            $totale_items = 0;

            if (isset($items_buffer[$id_o])) {
                foreach ($items_buffer[$id_o] as $item_db) {
                    $ordine_ref['items'][] = [
                        'id_dettaglio_ordine' => $item_db['id_dettaglio_ordine'],
                        'nome_prodotto' => $item_db['nome_prodotto'],
                        'quantita' => (int)$item_db['quantita'],
                        'note_riga' => $item_db['note_riga'],
                        'stato_item' => $item_db['stato_item'] ?? 'pending'
                    ];
                    $prezzo_totale_ordine += (float)$item_db['prezzo_unitario_registrato'] * (int)$item_db['quantita'];
                    if (($item_db['stato_item'] ?? 'pending') === 'ready' || ($item_db['stato_item'] ?? 'pending') === 'completed') {
                        $items_completati++;
                    }
                    $totale_items++;
                }
            }
            $ordine_ref['prezzo_totale_calcolato'] = number_format($prezzo_totale_ordine, 2, '.', '');
            $ordine_ref['percentuale_completamento'] = ($totale_items > 0) ? round(($items_completati / $totale_items) * 100) : 0;
        }
        unset($ordine_ref); 
    }
    
    $ordini_array_final = array_values($ordini_map); 

    if (ob_get_level() > 0) ob_clean(); 
    echo json_encode(['success' => true, 'ordini' => $ordini_array_final]);
    $json_output_sent = true;

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    error_log("Exception in get_ordini.php (Ristorante: $id_ristorante_sessione): " . $errorMessage . " | Trace: " . $e->getTraceAsString()); 
    if (ob_get_level() > 0) ob_clean(); 
    echo json_encode(['success' => false, 'message' => "Si è verificato un errore durante il caricamento degli ordini. Riprova più tardi."]);
    $json_output_sent = true;
} finally {
    if ($conn) {
        pg_close($conn);
    }
    if ($json_output_sent && ob_get_status() && ob_get_level() > 0) { 
        ob_end_flush(); 
    } else if (ob_get_status() && ob_get_level() > 0) {
        ob_end_clean(); 
    }
}
?>