<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

include '../database/connection.php';
$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
        exit;
    }

    // === AGGIORNAMENTO COMPLETO PORTATA ===
    if ($_POST['action'] === 'update_menu') {
        if (!isset($_POST['id'], $_POST['nome'], $_POST['descrizione'], $_POST['prezzo'], $_POST['category'])) {
            echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
            pg_close($conn);
            exit;
        }

        $id = pg_escape_string($conn, $_POST['id']);
        $nome = pg_escape_string($conn, $_POST['nome']);
        $descrizione = pg_escape_string($conn, $_POST['descrizione']);
        $prezzo = floatval($_POST['prezzo']);

        if (!is_numeric($prezzo) || $prezzo < 0) {
            echo json_encode(['success' => false, 'message' => 'Prezzo non valido']);
            pg_close($conn);
            exit;
        }

        $allergeniArray = [];
        if (isset($_POST['allergeni'])) {
            $allergeniArray = json_decode($_POST['allergeni'], true);
            if (!is_array($allergeniArray)) {
                $allergeniArray = array_filter(array_map('trim', explode(',', $_POST['allergeni'])));
            }
        }
        $allergeni = json_encode($allergeniArray);

        // Salva la categoria come testo semplice (stringa)
        $categoria = pg_escape_string($conn, $_POST['category']);

        $urlImg = isset($_POST['url_img']) && !empty($_POST['url_img']) ? pg_escape_string($conn, $_POST['url_img']) : null;
        $disponibile = isset($_POST['disponibile']) ? pg_escape_string($conn, $_POST['disponibile']) : 'available';

        $query = "UPDATE prodotti 
                  SET nome = $1, descrizione = $2, prezzo = $3, allergeni = $4::jsonb, categoria = $5, url_img = $6, disponibile = $7 
                  WHERE id_portata = $8";
        $params = [
            $nome, $descrizione, $prezzo, $allergeni, $categoria, $urlImg, $disponibile, $id
        ];

        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Portata aggiornata con successo!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento: ' . pg_last_error($conn)]);
        }

        pg_close($conn);
        exit;
    }

    // === AGGIORNAMENTO DISPONIBILITÀ ===
    if ($_POST['action'] === 'update_availability') {
        if (!isset($_POST['id'], $_POST['disponibile'])) {
            echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
            pg_close($conn);
            exit;
        }

        $id = pg_escape_string($conn, $_POST['id']);
        $disponibile = pg_escape_string($conn, $_POST['disponibile']);

        if (!in_array($disponibile, ['available', 'unavailable'])) {
            echo json_encode(['success' => false, 'message' => 'Valore disponibilità non valido']);
            pg_close($conn);
            exit;
        }

        $query = "UPDATE prodotti SET disponibile = $1 WHERE id_portata = $2";
        $params = [$disponibile, $id];

        $result = pg_query_params($conn, $query, $params);

        if ($result && pg_affected_rows($result) > 0) {
            echo json_encode(['success' => true, 'message' => 'Stato aggiornato con successo!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore o portata non trovata']);
        }

        pg_close($conn);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>