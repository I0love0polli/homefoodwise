<?php
session_start();
ob_start();
header('Content-Type: application/json');
include '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$ristorante = $data['ristorante'] ?? null;

if (!$ristorante) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Ristorante non specificato']);
    exit;
}

// Verifica che il ristorante esista
$conn = connessione();
if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit;
}

try {
    $query = "SELECT id_ristorante FROM ristoranti WHERE LOWER(id_ristorante) = LOWER($1)";
    $result = pg_query_params($conn, $query, [strtolower($ristorante)]);
    if (!$result || pg_num_rows($result) === 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Ristorante non trovato']);
        pg_close($conn);
        exit;
    }

    // Verifica che l'utente sia autorizzato (opzionale, in base al contesto)
    if (!isset($_SESSION['login_restaurant']) || strtolower($_SESSION['login_restaurant']) !== strtolower($ristorante)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Non autorizzato per questo ristorante']);
        pg_close($conn);
        exit;
    }

    // Svuota il carrello
    $_SESSION['cart'] = [];

    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Carrello svuotato con successo']);
} catch (Exception $e) {
    error_log('Eccezione: ' . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore server: ' . $e->getMessage()]);
}

pg_close($conn);
exit;
?>