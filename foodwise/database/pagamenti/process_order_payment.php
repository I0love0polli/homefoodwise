<?php
session_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/php_payments_process_error.log');
ob_start();

header('Content-Type: application/json');
include '../connection.php';

if (!isset($_SESSION['login_restaurant']) || empty($_SESSION['login_restaurant'])) {
    if (ob_get_level() > 0) ob_clean();
    echo json_encode(['success' => false, 'message' => 'Ristorante non autenticato.']);
    if (ob_get_level() > 0) ob_end_flush();
    exit;
}
$id_ristorante_sessione = $_SESSION['login_restaurant'];

$conn = connessione();
if (!$conn) {
    if (ob_get_level() > 0) ob_clean();
    error_log("process_order_payment.php: Database connection failed for restaurant ID $id_ristorante_sessione");
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database.']);
    if (ob_get_level() > 0) ob_end_flush();
    exit;
}

$action = $_POST['action'] ?? null;
$id_tavolo_post = filter_input(INPUT_POST, 'id_tavolo', FILTER_VALIDATE_INT); 
$payment_method = isset($_POST['payment_method']) ? pg_escape_string($conn, $_POST['payment_method']) : null;

$response = ['success' => false, 'message' => 'Azione non valida o ID tavolo mancante.'];
$json_output_sent = false;

if (!$id_tavolo_post && $action !== 'mark_payment_failed_tavolo') {
    if (ob_get_level() > 0) ob_clean();
    echo json_encode(['success' => false, 'message' => 'ID Tavolo mancante.']);
    if (ob_get_level() > 0) ob_end_flush();
    exit;
}

try {
    if ($id_tavolo_post) {
        $check_table_query = "SELECT id_ristorante FROM public.tavoli WHERE id_tavolo = $1";
        $check_table_result = pg_query_params($conn, $check_table_query, [$id_tavolo_post]);
        if (!$check_table_result || pg_num_rows($check_table_result) === 0) {
            throw new Exception("Tavolo #{$id_tavolo_post} non trovato.");
        }
        $tavolo_db = pg_fetch_assoc($check_table_result);
        if ($tavolo_db['id_ristorante'] !== $id_ristorante_sessione) {
            throw new Exception("Accesso non autorizzato al tavolo #{$id_tavolo_post}.");
        }
    }

    if ($action === 'confirm_payment_tavolo') {
        if (!$payment_method || !in_array($payment_method, ['cash', 'card'])) {
            throw new Exception("Metodo di pagamento non valido: {$payment_method}.");
        }

        pg_query($conn, "BEGIN");

        $query_ordini_del_tavolo = "SELECT id_ordine FROM public.ordini 
                                    WHERE id_tavolo = $1 AND id_ristorante = $2 
                                    AND stato = 'served' AND pagato = FALSE";
        $result_ordini_del_tavolo = pg_query_params($conn, $query_ordini_del_tavolo, [$id_tavolo_post, $id_ristorante_sessione]);

        if (!$result_ordini_del_tavolo) {
            pg_query($conn, "ROLLBACK");
            throw new Exception("Errore nel recuperare gli ordini per il tavolo #{$id_tavolo_post}.");
        }

        $ordini_da_aggiornare_ids = [];
        while ($row_ordine = pg_fetch_assoc($result_ordini_del_tavolo)) {
            $ordini_da_aggiornare_ids[] = $row_ordine['id_ordine'];
        }

        if (empty($ordini_da_aggiornare_ids)) {
            pg_query($conn, "ROLLBACK");
            throw new Exception("Nessun ordine da pagare trovato per il tavolo #{$id_tavolo_post}. Potrebbe essere già stato saldato.");
        }

        $placeholders_update = implode(',', array_map(function($i) { return '$'.($i+2); }, array_keys($ordini_da_aggiornare_ids)));
        $query_update_ordini = "UPDATE public.ordini SET pagato = TRUE, stato = 'completed' 
                                WHERE id_ristorante = $1 AND id_ordine IN ($placeholders_update)";
        $params_update = array_merge([$id_ristorante_sessione], $ordini_da_aggiornare_ids);
        
        $result_update_ordini = pg_query_params($conn, $query_update_ordini, $params_update);

        if (!$result_update_ordini || pg_affected_rows($result_update_ordini) < count($ordini_da_aggiornare_ids)) {
            pg_query($conn, "ROLLBACK");
            $affected = $result_update_ordini ? pg_affected_rows($result_update_ordini) : 'Errore query';
            error_log("process_order_payment.php: Fallito aggiornamento di alcuni/tutti gli ordini per tavolo #{$id_tavolo_post}. Previsti: ".count($ordini_da_aggiornare_ids).", Aggiornati: ".$affected.". Errore DB: ".pg_last_error($conn));
            throw new Exception("Impossibile aggiornare lo stato di tutti gli ordini per il tavolo #{$id_tavolo_post}.");
        }
        
        $check_altri_ordini_attivi = "SELECT 1 FROM public.ordini 
                                      WHERE id_tavolo = $1 AND id_ristorante = $2 
                                      AND stato NOT IN ('completed', 'cancelled') LIMIT 1";
        $result_altri_ordini = pg_query_params($conn, $check_altri_ordini_attivi, [$id_tavolo_post, $id_ristorante_sessione]);

        if ($result_altri_ordini && pg_num_rows($result_altri_ordini) === 0) {
            $update_table_query = "UPDATE public.tavoli SET stato = 'Free' WHERE id_tavolo = $1 AND id_ristorante = $2";
            $result_update_table = pg_query_params($conn, $update_table_query, [$id_tavolo_post, $id_ristorante_sessione]);
            if (!$result_update_table) {
                 pg_query($conn, "ROLLBACK");
                 error_log("process_order_payment.php: Impossibile aggiornare stato tavolo #{$id_tavolo_post}. " . pg_last_error($conn));
            }
        }
        
        pg_query($conn, "COMMIT");
        $response = ['success' => true, 'message' => "Pagamento per tavolo #{$id_tavolo_post} (Ordini: " . implode(', ', $ordini_da_aggiornare_ids) . ") con metodo '{$payment_method}' confermato."];

    } elseif ($action === 'mark_payment_failed_tavolo') {
        if (!$id_tavolo_post) throw new Exception("ID Tavolo mancante per registrare fallimento.");
        $response = ['success' => true, 'message' => "Tentativo di pagamento per tavolo #{$id_tavolo_post} registrato come fallito (UI). Nessuna modifica al DB."];
    
    } else {
        throw new Exception("Azione '{$action}' non riconosciuta.");
    }

    if (ob_get_level() > 0) ob_clean();
    echo json_encode($response);
    $json_output_sent = true;

} catch (Exception $e) {
    if (pg_connection_status($conn) === PGSQL_CONNECTION_OK && pg_get_transaction_status($conn) === PGSQL_TRANSACTION_INPROGRESS) {
        pg_query($conn, "ROLLBACK"); 
    }
    $errorMessage = $e->getMessage();
    error_log("Exception in process_order_payment.php (Ristorante: $id_ristorante_sessione, Tavolo: $id_tavolo_post, Azione: $action): " . $errorMessage . " | Trace: " . $e->getTraceAsString());
    if (!$json_output_sent && ob_get_level() > 0) ob_clean();
    echo json_encode(['success' => false, 'message' => $errorMessage]);
    $json_output_sent = true;
} finally {
    if ($conn) {
        pg_close($conn);
    }
     if ($json_output_sent && ob_get_status() && ob_get_level() > 0) { 
        ob_end_flush(); 
    } else if (!$json_output_sent && ob_get_status() && ob_get_level() > 0) {
        ob_end_clean(); 
    }
}
?>
