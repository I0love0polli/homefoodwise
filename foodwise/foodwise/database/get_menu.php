<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
session_start();

header('Content-Type: application/json');
include '../database/connection.php';
$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
        exit;
    }

    // ID ristorante da recuperare, hardcoded come "Arlecchino" per ora
    $idRistorante = $_SESSION['login_restaurant'];

    // Recupera le portate del ristorante tramite join tra prodotti e menu_ristorante
    $query = "
        SELECT p.*
        FROM prodotti p
        JOIN menu_ristorante mr ON p.id_portata = mr.id_portata
        WHERE mr.id_ristorante = $1
    ";

    $result = pg_query_params($conn, $query, [$idRistorante]);

    if ($result) {
        $menuItems = [];
        while ($row = pg_fetch_assoc($result)) {
            $row['allergeni'] = json_decode($row['allergeni'], true);
            $menuItems[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $menuItems]);
    } else {
        $errorMessage = 'Errore durante il recupero dei dati: ' . pg_last_error($conn);
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>
