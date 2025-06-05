<?php
session_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
error_reporting(E_ALL);

// Inizia il buffer di output
ob_start();

header('Content-Type: application/json');

// Initialize log file
$logFile = __DIR__ . '/set_ristorante.log';
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

logMessage('set_ristorante.php started');

// Check session
if (!isset($_SESSION['login_restaurant']) || empty($_SESSION['login_restaurant'])) {
    $error = 'Utente non autorizzato';
    logMessage($error);
    echo json_encode(['success' => false, 'message' => $error]);
    ob_end_flush();
    exit;
}
$current_id_ristorante = $_SESSION['login_restaurant'];
logMessage("Current Restaurant ID (name): $current_id_ristorante");

// Include database connection
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
$section = $_POST['section'] ?? 'general';
logMessage("Section: $section");

try {
    if ($section === 'security') {
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmNewPassword = $_POST['confirmNewPassword'] ?? '';

        logMessage("Dati ricevuti: currentPassword=$currentPassword, newPassword=$newPassword, restaurant_id=$current_id_ristorante");

        if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
            logMessage('Errore: Tutti i campi della password sono obbligatori');
            echo json_encode(['success' => false, 'message' => 'Tutti i campi della password sono obbligatori']);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        if ($newPassword !== $confirmNewPassword) {
            logMessage('Errore: Le nuove password non corrispondono');
            echo json_encode(['success' => false, 'message' => 'Le nuove password non corrispondono']);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        // Recupera la password
        $query = "SELECT password FROM ristoranti WHERE id_ristorante = $1";
        $result = pg_query_params($conn, $query, [$current_id_ristorante]);
        if (!$result) {
            $error = 'Errore query: ' . pg_last_error($conn);
            logMessage($error);
            echo json_encode(['success' => false, 'message' => 'Errore del database']);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        if (pg_num_rows($result) === 0) {
            logMessage('Errore: Ristorante non trovato per id_ristorante: ' . $current_id_ristorante);
            echo json_encode(['success' => false, 'message' => 'Ristorante non trovato']);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        $row = pg_fetch_assoc($result);
        $storedPassword = $row['password'];
        logMessage("Password salvata: $storedPassword");

        // Verifica la password (hashata)
        if (!password_verify($currentPassword, $storedPassword)) {
            logMessage('Errore: Password attuale non corretta (hash)');
            echo json_encode(['success' => false, 'message' => 'Password attuale non corretta']);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        // Hash della nuova password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        logMessage('Nuova password hashata generata');

        // Aggiorna la password
        $query = "UPDATE ristoranti SET password = $1 WHERE id_ristorante = $2";
        $result = pg_query_params($conn, $query, [$hashedNewPassword, $current_id_ristorante]);
        if (!$result) {
            $error = 'Errore aggiornamento password: ' . pg_last_error($conn);
            logMessage($error);
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento della password']);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        logMessage("Password aggiornata per id_ristorante: $current_id_ristorante");
        echo json_encode(['success' => true, 'message' => 'Password aggiornata con successo']);
        pg_close($conn);
        ob_end_flush();
        exit;
    }

    // Sezioni general, payments, wifi (invariate)
    $updates = [];
    $params = [];
    $paramIndex = 1;

    if ($section === 'general') {
        $restaurantName = $_POST['restaurantName'] ?? '';
        $address = $_POST['address'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $website = $_POST['website'] ?? '';
        $openingHours = $_POST['openingHours'] ?? '';

        logMessage('Form data received (general): ' . json_encode([
            'restaurantName' => $restaurantName,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'openingHours' => $openingHours
        ]));

        if (empty($restaurantName)) {
            $error = 'Il nome del ristorante è obbligatorio';
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

        if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
            $error = 'URL del sito web non valido';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        $imageUrl = null;
        $uploadDir = '../../Uploads/Ristoranti/';
        if (!is_dir($uploadDir)) {
            logMessage("Creating upload directory: $uploadDir");
            if (!mkdir($uploadDir, 0755, true)) {
                $error = 'Impossibile creare la directory di upload';
                logMessage($error);
                echo json_encode(['success' => false, 'message' => $error]);
                pg_close($conn);
                ob_end_flush();
                exit;
            }
        }

        if (!is_writable($uploadDir)) {
            $error = "Directory di upload non scrivibile: $uploadDir";
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        if (isset($_FILES['restaurantImage']) && $_FILES['restaurantImage']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['restaurantImage'];
            logMessage('Image upload detected: ' . json_encode([
                'name' => $file['name'],
                'size' => $file['size'],
                'type' => $file['type']
            ]));

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                $error = 'Tipo di immagine non valido: ' . $file['type'];
                logMessage($error);
                echo json_encode(['success' => false, 'message' => $error]);
                pg_close($conn);
                ob_end_flush();
                exit;
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Immagine troppo grande: ' . $file['size'];
                logMessage($error);
                echo json_encode(['success' => false, 'message' => $error]);
                pg_close($conn);
                ob_end_flush();
                exit;
            }

            $fileName = uniqid() . '_' . basename($file['name']);
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $imageUrl = '/foodwise/Uploads/Ristoranti/' . $fileName;
                logMessage("Image uploaded successfully: $imageUrl");
            } else {
                $error = 'Errore durante il caricamento dell\'immagine';
                logMessage($error);
                echo json_encode(['success' => false, 'message' => $error]);
                pg_close($conn);
                ob_end_flush();
                exit;
            }
        } else {
            $uploadError = $_FILES['restaurantImage']['error'] ?? 'No file';
            logMessage("No image uploaded or upload error: $uploadError");
        }

        $updates[] = "indirizzo = $" . $paramIndex;
        $params[] = $address;
        $paramIndex++;

        $updates[] = "telefono = $" . $paramIndex;
        $params[] = $phone;
        $paramIndex++;

        $updates[] = "email = $" . $paramIndex;
        $params[] = $email;
        $paramIndex++;

        $updates[] = "sito = $" . $paramIndex;
        $params[] = $website;
        $paramIndex++;

        $updates[] = "ore = $" . $paramIndex;
        $params[] = $openingHours;
        $paramIndex++;

        if ($imageUrl) {
            $updates[] = "url_img = $" . $paramIndex;
            $params[] = $imageUrl;
            $paramIndex++;
        }

        if ($restaurantName !== $current_id_ristorante) {
            $updates = array_merge(["id_ristorante = $" . $paramIndex], $updates);
            $params = array_merge([$restaurantName], $params);
            $paramIndex++;
        }
    } elseif ($section === 'payments') {
        $acceptCard = isset($_POST['acceptCard']) ? true : false;
        $acceptCash = isset($_POST['acceptCash']) ? true : false;
        $acceptAppPayments = isset($_POST['acceptAppPayments']) ? true : false;
        $vatCode = $_POST['vatCode'] ?? '';
        $coverCharge = $_POST['coverCharge'] ?? '';

        logMessage('Form data received (payments): ' . json_encode([
            'acceptCard' => $acceptCard,
            'acceptCash' => $acceptCash,
            'acceptAppPayments' => $acceptAppPayments,
            'vatCode' => $vatCode,
            'coverCharge' => $coverCharge
        ]));

        if (empty($vatCode)) {
            $error = 'Il codice IVA è obbligatorio';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        if (!empty($coverCharge) && !is_numeric($coverCharge)) {
            $error = 'Il costo del coperto deve essere un numero';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        $coverChargeInCents = !empty($coverCharge) ? (int)($coverCharge * 100) : null;

        $updates[] = "carta = $" . $paramIndex;
        $params[] = $acceptCard;
        $paramIndex++;

        $updates[] = "contanti = $" . $paramIndex;
        $params[] = $acceptCash;
        $paramIndex++;

        $updates[] = "pagamenti_app = $" . $paramIndex;
        $params[] = $acceptAppPayments;
        $paramIndex++;

        $updates[] = "iva = $" . $paramIndex;
        $params[] = $vatCode;
        $paramIndex++;

        $updates[] = "coperto = $" . $paramIndex;
        $params[] = $coverChargeInCents;
        $paramIndex++;
    } elseif ($section === 'wifi') {
        $wifiName = $_POST['wifiName'] ?? '';
        $wifiPassword = $_POST['wifiPassword'] ?? '';

        logMessage('Form data received (wifi): ' . json_encode([
            'wifiName' => $wifiName,
            'wifiPassword' => $wifiPassword
        ]));

        if (empty($wifiName)) {
            $error = 'Il nome del Wi-Fi è obbligatorio';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        if (empty($wifiPassword)) {
            $error = 'La password del Wi-Fi è obbligatoria';
            logMessage($error);
            echo json_encode(['success' => false, 'message' => $error]);
            pg_close($conn);
            ob_end_flush();
            exit;
        }

        $updates[] = "nome_wifi = $" . $paramIndex;
        $params[] = $wifiName;
        $paramIndex++;

        $updates[] = "pwd_wifi = $" . $paramIndex;
        $params[] = $wifiPassword;
        $paramIndex++;
    } else {
        $error = 'Sezione non valida';
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
        pg_close($conn);
        ob_end_flush();
        exit;
    }

    // Esegui la query solo se ci sono aggiornamenti
    if (!empty($updates)) {
        $query = "UPDATE ristoranti SET " . implode(', ', $updates) . " WHERE id_ristorante = $" . $paramIndex;
        $params[] = $current_id_ristorante;
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

        logMessage('Update successful');
        echo json_encode(['success' => true, 'message' => 'Impostazioni ' . ucfirst($section) . ' aggiornate con successo']);

        if ($section === 'general' && isset($restaurantName) && $restaurantName !== $current_id_ristorante) {
            $_SESSION['login_restaurant'] = $restaurantName;
            logMessage("Updated session login_restaurant to: $restaurantName");
        }
    } else {
        $error = 'Nessun dato da aggiornare';
        logMessage($error);
        echo json_encode(['success' => false, 'message' => $error]);
    }

} catch (Exception $e) {
    $error = 'Eccezione: ' . $e->getMessage();
    logMessage($error);
    echo json_encode(['success' => false, 'message' => 'Errore del server']);
}

pg_close($conn);
logMessage('set_ristorante.php completed');
ob_end_flush();
exit;
?>