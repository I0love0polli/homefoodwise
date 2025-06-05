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
$dishId = $data['dishId'] ?? null;
$quantity = $data['quantity'] ?? null;
$ristorante = $data['ristorante'] ?? null;

if (!$dishId || !$ristorante || !is_numeric($quantity) || $quantity < 0) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Dati non validi o quantità negativa']);
    exit;
}

$conn = connessione();
if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit;
}

try {
    // Recupera i dettagli del piatto
    $query = "SELECT p.nome, p.prezzo, p.url_img 
              FROM prodotti p
              INNER JOIN menu_ristorante mr ON p.id_portata = mr.id_portata
              INNER JOIN ristoranti r ON mr.id_ristorante = r.id_ristorante
              WHERE p.id_portata = $1 AND LOWER(r.id_ristorante) = LOWER($2)";
    $result = pg_query_params($conn, $query, [$dishId, $ristorante]);
    
    if (!$result) {
        error_log('Errore query: ' . pg_last_error($conn));
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Errore nella query al database']);
        pg_close($conn);
        exit;
    }

    if ($row = pg_fetch_assoc($result)) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if ($quantity > 0) {
            $_SESSION['cart'][$dishId] = [
                'name' => $row['nome'],
                'price' => floatval($row['prezzo']),
                'quantity' => (int)$quantity,
                'image' => $row['url_img'] ?: 'https://placehold.co/150x150?text=No+Image'
            ];
        } else {
            unset($_SESSION['cart'][$dishId]);
        }

        ob_end_clean();
        echo json_encode(['success' => true]);
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Piatto non trovato o non associato al ristorante']);
    }
} catch (Exception $e) {
    error_log('Eccezione: ' . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore server: ' . $e->getMessage()]);
}

pg_close($conn);
exit;
?>