<?php
session_start();
ini_set('display_errors', 0); // Disable error display
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', __DIR__ . '/php_errors.log');
error_reporting(E_ALL);

// Start output buffering
ob_start();

header('Content-Type: application/json');

// Initialize log file
$logFile = __DIR__ . '/set_user.log';
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

logMessage('set_user.php started');

// Check if user is logged in
if (!isset($_SESSION['login_username']) || empty($_SESSION['login_username'])) {
    $error = 'Utente non autorizzato';
    logMessage($error);
    echo json_encode(['success' => false, 'message' => $error]);
    ob_end_flush();
    exit;
}
$currentUsername = $_SESSION['login_username'];
logMessage("Current Username: $currentUsername");

// Database connection
include '../../database/connection.php';
$conn = connessione();
if (!$conn) {
    $error = 'Errore di connessione al database';
    logMessage($error);
    echo json_encode(['success' => false, 'message' => $error]);
    ob_end_flush();
    exit;
}
logMessage('Database connected successfully');

// Determine which section is being updated
$section = $_POST['section'] ?? '';
logMessage("Section: $section");

try {
    if ($section === 'userSettings') {
        // Collect form data
        $fullName = $_POST['fullName'] ?? '';
        $newUsername = $_POST['username'] ?? '';
        $phone = $_POST['userPhone'] ?? '';
        $email = $_POST['userEmail'] ?? '';

        logMessage('Form data received (userSettings): ' . json_encode([
            'fullName' => $fullName,
            'username' => $newUsername,
            'userPhone' => $phone,
            'userEmail' => $email
        ]));

        // Validate required fields
        if (empty($fullName)) {
            $error = 'Il nome completo è obbligatorio';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }
        if (empty($newUsername)) {
            $error = 'Lo username è obbligatorio';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Formato email non valido';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }
        // Validate phone number format (basic regex for international numbers)
        if (!empty($phone) && !preg_match('/^\+?[0-9\s\-]{7,15}$/', $phone)) {
            $error = 'Formato numero di telefono non valido';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        // Check if username is changed and unique
        if ($newUsername !== $currentUsername) {
            $checkQuery = "SELECT COUNT(*) FROM personale WHERE username = $1";
            $checkResult = pg_query_params($conn, $checkQuery, [$newUsername]);
            if (!$checkResult) {
                $error = 'Errore nella verifica dello username: ' . pg_last_error($conn);
                logMessage($error);
                echo json_encode(['success' => false, 'message' => $error]);
                pg_close($conn);
                ob_end_flush();
                exit;
            }
            $row = pg_fetch_row($checkResult);
            if ($row[0] > 0) {
                $error = 'Username già in uso';
                logMessage($error);
                echo json_encode(['success' => false, 'message' => $error]);
                pg_close($conn);
                ob_end_flush();
                exit;
            }
        }

        // Prepare and execute the UPDATE query
        $updates = [];
        $params = [];
        $paramIndex = 1;

        $updates[] = "full_name = $" . $paramIndex;
        $params[] = $fullName;
        $paramIndex++;

        $updates[] = "telefono = $" . $paramIndex;
        $params[] = $phone;
        $paramIndex++;

        $updates[] = "email = $" . $paramIndex;
        $params[] = $email;
        $paramIndex++;

        if ($newUsername !== $currentUsername) {
            $updates[] = "username = $" . $paramIndex;
            $params[] = $newUsername;
            $paramIndex++;
        }

        $query = "UPDATE personale SET " . implode(', ', $updates) . " WHERE username = $" . $paramIndex;
        $params[] = $currentUsername;

        logMessage('Query: ' . $query);
        logMessage('Parameters: ' . json_encode($params));

        $result = pg_query_params($conn, $query, $params);
        if (!$result) {
            $error = 'Errore nell\'aggiornamento dei dati: ' . pg_last_error($conn);
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        // Update session if username changed
        if ($newUsername !== $currentUsername) {
            $_SESSION['login_username'] = $newUsername;
            logMessage("Updated session login_username to: $newUsername");
        }

        logMessage('Update successful');
        echo json_encode(['success' => true, 'message' => 'Impostazioni utente aggiornate con successo']);
    } elseif ($section === 'userSecurity') {
    // Collect security form data
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmNewPassword = $_POST['confirmNewPassword'] ?? '';

    logMessage('Security form data received: ' . json_encode([
        'currentPassword' => '****',
        'newPassword' => '****',
        'confirmNewPassword' => '****'
    ]));

    // Validate security fields
    if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
        $error = 'Tutti i campi della password sono obbligatori';
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
        pg_close($conn);
        ob_end_flush();
        exit;
    }

    if ($newPassword !== $confirmNewPassword) {
        $error = 'Le nuove password non corrispondono';
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
        pg_close($conn);
        ob_end_flush();
        exit;
    }

    // Validate password strength (minimum 8 characters)
    if (strlen($newPassword) < 8) {
        $error = 'La nuova password deve essere lunga almeno 8 caratteri';
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
        pg_close($conn);
        ob_end_flush();
        exit;
    }

    // Verify current password
    $query = "SELECT psw FROM personale WHERE username = $1";
    $result = pg_query_params($conn, $query, [$currentUsername]);
    if (!$result) {
        $error = 'Errore nella verifica della password: ' . pg_last_error($conn);
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
        pg_close($conn);
        ob_end_flush();
        exit;
    }
    $row = pg_fetch_assoc($result);
    if (!$row || !password_verify($currentPassword, $row['psw'])) {
        $error = 'Password attuale non corretta';
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
        pg_close($conn);
        ob_end_flush();
        exit;
    }

    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $query = "UPDATE personale SET psw = $1 WHERE username = $2";
    $result = pg_query_params($conn, $query, [$hashedPassword, $currentUsername]);
    if (!$result) {
        $error = 'Errore nell\'aggiornamento della password: ' . pg_last_error($conn);
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
        pg_close($conn);
        ob_end_flush();
        exit;
    }

    logMessage('Password update successful');
    echo json_encode(['success' => true, 'message' => 'Impostazioni di sicurezza utente aggiornate con successo']);

    } else {
        $error = 'Sezione non valida';
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
    }
} catch (Exception $e) {
    $error = 'Eccezione: ' . $e->getMessage();
    logMessage($error);
    echo json_encode(['success' => false, 'message' => 'Errore del server']);
}

pg_close($conn);
logMessage('set_user.php completed');
ob_end_flush();
exit;
?>