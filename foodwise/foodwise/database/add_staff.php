<?php
// Abilita gli errori per il debug (rimuovi in produzione)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Forza il tipo di contenuto come JSON

// Includi la funzione di connessione
include '../database/connection.php';

// Connessione al database
$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_staff') {
    if (!$conn) {
        $conn = connessione();
        if (!$conn) {
            $errorMessage = 'Impossibile connettersi al database: ' . pg_last_error();
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit;
        }
    }

    // Correzione: passa la connessione a pg_escape_string()
    $username = pg_escape_string($conn, $_POST['staffUsername']);
    $fullName = pg_escape_string($conn, $_POST['staffFullName']);
    $password = password_hash($_POST['staffPassword'], PASSWORD_DEFAULT); // NON usare pg_escape_string() qui
    $role = ucfirst(strtolower(pg_escape_string($conn, $_POST['staffRole']))); // Corregge il ruolo
    $phone = pg_escape_string($conn, $_POST['staffPhone']);
    $email = pg_escape_string($conn, $_POST['staffEmail']);
    $idRistorante = pg_escape_string($conn, $_POST['staffIdRistorante']);

    // Correzione per il valore NULL
    $urlImg = 'NULL'; // Se vuoi un'immagine opzionale, puoi modificare questa parte per gestire un file upload

    $query = "INSERT INTO personale (username, full_name, psw, ruolo, url_img, telefono, email, id_ristorante) 
              VALUES ('$username', '$fullName', '$password', '$role', $urlImg, '$phone', '$email', '$idRistorante')";

    $result = pg_query($conn, $query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Membro aggiunto con successo!']);
    } else {
        $errorMessage = 'Errore durante l\'inserimento: ' . pg_last_error($conn);
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>
