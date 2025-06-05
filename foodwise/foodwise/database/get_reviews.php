<?php
// Start session only if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start(); // Inizia il buffering dell'output
header('Content-Type: application/json');

// Percorso corretto per connection.php se get_reviews.php è in foodwise/database/
include './connection.php'; 

$conn = connessione();

if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database.']);
    exit;
}

$id_ristorante = $_GET['ristorante'] ?? null;

if (empty($id_ristorante)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'ID Ristorante non fornito.']);
    pg_close($conn);
    exit;
}

$recensioni = [];
try {
    $query = "SELECT nome, descrizione, stelle, data_recensione FROM recensioni WHERE LOWER(id_ristorante) = LOWER($1) ORDER BY data_recensione DESC";
    $result = pg_query_params($conn, $query, [strtolower($id_ristorante)]);

    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $recensioni[] = $row;
        }
        ob_end_clean();
        echo json_encode(['success' => true, 'recensioni' => $recensioni]);
    } else {
        error_log("get_reviews.php: Errore query per ristorante '$id_ristorante': " . pg_last_error($conn));
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Errore nel recupero delle recensioni.']);
    }
} catch (Exception $e) {
    error_log("get_reviews.php: Eccezione per ristorante '$id_ristorante': " . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore del server.']);
}

pg_close($conn);
exit;
?>
