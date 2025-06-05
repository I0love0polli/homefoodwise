<?php
session_start();
// Avvia il buffering dell'output
ob_start();

// Includi il file di connessione
include '../../database/connection.php';

// Log per confermare l'esecuzione
error_log("auth.php: Script avviato");

// Connessione al database
$conn = connessione();

if (!$conn) {
    error_log("auth.php: Errore connessione al database");
    $_SESSION['error_message'] = "Errore di connessione al database. Riprova più tardi.";
    header("Location: ../../login/index.php?error=db_connection"); // Reindirizza
    ob_end_flush();
    exit();
}

// Controllo metodo POST
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['submit_login'])) {
    error_log("auth.php: Richiesta non valida. Metodo: " . $_SERVER["REQUEST_METHOD"] . ", submit_login: " . (isset($_POST['submit_login']) ? 'presente' : 'non presente'));
    $_SESSION['error_message'] = "Richiesta non valida.";
    header("Location: ../../login/index.php?error=invalid_request"); // Reindirizza
    ob_end_flush();
    exit();
}

// Dati dal form
$ristorante_login = trim($_POST['ristorante_login'] ?? '');
$utente_login = trim($_POST['utente_login'] ?? '');
$password_login = trim($_POST['password_login'] ?? '');

// Validazione input
if (empty($ristorante_login) || empty($utente_login) || empty($password_login)) {
    error_log("auth.php: Campi vuoti. Ristorante: '$ristorante_login', Utente: '$utente_login', Password fornita: " . (empty($password_login) ? 'NO' : 'SI'));
    $_SESSION['error_message'] = "Tutti i campi sono obbligatori. Per favore, compilali tutti.";
    // Aggiungi parametri all'URL per identificare quali campi sono vuoti, se necessario per JS lato client
    $error_params = [];
    if (empty($ristorante_login)) $error_params[] = "ristorante_empty";
    if (empty($utente_login)) $error_params[] = "utente_empty";
    if (empty($password_login)) $error_params[] = "password_empty";
    $error_query = !empty($error_params) ? "&fields=" . implode(",", $error_params) : "";

    header("Location: ../../login/index.php?error=empty_fields" . $error_query); // Reindirizza
    ob_end_flush();
    exit();
}

// Verifica ristorante
$query_ristorante = "SELECT 1 FROM personale WHERE id_ristorante = $1";
$result_rist = pg_query_params($conn, $query_ristorante, [$ristorante_login]); // Rinominata variabile per evitare conflitti

if ($result_rist === false) {
    error_log("auth.php: Errore SQL (query ristorante): " . pg_last_error($conn));
    $_SESSION['error_message'] = "Errore tecnico durante la verifica del ristorante. Riprova.";
    header("Location: ../../login/index.php?error=db_query_rist"); // Reindirizza
    ob_end_flush();
    exit();
}

if (pg_num_rows($result_rist) == 0) {
    error_log("auth.php: Ristorante non trovato per id_ristorante: " . htmlspecialchars($ristorante_login));
    $_SESSION['error_message'] = "Codice Ristorante non valido o non trovato.";
    header("Location: ../../login/index.php?error=restaurant_not_found"); // Reindirizza
    ob_end_flush();
    exit();
}

// Verifica utente
$query_utente = "SELECT * FROM personale WHERE username = $1 AND id_ristorante = $2";
$result_user = pg_query_params($conn, $query_utente, [$utente_login, $ristorante_login]); // Rinominata variabile

if ($result_user === false) {
    error_log("auth.php: Errore SQL (query utente): " . pg_last_error($conn));
    $_SESSION['error_message'] = "Errore tecnico durante la verifica dell'utente. Riprova.";
    header("Location: ../../login/index.php?error=db_query_user"); // Reindirizza
    ob_end_flush();
    exit();
}

$user = pg_fetch_assoc($result_user);

if ($user && password_verify($password_login, $user['psw'])) {
    error_log("auth.php: Login riuscito per username: " . htmlspecialchars($user['username']));
    // Rigenera l'ID di sessione per prevenire session fixation
    session_regenerate_id(true);
    
    $_SESSION['login_username'] = $user['username'];
    $_SESSION['ruolo'] = $user['ruolo'] ;
    $_SESSION['login_restaurant'] = $user['id_ristorante']; // Questo è l'ID del ristorante
    // Potresti voler salvare anche il nome del ristorante se disponibile e utile
    // Esempio: $_SESSION['restaurant_name'] = $user['nome_ristorante']; (se presente nella tabella personale o da altra query)

    // Rimuovi eventuali messaggi di errore precedenti
    unset($_SESSION['error_message']);

    header("Location: ../../dashboard/index.php"); // Reindirizza alla dashboard
    ob_end_flush();
    exit();
} else {
    error_log("auth.php: Credenziali errate per username: " . htmlspecialchars($utente_login) . ", id_ristorante: " . htmlspecialchars($ristorante_login));
    $_SESSION['error_message'] = "Username o Password non corretti. Controlla e riprova.";
    header("Location: ../../login/index.php?error=credentials"); // Reindirizza
    ob_end_flush();
    exit();
}

// Chiusura connessione
pg_close($conn);
ob_end_flush(); // Assicura che tutto l'output sia inviato
?>
