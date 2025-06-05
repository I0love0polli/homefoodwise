<?php
include 'connection.php';

// Log per debug
error_log("Dati ricevuti in signup.php: " . print_r($_POST, true));

// Connessione al database
$conn = connessione();
if (!$conn) {
    echo "<h1>Errore</h1><p>Impossibile connettersi al database.</p>";
    error_log("Errore: Impossibile connettersi al database.");
    exit();
}

// Controllo metodo POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<h1>Errore</h1><p>Metodo non valido. Usa il form per registrarti.</p>";
    error_log("Errore: Metodo non valido.");
    exit();
}

// Recupera i dati dal form
$nome_ristorante = trim($_POST['nome_ristorante'] ?? '');
$iva = trim($_POST['iva'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$terms = isset($_POST['terms']);

// Log dei dati ricevuti
error_log("Dati elaborati: nome_ristorante=$nome_ristorante, iva=$iva, email=$email, telefono=$telefono, terms=" . ($terms ? 'true' : 'false'));

// Array per raccogliere gli errori
$errors = [];

// Validazione dei campi
if (empty($nome_ristorante)) {
    $errors[] = "Il nome del ristorante è obbligatorio.";
}
if (empty($iva)) {
    $errors[] = "La Partita IVA è obbligatoria.";
} elseif (!preg_match('/^[0-9]{11}$/', $iva)) {
    $errors[] = "La Partita IVA deve essere di 11 cifre.";
}
if (empty($email)) {
    $errors[] = "L'email è obbligatoria.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Inserisci un'email valida.";
}
if (empty($telefono)) {
    $errors[] = "Il numero di telefono è obbligatorio.";
} elseif (!preg_match('/^[0-9]{10}$/', $telefono)) {
    $errors[] = "Il numero di telefono deve essere di 10 cifre.";
}
if (empty($password)) {
    $errors[] = "La password è obbligatoria.";
} elseif (strlen($password) < 6) {
    $errors[] = "La password deve contenere almeno 6 caratteri.";
}
if ($password !== $confirm_password) {
    $errors[] = "Le password non coincidono.";
}
if (!$terms) {
    $errors[] = "Devi accettare i Termini di Servizio e la Privacy Policy.";
}

// Se ci sono errori, mostra gli errori direttamente nella pagina
if (!empty($errors)) {
    error_log("Errori di validazione: " . implode(', ', $errors));
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Errore di Registrazione</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #090c0a;
                color: #fff;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .error-container {
                background-color: #1e1e1e;
                padding: 20px;
                border-radius: 8px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            }
            h1 {
                color: #ff4d4d;
            }
            ul {
                list-style-type: none;
                padding: 0;
                color: #ff4d4d;
            }
            li {
                margin-bottom: 10px;
            }
            a {
                color: #00FF7F;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Errore di Registrazione</h1>
            <p>Si sono verificati i seguenti errori:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <p><a href="../signup">Torna al form di registrazione</a></p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Controllo se l'email o la partita IVA sono già in uso
$query_email = "SELECT email FROM ristoranti WHERE email = $1";
$result_email = pg_query_params($conn, $query_email, [$email]);
if (pg_num_rows($result_email) > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Errore di Registrazione</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #090c0a;
                color: #fff;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .error-container {
                background-color: #1e1e1e;
                padding: 20px;
                border-radius: 8px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            }
            h1 {
                color: #ff4d4d;
            }
            p {
                color: #ff4d4d;
            }
            a {
                color: #00FF7F;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Errore di Registrazione</h1>
            <p>L'email è già in uso.</p>
            <p><a href="../signup">Torna al form di registrazione</a></p>
        </div>
    </body>
    </html>
    <?php
    error_log("Errore: Email già in uso: $email");
    exit();
}

$query_iva = "SELECT iva FROM ristoranti WHERE iva = $1";
$result_iva = pg_query_params($conn, $query_iva, [$iva]);
if (pg_num_rows($result_iva) > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Errore di Registrazione</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #090c0a;
                color: #fff;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .error-container {
                background-color: #1e1e1e;
                padding: 20px;
                border-radius: 8px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            }
            h1 {
                color: #ff4d4d;
            }
            p {
                color: #ff4d4d;
            }
            a {
                color: #00FF7F;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Errore di Registrazione</h1>
            <p>La Partita IVA è già registrata.</p>
            <p><a href="../signup">Torna al form di registrazione</a></p>
        </div>
    </body>
    </html>
    <?php
    error_log("Errore: Partita IVA già registrata: $iva");
    exit();
}

// URL immagine predefinita
$supabaseUrl = 'https://jfshzxaoiazolfzuzism.supabase.co';
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Impmc2h6eGFvaWF6b2xmenV6aXNtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Mzg5MzEzNjMsImV4cCI6MjA1NDUwNzM2M30.B7WRMTbGy9cM9IPbRq6hU97UsR3Zk2giIqHNTwgK3uE';
$bucket = 'user_profilePIC';
$url_img = "$supabaseUrl/storage/v1/object/public/$bucket/npp.jpg";

// Hash della password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    // Query di inserimento
    $query = "INSERT INTO ristoranti (id_ristorante, iva, email, password, telefono, url_img) 
              VALUES ($1, $2, $3, $4, $5, $6)";
    $result = pg_query_params($conn, $query, [
        $nome_ristorante,
        $iva,
        $email,
        $hashed_password,
        $telefono,
        $url_img
    ]);

    if ($result) {
        ?>
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Registrazione Completata</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #090c0a;
                    color: #fff;
                    padding: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                }
                .success-container {
                    background-color: #1e1e1e;
                    padding: 20px;
                    border-radius: 8px;
                    max-width: 600px;
                    width: 100%;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                }
                h1 {
                    color: #00FF7F;
                }
                a {
                    color: #00FF7F;
                    text-decoration: none;
                }
                a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class="success-container">
                <h1>Registrazione Completata</h1>
                <p>Registrazione completata con successo! Puoi ora effettuare il login.</p>
                <p><a href="../restaurant/">Vai al login</a></p>
            </div>
        </body>
        </html>
        <?php
        error_log("Registrazione completata con successo per: $email");
        exit();
    } else {
        $error = pg_last_error($conn);
        error_log("Errore query: $error");
        throw new Exception("Errore durante l'esecuzione della query: $error");
    }
} catch (Exception $e) {
    error_log("Errore registrazione: " . $e->getMessage());
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Errore di Registrazione</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #090c0a;
                color: #fff;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .error-container {
                background-color: #1e1e1e;
                padding: 20px;
                border-radius: 8px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            }
            h1 {
                color: #ff4d4d;
            }
            p {
                color: #ff4d4d;
            }
            a {
                color: #00FF7F;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Errore di Registrazione</h1>
            <p>Errore durante la registrazione: <?php echo htmlspecialchars($e->getMessage()); ?></p>
            <p><a href="../signup">Torna al form di registrazione</a></p>
        </div>
    </body>
    </html>
    <?php
    exit();
} finally {
    pg_close($conn);
}
?>