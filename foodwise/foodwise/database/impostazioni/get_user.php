<?php
session_start();
$username = $_SESSION['login_username'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include '../../database/connection.php';
$conn = connessione();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit;
}

$query = "SELECT username, full_name, email, telefono, hired, ruolo 
          FROM personale 
          WHERE username= '$username'"; 
$result = pg_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Errore nel recupero dati: ' . pg_last_error($conn)]);
    pg_close($conn);
    exit;
}

$manager = pg_fetch_assoc($result);

if (!$manager) {
    echo json_encode(['success' => false, 'message' => 'Nessun manager trovato']);
    pg_close($conn);
    exit;
}

pg_close($conn);
echo json_encode(['success' => true, 'manager' => $manager]);
exit;
?>