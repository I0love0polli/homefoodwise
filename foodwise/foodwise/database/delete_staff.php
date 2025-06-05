<?php
// Abilita il debug (da rimuovere in produzione)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Imposta il tipo di contenuto come JSON (prima di qualsiasi output)
header('Content-Type: application/json');

include 'connection.php'; // Verifica che il percorso sia corretto

$conn = connessione();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
    exit;
}

// Recupera l'username dalla richiesta
$staffUsername = isset($_POST['staffUsername']) ? $_POST['staffUsername'] : null;

if (!$staffUsername) {
    echo json_encode(['success' => false, 'message' => 'Username non fornito']);
    exit;
}

// Query per eliminare il membro dello staff usando username
// Assicurati che 'username' sia unico nella tabella personale
$query = "DELETE FROM personale WHERE username = $1";
$result = pg_query_params($conn, $query, [$staffUsername]);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione: ' . pg_last_error($conn)]);
    pg_close($conn);
    exit;
}

// Verifica se è stata eliminata almeno una riga
if (pg_affected_rows($result) > 0) {
    echo json_encode(['success' => true, 'message' => 'Membro dello staff eliminato con successo']);
} else {
    echo json_encode(['success' => false, 'message' => 'Nessun membro dello staff trovato con questo username']);
}

pg_close($conn);
exit;