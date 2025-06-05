<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

include '../database/connection.php';

// Connessione al database
$conn = connessione();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Impossibile connettersi al database']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_reservation') {
        // Verifica che tutti i campi obbligatori siano presenti
        if (!isset($_POST['id_tavolo'], $_POST['nome_prenotazione'], $_POST['numero_persone'], $_POST['ore'], $_POST['id_ristorante'])) {
            echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
            pg_close($conn);
            exit;
        }

        $id_tavolo = pg_escape_string($conn, $_POST['id_tavolo']);
        $nome_prenotazione = pg_escape_string($conn, $_POST['nome_prenotazione']);
        $numero_persone = intval($_POST['numero_persone']);
        $ore = pg_escape_string($conn, $_POST['ore']);
        $id_ristorante = pg_escape_string($conn, $_POST['id_ristorante']);

        // Validazione numero persone
        if ($numero_persone < 1) {
            echo json_encode(['success' => false, 'message' => 'Il numero di persone deve essere positivo']);
            pg_close($conn);
            exit;
        }

        // Validazione ora
        if (!preg_match("/^([01]\d|2[0-3]):([0-5]\d)$/", $ore)) {
            echo json_encode(['success' => false, 'message' => 'Formato ora non valido (HH:MM)']);
            pg_close($conn);
            exit;
        }

        // Query di inserimento
        $query = "INSERT INTO prenotazioni (id_tavolo, nome_prenotazione, numero_persone, ore) 
                  VALUES ($1, $2, $3, $4) RETURNING id_prenotazione";
        $params = [$id_tavolo, $nome_prenotazione, $numero_persone, $ore];

        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            $row = pg_fetch_assoc($result);
            echo json_encode(['success' => true, 'message' => 'Prenotazione aggiunta con successo!', 'id_prenotazione' => $row['id_prenotazione']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiunta della prenotazione: ' . pg_last_error($conn)]);
        }
    } elseif ($_POST['action'] === 'delete_reservation') {
        // Verifica che id_tavolo sia presente
        if (!isset($_POST['id_tavolo'], $_POST['id_ristorante'])) {
            echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
            pg_close($conn);
            exit;
        }

        $id_tavolo = pg_escape_string($conn, $_POST['id_tavolo']);
        $id_ristorante = pg_escape_string($conn, $_POST['id_ristorante']);

        // Query di eliminazione
        $query = "DELETE FROM prenotazioni WHERE id_tavolo = $1";
        $params = [$id_tavolo];

        $result = pg_query_params($conn, $query, $params);

        if ($result && pg_affected_rows($result) > 0) {
            echo json_encode(['success' => true, 'message' => 'Prenotazione eliminata con successo!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nessuna prenotazione trovata per il tavolo specificato']);
        }
    }

    pg_close($conn);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_reservations') {
    // Recupera prenotazioni per il ristorante
    $id_ristorante = pg_escape_string($conn, $_GET['id_ristorante']);
    $query = "SELECT p.id_prenotazione, p.id_tavolo, p.nome_prenotazione, p.numero_persone, p.ore, t.numero_tavolo 
              FROM prenotazioni p 
              JOIN tavoli t ON p.id_tavolo = t.id_tavolo 
              WHERE t.id_ristorante = $1 AND t.attivo = TRUE";
    $params = [$id_ristorante];

    $result = pg_query_params($conn, $query, $params);

    if ($result) {
        $reservations = [];
        while ($row = pg_fetch_assoc($result)) {
            $reservations[] = [
                'id_prenotazione' => $row['id_prenotazione'],
                'id_tavolo' => $row['id_tavolo'],
                'numero_tavolo' => $row['numero_tavolo'],
                'nome_prenotazione' => $row['nome_prenotazione'],
                'numero_persone' => intval($row['numero_persone']),
                'ore' => $row['ore']
            ];
        }
        echo json_encode(['success' => true, 'reservations' => $reservations]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore durante il recupero delle prenotazioni: ' . pg_last_error($conn)]);
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>