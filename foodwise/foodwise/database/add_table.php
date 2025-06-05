<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

include '../database/connection.php';

$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Impossibile connettersi al database']);
        exit;
    }

    if ($_POST['action'] === 'add_table') {
        if (!isset($_POST['numero_tavolo'], $_POST['clienti'], $_POST['id_ristorante'])) {
            echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
            pg_close($conn);
            exit;
        }

        $numero_tavolo = pg_escape_string($conn, $_POST['numero_tavolo']);
        $clienti = intval($_POST['clienti']);
        $id_ristorante = pg_escape_string($conn, $_POST['id_ristorante']);

        if (strlen($numero_tavolo) > 500) {
            echo json_encode(['success' => false, 'message' => 'Il nome del tavolo non può superare i 500 caratteri']);
            pg_close($conn);
            exit;
        }

        if (is_numeric($numero_tavolo)) {
            $tableNumber = intval($numero_tavolo);
            if ($tableNumber > 500) {
                echo json_encode(['success' => false, 'message' => 'Il numero del tavolo non può essere maggiore di 500']);
                pg_close($conn);
                exit;
            }
        }

        if ($clienti < 1) {
            echo json_encode(['success' => false, 'message' => 'La capacità deve essere un numero positivo']);
            pg_close($conn);
            exit;
        }

        $checkQuery = "SELECT COUNT(*) FROM tavoli WHERE numero_tavolo = $1 AND id_ristorante = $2 AND attivo = TRUE";
        $checkParams = [$numero_tavolo, $id_ristorante];
        $checkResult = pg_query_params($conn, $checkQuery, $checkParams);

        if ($checkResult) {
            $row = pg_fetch_assoc($checkResult);
            if ($row['count'] > 0) {
                echo json_encode(['success' => false, 'message' => 'Un tavolo con questo nome esiste già']);
                pg_close($conn);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante il controllo del tavolo esistente']);
            pg_close($conn);
            exit;
        }
        $token = rand(1, 500);
        $stato = 'Free';
        $attivo = true;

        $query = "INSERT INTO tavoli (numero_tavolo, clienti, stato, attivo, id_ristorante, token) 
                  VALUES ($1, $2, $3, $4, $5, $6) RETURNING id_tavolo";
        $params = [$numero_tavolo, $clienti, $stato, $attivo, $id_ristorante, $token];
        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            $row = pg_fetch_assoc($result);
            echo json_encode(['success' => true, 'message' => 'Tavolo aggiunto con successo!', 'id_tavolo' => $row['id_tavolo']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'inserimento: ' . pg_last_error($conn)]);
        }
    } elseif ($_POST['action'] === 'update_status') {
        if (!isset($_POST['numero_tavolo'], $_POST['stato'], $_POST['id_ristorante'], $_POST['id_tavolo'])) {
            echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
            pg_close($conn);
            exit;
        }

        $numero_tavolo = pg_escape_string($conn, $_POST['numero_tavolo']);
        $stato = pg_escape_string($conn, $_POST['stato']);
        $id_ristorante = pg_escape_string($conn, $_POST['id_ristorante']);
        $id_tavolo = pg_escape_string($conn, $_POST['id_tavolo']);

        if (!in_array($stato, ['Free', 'Occupied', 'Reserved'])) {
            echo json_encode(['success' => false, 'message' => 'Stato non valido']);
            pg_close($conn);
            exit;
        }

        pg_query($conn, "BEGIN");

        // Update table status
        $query = "UPDATE tavoli SET stato = $1 WHERE id_tavolo = $2 AND id_ristorante = $3 AND attivo = TRUE";
        $params = [$stato, $id_tavolo, $id_ristorante];
        $result = pg_query_params($conn, $query, $params);

        if (!$result || pg_affected_rows($result) == 0) {
            pg_query($conn, "ROLLBACK");
            echo json_encode(['success' => false, 'message' => 'Tavolo non trovato o aggiornamento non riuscito']);
            pg_close($conn);
            exit;
        }

        // Handle prenotazioni
        if ($stato === 'Reserved') {
            if (!isset($_POST['nome_prenotazione'], $_POST['numero_persone'], $_POST['ore'])) {
                pg_query($conn, "ROLLBACK");
                echo json_encode(['success' => false, 'message' => 'Dati della prenotazione mancanti']);
                pg_close($conn);
                exit;
            }

            $nome_prenotazione = pg_escape_string($conn, $_POST['nome_prenotazione']);
            $numero_persone = intval($_POST['numero_persone']);
            $ore = pg_escape_string($conn, $_POST['ore']);

            if ($numero_persone < 1) {
                pg_query($conn, "ROLLBACK");
                echo json_encode(['success' => false, 'message' => 'Il numero di persone deve essere positivo']);
                pg_close($conn);
                exit;
            }

            if (!preg_match("/^([01]\d|2[0-3]):([0-5]\d)$/", $ore)) {
                pg_query($conn, "ROLLBACK");
                echo json_encode(['success' => false, 'message' => 'Formato ora non valido (HH:MM)']);
                pg_close($conn);
                exit;
            }

            // Delete existing reservation
            $deleteQuery = "DELETE FROM prenotazioni WHERE id_tavolo = $1";
            $deleteResult = pg_query_params($conn, $deleteQuery, [$id_tavolo]);

            if (!$deleteResult) {
                pg_query($conn, "ROLLBACK");
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione della prenotazione esistente']);
                pg_close($conn);
                exit;
            }

            // Insert new reservation
            $query = "INSERT INTO prenotazioni (id_tavolo, nome_prenotazione, numero_persone, ore) 
                      VALUES ($1, $2, $3, $4) RETURNING id_prenotazione";
            $params = [$id_tavolo, $nome_prenotazione, $numero_persone, $ore];
            $result = pg_query_params($conn, $query, $params);

            if (!$result) {
                pg_query($conn, "ROLLBACK");
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiunta della prenotazione']);
                pg_close($conn);
                exit;
            }
        } else {
            // Delete any existing reservations for Free or Occupied
            $query = "DELETE FROM prenotazioni WHERE id_tavolo = $1";
            $result = pg_query_params($conn, $query, [$id_tavolo]);

            if (!$result) {
                pg_query($conn, "ROLLBACK");
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione della prenotazione']);
                pg_close($conn);
                exit;
            }
        }

        pg_query($conn, "COMMIT");
        echo json_encode(['success' => true, 'message' => 'Stato aggiornato con successo!']);
    } elseif ($_POST['action'] === 'delete_table') {
        if (!isset($_POST['numero_tavolo'], $_POST['id_ristorante'])) {
            echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
            pg_close($conn);
            exit;
        }

        $numero_tavolo = pg_escape_string($conn, $_POST['numero_tavolo']);
        $id_ristorante = pg_escape_string($conn, $_POST['id_ristorante']);

        pg_query($conn, "BEGIN");

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

        $query = "DELETE FROM prenotazioni WHERE id_tavolo = $1";
        $result = pg_query_params($conn, $query, [$id_tavolo]);

        if (!$result) {
            pg_query($conn, "ROLLBACK");
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione della prenotazione']);
            pg_close($conn);
            exit;
        }

        $query = "DELETE FROM tavoli WHERE numero_tavolo = $1 AND id_ristorante = $2 AND attivo = TRUE";
        $params = [$numero_tavolo, $id_ristorante];
        $result = pg_query_params($conn, $query, $params);

        if ($result && pg_affected_rows($result) > 0) {
            pg_query($conn, "COMMIT");
            echo json_encode(['success' => true, 'message' => 'Tavolo eliminato con successo!']);
        } else {
            pg_query($conn, "ROLLBACK");
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione del tavolo']);
        }
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>