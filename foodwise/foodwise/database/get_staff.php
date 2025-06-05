<?php
// Abilita il debug (da rimuovere in produzione)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$ristorante = $_SESSION['login_restaurant'];

// Imposta il tipo di contenuto come JSON (prima di qualsiasi output)
header('Content-Type: application/json');

include '../database/connection.php'; // Verifica che il percorso sia corretto

$conn = connessione();


if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit;
}

// Query per ottenere lo staff
$query = "SELECT  username, full_name, email, telefono, hired, ruolo FROM personale where id_ristorante='$ristorante'";
$result = pg_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Errore nel recupero dati: ' . pg_last_error($conn)]);
    pg_close($conn);
    exit;
}

// Converte i risultati in un array associativo
$staff = [];
while ($row = pg_fetch_assoc($result)) {
    $staff[] = $row;
}

pg_close($conn);

// Stampa solo JSON senza spazi extra
echo json_encode(['success' => true, 'staff' => $staff], JSON_UNESCAPED_UNICODE);
exit;