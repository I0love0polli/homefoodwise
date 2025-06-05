<?php
// Start session only if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start(); // Inizia il buffering dell'output
header('Content-Type: application/json');

// Percorso corretto per connection.php se add_review.php è in foodwise/database/
include './connection.php'; 

// Log per debug - da rimuovere o commentare in produzione
// error_log("add_review.php: Script avviato. Dati POST: " . print_r($_POST, true));

$conn = connessione();

if (!$conn) {
    ob_end_clean(); // Pulisci il buffer prima di inviare JSON
    error_log("add_review.php: Errore di connessione al database.");
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database.']);
    exit;
}

// Recupera i dati dal form
$id_ristorante_form = $_POST['id_ristorante'] ?? null; 
$nome_form = $_POST['nome'] ?? null;
$descrizione_form = $_POST['descrizione'] ?? null;
$stelle_form = $_POST['stelle'] ?? null;

// Log dei dati ricevuti (più sicuro)
// error_log("add_review.php: Dati ricevuti - Ristorante: " . htmlspecialchars($id_ristorante_form) . ", Nome: " . htmlspecialchars($nome_form) . ", Stelle: " . htmlspecialchars($stelle_form));


// Validazione semplice dei dati
if (empty($id_ristorante_form) || empty($nome_form) || empty($descrizione_form) || !is_numeric($stelle_form) || $stelle_form < 1 || $stelle_form > 5) {
    // error_log("add_review.php: Dati mancanti o non validi.");
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Tutti i campi sono obbligatori e la valutazione deve essere tra 1 e 5.']);
    pg_close($conn);
    exit;
}

// Sanitizzazione non è strettamente necessaria qui perché usiamo query parametrizzate,
// ma pg_escape_string può essere usato per sicurezza se si costruissero query dinamicamente.
// Per i parametri, il driver si occupa della sanitizzazione.

try {
    // Query di inserimento con parametri
    $query_insert = "INSERT INTO recensioni (id_ristorante, nome, descrizione, stelle, data_recensione) VALUES ($1, $2, $3, $4, NOW())";
    $result_insert = pg_query_params($conn, $query_insert, [
        strtolower($id_ristorante_form), // Converto in minuscolo per coerenza con il recupero
        $nome_form, 
        $descrizione_form, 
        (int)$stelle_form // Cast a intero
    ]);

    if ($result_insert) {
        // error_log("add_review.php: Recensione inserita con successo per ristorante " . htmlspecialchars($id_ristorante_form));
        ob_end_clean();
        echo json_encode(['success' => true, 'message' => 'Recensione inviata con successo!']);
    } else {
        // error_log("add_review.php: Errore durante l'inserimento della recensione: " . pg_last_error($conn));
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'invio della recensione. Riprova. Dettaglio DB: ' . pg_last_error($conn)]);
    }

} catch (Exception $e) {
    // error_log("add_review.php: Eccezione: " . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Si è verificato un errore imprevisto: ' . $e->getMessage()]);
}

pg_close($conn);
exit;
?>
