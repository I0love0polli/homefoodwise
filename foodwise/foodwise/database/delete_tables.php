<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

include '../database/connection.php';

$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_table') {
    if (!$conn) {
        $conn = connessione();
        if (!$conn) {
            $errorMessage = 'Impossibile connettersi al database: ' . pg_last_error();
            error_log("Connessione fallita: $errorMessage");
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit;
        }
    }

    if (!isset($_POST['numero_tavolo'], $_POST['id_ristorante'])) {
        $errorMessage = 'Campi obbligatori mancanti: ' . (isset($_POST['numero_tavolo']) ? '' : 'numero_tavolo') . (isset($_POST['id_ristorante']) ? '' : ' id_ristorante');
        error_log("Campi mancanti: $errorMessage");
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        pg_close($conn);
        exit;
    }

    $numero_tavolo = pg_escape_string($conn, $_POST['numero_tavolo']);
    $id_ristorante = pg_escape_string($conn, $_POST['id_ristorante']);

    error_log("Richiesta di eliminazione ricevuta - numero_tavolo: $numero_tavolo, id_ristorante: $id_ristorante");

    // Inizia una transazione
    pg_query($conn, "BEGIN");

    // Recupera id_tavolo
    $query = "SELECT id_tavolo FROM tavoli WHERE numero_tavolo = $1 AND id_ristorante = $2 AND attivo = TRUE";
    $params = [$numero_tavolo, $id_ristorante];
    $result = pg_query_params($conn, $query, $params);

    if (!$result || pg_num_rows($result) == 0) {
        pg_query($conn, "ROLLBACK");
        echo json_encode(['success' => false, 'message' => 'Tavolo non trovato']);
        pg_close($conn);
        exit;
    }

    $row = pg_fetch_assoc($result);
    $id_tavolo = $row['id_tavolo'];

    // Elimina eventuali prenotazioni associate
    $query = "DELETE FROM prenotazioni WHERE id_tavolo = $1";
    $params = [$id_tavolo];
    $result = pg_query_params($conn, $query, $params);

    if (!$result) {
        pg_query($conn, "ROLLBACK");
        $errorMessage = 'Errore durante l\'eliminazione della prenotazione: ' . pg_last_error($conn);
        error_log("Query prenotazioni fallita: $errorMessage");
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        pg_close($conn);
        exit;
    }

    // Elimina il tavolo
    $query = "DELETE FROM tavoli WHERE numero_tavolo = $1 AND id_ristorante = $2 AND attivo = TRUE";
    $params = [$numero_tavolo, $id_ristorante];
    $result = pg_query_params($conn, $query, $params);

    if ($result) {
        $affectedRows = pg_affected_rows($result);
        error_log("Query tavolo eseguita - righe interessate: $affectedRows");
        if ($affectedRows > 0) {
            pg_query($conn, "COMMIT");
            echo json_encode(['success' => true, 'message' => 'Tavolo eliminato con successo!']);
        } else {
            pg_query($conn, "ROLLBACK");
            echo json_encode(['success' => false, 'message' => 'Nessun tavolo trovato con i criteri specificati']);
        }
    } else {
        pg_query($conn, "ROLLBACK");
        $errorMessage = 'Errore durante l\'eliminazione: ' . pg_last_error($conn);
        error_log("Query tavolo fallita: $errorMessage");
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>