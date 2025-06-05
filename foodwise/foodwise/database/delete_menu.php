<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

include '../database/connection.php';
$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_menu') {
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
        exit;
    }

    if (!isset($_POST['id'], $_POST['id_ristorante'])) {
        echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
        pg_close($conn);
        exit;
    }

    $idPortata = pg_escape_string($conn, $_POST['id']);

    // Elimina la relazione dal menu_ristorante
    $query = "DELETE FROM prodotti WHERE id_portata = $1";
    $params = [$idPortata];

    $result = pg_query_params($conn, $query, $params);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Portata eliminata dal menu con successo!']);
    } else {
        $errorMessage = 'Errore durante l\'eliminazione: ' . pg_last_error($conn);
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>
