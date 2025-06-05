<?php
session_start();
ob_start();
header('Content-Type: application/json');

include '../database/connection.php';

$ristorante = isset($_GET['ristorante']) ? htmlspecialchars($_GET['ristorante']) : null;

if (!$ristorante) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Ristorante non specificato']);
    exit;
}

$conn = connessione();
if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit;
}

try {
    $query = "SELECT p.id_portata, p.nome, p.prezzo, p.descrizione, p.url_img, p.allergeni, p.disponibile, p.categoria
              FROM prodotti p
              INNER JOIN menu_ristorante mr ON p.id_portata = mr.id_portata
              WHERE LOWER(mr.id_ristorante) = LOWER($1)";
    $result = pg_query_params($conn, $query, [strtolower($ristorante)]);
    
    if (!$result) {
        error_log('Errore query: ' . pg_last_error($conn));
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Errore nella query al database']);
        pg_close($conn);
        exit;
    }

    $piatti = [];
    while ($row = pg_fetch_assoc($result)) {
        $piatti[] = [
            'id_portata' => $row['id_portata'],
            'nome' => $row['nome'],
            'prezzo' => floatval($row['prezzo']),
            'descrizione' => $row['descrizione'],
            'url_img' => $row['url_img'] ?: null,
            'allergeni' => json_decode($row['allergeni'] ?: '[]', true),
            'disponibile' => $row['disponibile'],
            'categoria' => $row['categoria'] ?: ''
        ];
    }

    ob_end_clean();
    echo json_encode(['success' => true, 'data' => $piatti]);
} catch (Exception $e) {
    error_log('Eccezione: ' . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Errore server: ' . $e->getMessage()]);
}

pg_close($conn);
?>