<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

include '../database/connection.php';

$conn = connessione();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Impossibile connettersi al database']);
    exit;
}

$statoFilter = isset($_GET['stato']) ? pg_escape_string($conn, $_GET['stato']) : null;
$ristorante = $_SESSION['login_restaurant'];

$query = "SELECT id_tavolo, numero_tavolo, clienti, stato FROM tavoli WHERE id_ristorante = $1 AND attivo = TRUE";
$params = [$ristorante];

if ($statoFilter) {
    $query .= " AND stato = $2";
    $params[] = $statoFilter;
}

$result = pg_query_params($conn, $query, $params);

if ($result) {
    $tables = [];
    while ($row = pg_fetch_assoc($result)) {
        $tables[] = [
            'id_tavolo' => $row['id_tavolo'],
            'numero_tavolo' => $row['numero_tavolo'],
            'clienti' => intval($row['clienti']),
            'stato' => $row['stato']
        ];
    }
    echo json_encode(['success' => true, 'tables' => $tables]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore durante il recupero dei tavoli: ' . pg_last_error($conn)]);
}

pg_close($conn);
exit;
?>