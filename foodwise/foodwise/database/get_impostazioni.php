<?php
// Enable debug mode (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once __DIR__ . '/connection.php';

// Constants
const ROLE_MANAGER = 'Manager';
const FROM_TEST_HEADER = 'test.php';

// Connect to database
$conn = connessione();
if (!$conn) {
    respond_with_error('Database connection failed');
}

// Check if the script is being included (not directly requested)
$isDirectRequest = isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === 'get_impostazioni.php';
$isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === FROM_TEST_HEADER;

if ($isAjaxRequest) {
    define('FROM_INCLUDE', true);
}

// Define the query with a placeholder
$query = "SELECT username, full_name, email, ruolo, id_ristorante, url_img, telefono, hired 
          FROM personale 
          WHERE ruolo = $1";

// Attempt to prepare the statement
$prepared = @pg_prepare($conn, 'get_manager', $query);
if ($prepared === false) {
    $error = pg_last_error($conn);
    if (stripos($error, 'prepared statement "get_manager" already exists') !== false) {
        // If the statement already exists, execute it directly
        $result = pg_execute($conn, 'get_manager', [ROLE_MANAGER]);
    } else {
        respond_with_error('Failed to prepare query: ' . $error);
    }
} else {
    // If preparation succeeded, execute the statement
    $result = pg_execute($conn, 'get_manager', [ROLE_MANAGER]);
}

if ($result === false) {
    respond_with_error('Query execution failed: ' . pg_last_error($conn));
}

// Fetch data
$managerList = [];
while ($row = pg_fetch_assoc($result)) {
    $managerList[] = $row;
}

// Close connection
pg_close($conn);

// Return data if included or not a direct request
if (defined('FROM_INCLUDE') || !$isDirectRequest) {
    return $managerList;
}

// Return JSON response (only for direct requests)
if (!headers_sent()) {
    header('Content-Type: application/json');
}
echo json_encode([
    'success' => true,
    'data' => $managerList[0] ?? null
], JSON_UNESCAPED_UNICODE);
exit;

/**
 * Sends a JSON error response and exits.
 * @param string $message Error message
 */
function respond_with_error($message) {
    global $conn;
    if ($conn) {
        pg_close($conn);
    }
    if (!defined('FROM_INCLUDE')) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $message]);
        } else {
            echo "<p>Error: $message</p>";
        }
    }
    error_log($message);
    exit;
}
?>