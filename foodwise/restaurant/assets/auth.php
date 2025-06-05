<?php
session_start();
// Avvia il buffering dell'output per evitare problemi con header()
ob_start();

// Includi il file globale (assumiamo che session_start() sia qui)
include '../../database/connection.php';

// Scrivi un log per confermare che il file viene eseguito
error_log("auth.php: Script avviato");

// Connessione al databasese
$conn = connessione();

if (!$conn) {
    error_log("auth.php: Errore connessione al database");
    $_SESSION['error_message'] = "Errore di connessione al database.";
    header("Location: ../index.php");
    ob_end_flush();
    exit();
}

// Controllo metodo POST
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['submit_login'])) {
    error_log("auth.php: Richiesta non valida");
    $_SESSION['error_message'] = "Richiesta non valida.";
    header("Location: ../index.php");
    ob_end_flush();
    exit();
}

// Dati dal form
$ristorante_login = trim($_POST['ristorante_login'] ?? '');
$password_login = trim($_POST['password_login'] ?? '');

// Validazione input
if (empty($ristorante_login) || empty($password_login)) {
    error_log("auth.php: Campi vuoti");
    $_SESSION['error_message'] = "Compila tutti i campi.";
    header("Location: ../index.php");
    ob_end_flush();
    exit();
}

// Verifica ristorante
$query = "SELECT * FROM ristoranti WHERE id_ristorante = $1";
$result = pg_query_params($conn, $query, [$ristorante_login]);

if ($result === false) {
    error_log("auth.php: Errore nella query SQL");
    $_SESSION['error_message'] = "Errore nella query al database.";
    header("Location: ../index.php");
    ob_end_flush();
    exit();
}

$user = pg_fetch_assoc($result);

if ($user && password_verify($password_login, $user['password'])) {
    error_log("auth.php: Login riuscito per id_ristorante: " . $user['id_ristorante']);
    $_SESSION['login_restaurant'] = $user['id_ristorante'];
    
    // Reindirizzamento alla dashboard con percorso assoluto
    header("Location: ../../dashboard/index.php");
    ob_end_flush();
    exit();
} else {
    error_log("auth.php: Credenziali errate per id_ristorante: " . $ristorante_login);
    $_SESSION['error_message'] = "ID ristorante o password errati. Riprova.";
    header("Location: ../index.php");
    ob_end_flush();
    exit();
}

// Chiusura connessione
pg_close($conn);
ob_end_flush();
?>