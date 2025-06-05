<?php
// Connessione al database
include '../database/connection.php';
$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_username']) && isset($_POST['newRole'])) {
    // Acquisisci i dati inviati dal client
    $id_username = pg_escape_string($conn, $_POST['id_username']);
    $newRole = ucfirst(strtolower(pg_escape_string($conn, $_POST['newRole'])));

    // Aggiorna il ruolo nel database utilizzando l'ID primario (id_username)
    $query = "UPDATE personale SET ruolo = '$newRole' WHERE username = '$id_username'";
    $result = pg_query($conn, $query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Ruolo aggiornato con successo!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento del ruolo.']);
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Dati mancanti o richiesta non valida']);
?>
