<?php
session_start();
ob_start();
header('Content-Type: application/json');

include '../database/connection.php';

$input = json_decode(file_get_contents('php://input'), true);
error_log('Dati ricevuti: ' . print_r($input, true));

if (!isset($input['ristorante']) || !isset($input['tableId']) || !isset($input['tableToken']) || 
    !isset($input['prodotti']) || !is_array($input['prodotti']) || !isset($input['prezzoTotale'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Dati mancanti o non validi']);
    exit;
}

$ristorante = htmlspecialchars($input['ristorante']);
$tableId = (int)$input['tableId'];
$tableToken = htmlspecialchars($input['tableToken']);
$prodotti = $input['prodotti'];
$prezzoTotale = (float)$input['prezzoTotale'];

$conn = connessione();
if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit;
}

try {
    // Verifica il tableToken
    $query = "SELECT token FROM tavoli WHERE id_tavolo = $1 AND id_ristorante = $2";
    $result = pg_query_params($conn, $query, [$tableId, strtolower($ristorante)]);
    if (!$result || pg_num_rows($result) === 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Tavolo non trovato']);
        pg_close($conn);
        exit;
    }
    $row = pg_fetch_assoc($result);
    if ($row['token'] !== $tableToken) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Token tavolo non valido']);
        pg_close($conn);
        exit;
    }

    // Inizia una transazione
    pg_query($conn, 'BEGIN');

    // Inserisci l'ordine
    $query = "INSERT INTO ordini (id_ristorante, id_tavolo, pagato, data_ordine, stato) 
              VALUES ($1, $2, FALSE, NOW(), 'pending') RETURNING id_ordine";
    $result = pg_query_params($conn, $query, [strtolower($ristorante), $tableId]);
    if (!$result || pg_num_rows($result) === 0) {
        error_log('Errore inserimento ordine: ' . pg_last_error($conn));
        pg_query($conn, 'ROLLBACK');
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Errore nell\'inserimento dell\'ordine']);
        pg_close($conn);
        exit;
    }

    $id_ordine = pg_fetch_result($result, 0, 'id_ordine');

    // Inserisci i dettagli dell'ordine
    foreach ($prodotti as $prodotto) {
        if (!isset($prodotto['id_portata']) || !isset($prodotto['quantita']) || !isset($prodotto['prezzo'])) {
            pg_query($conn, 'ROLLBACK');
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Dati prodotto non validi']);
            pg_close($conn);
            exit;
        }

        $id_portata = (int)$prodotto['id_portata'];
        $quantita = (int)$prodotto['quantita'];
        $prezzo = (float)$prodotto['prezzo'];

        // Verifica che il prodotto esista e sia associato al ristorante
        $query = "SELECT id_portata FROM menu_ristorante WHERE id_ristorante = $1 AND id_portata = $2";
        $result = pg_query_params($conn, $query, [strtolower($ristorante), $id_portata]);
        if (!$result || pg_num_rows($result) === 0) {
            pg_query($conn, 'ROLLBACK');
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Prodotto non valido per il ristorante']);
            pg_close($conn);
            exit;
        }

        
        // Inserisci il dettaglio
        $query = "INSERT INTO dettagli_ordine (id_ordine, id_portata, quantita, prezzo_unitario_registrato, stato_item) 
                  VALUES ($1, $2, $3, $4, 'pending')";
        $result = pg_query_params($conn, $query, [$id_ordine, $id_portata, $quantita, $prezzo]);
        if (!$result) {
            error_log('Errore inserimento dettaglio: ' . pg_last_error($conn));
            pg_query($conn, 'ROLLBACK');
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Errore nell\'inserimento dei dettagli']);
            pg_close($conn);
            exit;
        }
    }

    // Conferma la transazione
    pg_query($conn, 'COMMIT');

    // Pulisci il carrello
    unset($_SESSION['cart']);

    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Ordine inserito con successo']);
} catch (Exception $e) {
    error_log('Eccezione: ' . $e->getMessage());
    pg_query($conn, 'ROLLBACK');
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore server: ' . $e->getMessage()]);
}

pg_close($conn);
?>