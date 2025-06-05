<?php
session_start();

// ----- GESTIONE DEGLI ERRORI -----
ini_set('display_errors', 0); 
error_reporting(E_ALL);       
ini_set('log_errors', 1);     
ini_set('error_log', __DIR__.'/php_update_error.log'); 
ob_start();                   
// ----- FINE GESTIONE ERRORI -----

header('Content-Type: application/json');
include '../connection.php'; // Assicurati che questo percorso sia corretto

// Log di avvio script (può essere mantenuto se moderato)
// error_log("update_ordine_stato.php: Script avviato."); 

if (!isset($_SESSION['login_restaurant']) || empty($_SESSION['login_restaurant'])) {
    if (ob_get_level() > 0) ob_clean();
    error_log("update_ordine_stato.php: Tentativo di accesso non autenticato."); // Log più specifico
    echo json_encode(['success' => false, 'message' => 'Ristorante non autenticato.']);
    if (ob_get_level() > 0) ob_end_flush();
    exit;
}
$id_ristorante_sessione = $_SESSION['login_restaurant'];
// error_log("update_ordine_stato.php: ID Ristorante sessione: " . $id_ristorante_sessione . " | Dati POST: " . json_encode($_POST)); // Log ridotto per produzione

$conn = connessione(); 

$action = $_POST['action'] ?? null;
$id_ordine_post = isset($_POST['id_ordine']) ? filter_var($_POST['id_ordine'], FILTER_VALIDATE_INT) : null;
$id_dettaglio_ordine_post = isset($_POST['id_dettaglio_ordine']) ? filter_var($_POST['id_dettaglio_ordine'], FILTER_VALIDATE_INT) : null;
$nuovo_stato = isset($_POST['nuovo_stato']) ? pg_escape_string($conn, trim($_POST['nuovo_stato'])) : null; // Aggiunto trim
$item_ids_json = $_POST['item_ids'] ?? null; 

$response = ['success' => false, 'message' => 'Azione non valida o dati mancanti.'];
$json_output_sent = false;

try {
    $id_ordine_azione = $id_ordine_post; 

    if (!$id_ordine_azione && $action !== 'test_connection') { // test_connection è un esempio, rimuovi se non usato
         throw new Exception("ID ordine mancante per l'azione.");
    }
    if (!$action) {
        throw new Exception("Azione non specificata.");
    }

    // Verifica che l'ordine principale dell'azione appartenga al ristorante corrente
    if ($action !== 'test_connection' && $id_ordine_azione) {
        $check_query_main_order = "SELECT id_ristorante FROM public.ordini WHERE id_ordine = $1";
        $check_result_main_order = pg_query_params($conn, $check_query_main_order, [$id_ordine_azione]);
        if ($check_result_main_order && pg_num_rows($check_result_main_order) > 0) {
            $ordine_db_main = pg_fetch_assoc($check_result_main_order);
            if ($ordine_db_main['id_ristorante'] !== $id_ristorante_sessione) {
                error_log("update_ordine_stato.php: Accesso non autorizzato. Ristorante sessione: $id_ristorante_sessione, Ristorante ordine: {$ordine_db_main['id_ristorante']}, Ordine: $id_ordine_azione, Azione: $action");
                throw new Exception("Accesso non autorizzato all'ordine #$id_ordine_azione.");
            }
        } else {
             error_log("update_ordine_stato.php: Ordine non trovato. Ordine: $id_ordine_azione, Azione: $action, Ristorante: $id_ristorante_sessione");
            throw new Exception("Ordine principale #$id_ordine_azione non trovato.");
        }
    }

    if ($action === 'update_status_ordine') {
        if ($nuovo_stato && $id_ordine_azione) {
            $valid_stati_ordine = ['pending', 'preparing', 'ready', 'served', 'cancelled'];
            if (!in_array($nuovo_stato, $valid_stati_ordine)) {
                 throw new Exception("Stato ordine non valido fornito: $nuovo_stato");
            }
            $query = "UPDATE public.ordini SET stato = $1 WHERE id_ordine = $2 AND id_ristorante = $3";
            $result = pg_query_params($conn, $query, [$nuovo_stato, $id_ordine_azione, $id_ristorante_sessione]);
            
            if ($result && pg_affected_rows($result) > 0) {
                $response = ['success' => true, 'message' => "Stato dell'ordine #$id_ordine_azione aggiornato a $nuovo_stato."];
                // Se l'ordine è pronto, servito o cancellato, aggiorna anche gli item
                if ($nuovo_stato === 'ready' || $nuovo_stato === 'served' || $nuovo_stato === 'cancelled') { 
                    $stato_item_corrispondente = ($nuovo_stato === 'cancelled') ? 'pending' : 'ready'; 
                    $query_items = "UPDATE public.dettagli_ordine SET stato_item = $1 WHERE id_ordine = $2";
                    pg_query_params($conn, $query_items, [$stato_item_corrispondente, $id_ordine_azione]); // Non controlliamo l'esito qui, ma potremmo aggiungere logging
                }
            } else {
                // Logga l'errore DB se c'è, altrimenti indica che nessuna riga è stata aggiornata (potrebbe essere OK se lo stato era già quello)
                $db_error_msg = pg_last_error($conn);
                error_log("update_ordine_stato.php: Errore o nessuna modifica per update_status_ordine. Ordine: $id_ordine_azione, Nuovo Stato: $nuovo_stato, Ristorante: $id_ristorante_sessione. Errore DB: $db_error_msg");
                throw new Exception("Impossibile aggiornare lo stato dell'ordine." . (pg_affected_rows($result) === 0 ? " Lo stato potrebbe essere già '$nuovo_stato'." : ""));
            }
        } else {
             throw new Exception("Dati insufficienti per aggiornare lo stato dell'ordine.");
        }

    } elseif ($action === 'update_status_item' && $id_dettaglio_ordine_post && $nuovo_stato && $id_ordine_azione) {
        $valid_stati_item = ['pending', 'preparing', 'ready']; 
         if (!in_array($nuovo_stato, $valid_stati_item)) {
            throw new Exception("Stato item non valido fornito: $nuovo_stato");
        }

        // Verifica che il dettaglio_ordine appartenga all'ordine principale (già verificato per il ristorante)
        $q_check_item_order = "SELECT id_ordine FROM public.dettagli_ordine WHERE id_dettaglio_ordine = $1";
        $r_check_item_order = pg_query_params($conn, $q_check_item_order, [$id_dettaglio_ordine_post]);
        if (!$r_check_item_order || pg_num_rows($r_check_item_order) == 0) {
            error_log("update_ordine_stato.php: Dettaglio ordine non trovato. Dettaglio ID: $id_dettaglio_ordine_post, Ordine Azione: $id_ordine_azione, Ristorante: $id_ristorante_sessione");
            throw new Exception("Dettaglio ordine #$id_dettaglio_ordine_post non trovato.");
        }
        $item_order_db = pg_fetch_assoc($r_check_item_order);
        if ((int)$item_order_db['id_ordine'] !== (int)$id_ordine_azione) {
             error_log("update_ordine_stato.php: Inconsistenza ID ordine per dettaglio. Dettaglio ID: $id_dettaglio_ordine_post (appartiene a Ordine ID: {$item_order_db['id_ordine']}), Ordine Azione: $id_ordine_azione, Ristorante: $id_ristorante_sessione");
            throw new Exception("Il dettaglio ordine #$id_dettaglio_ordine_post non appartiene all'ordine #$id_ordine_azione specificato.");
        }
        
        $query = "UPDATE public.dettagli_ordine SET stato_item = $1 WHERE id_dettaglio_ordine = $2"; // id_ordine è già verificato
        $result = pg_query_params($conn, $query, [$nuovo_stato, $id_dettaglio_ordine_post]);
        if ($result && pg_affected_rows($result) > 0) {
            $response = ['success' => true, 'message' => "Stato dell'item #$id_dettaglio_ordine_post aggiornato a $nuovo_stato."];
        } else {
            $db_error_msg = pg_last_error($conn);
            error_log("update_ordine_stato.php: Errore o nessuna modifica per update_status_item. Dettaglio ID: $id_dettaglio_ordine_post, Nuovo Stato: $nuovo_stato, Ordine Azione: $id_ordine_azione, Ristorante: $id_ristorante_sessione. Errore DB: $db_error_msg");
            throw new Exception("Impossibile aggiornare lo stato dell'item." . (pg_affected_rows($result) === 0 ? " Lo stato potrebbe essere già '$nuovo_stato'." : ""));
        }

    } elseif (($action === 'mark_all_complete' || $action === 'mark_all_incomplete') && $item_ids_json && $id_ordine_azione) {
        $item_ids = json_decode($item_ids_json, true);
        if (is_array($item_ids) && count($item_ids) > 0) {
            $nuovo_stato_item = ($action === 'mark_all_complete') ? 'ready' : 'preparing';
            
            $sanitized_item_ids = [];
            foreach($item_ids as $item_id) {
                if (filter_var($item_id, FILTER_VALIDATE_INT) !== false) {
                    $sanitized_item_ids[] = (int)$item_id;
                } else {
                    // Logga l'ID non valido ma non interrompere per tutti se uno è malformato, a meno che non sia critico
                    error_log("update_ordine_stato.php: ID item non valido nel batch: " . htmlspecialchars($item_id) . " per Ordine: $id_ordine_azione, Ristorante: $id_ristorante_sessione");
                    // Potresti decidere di continuare o lanciare un'eccezione qui
                    // throw new Exception("ID item non valido nel batch: " . htmlspecialchars($item_id));
                }
            }
            if (empty($sanitized_item_ids)) {
                throw new Exception("Nessun ID item valido fornito per l'aggiornamento batch.");
            }

            $placeholders = implode(',', array_map(function($i) { return '$'.($i+3); }, array_keys($sanitized_item_ids))); 
            
            $query_items_batch = "UPDATE public.dettagli_ordine SET stato_item = $1 WHERE id_ordine = $2 AND id_dettaglio_ordine IN ($placeholders)";
            $params_batch = array_merge([$nuovo_stato_item, $id_ordine_azione], $sanitized_item_ids);

            $result_batch = pg_query_params($conn, $query_items_batch, $params_batch);
            if ($result_batch) {
                // Non è necessario controllare pg_affected_rows qui, perché alcuni item potrebbero essere già nello stato corretto
                $response = ['success' => true, 'message' => "Stato degli items aggiornato per l'ordine #$id_ordine_azione."];
            } else {
                $db_error_msg = pg_last_error($conn);
                error_log("update_ordine_stato.php: Errore DB (batch update). Ordine: $id_ordine_azione, Azione: $action, Ristorante: $id_ristorante_sessione. Errore DB: $db_error_msg");
                throw new Exception("Errore durante l'aggiornamento batch degli items.");
            }
        } else {
            throw new Exception("Nessun ID item fornito o formato JSON non valido per l'aggiornamento batch.");
        }
    
    } else {
         throw new Exception("Azione non riconosciuta ($action) o dati insufficienti forniti.");
    }

    if (ob_get_level() > 0) ob_clean();
    echo json_encode($response);
    $json_output_sent = true;

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    // Logga l'errore dettagliato sul server
    error_log("Exception in update_ordine_stato.php (Ristorante: $id_ristorante_sessione, Azione: $action, Ordine: $id_ordine_post): " . $errorMessage . " | Trace: " . $e->getTraceAsString()); 
    if (ob_get_level() > 0) ob_clean(); 
    // Invia un messaggio generico al client
    echo json_encode(['success' => false, 'message' => "Si è verificato un errore durante l'aggiornamento. Riprova più tardi."]);
    $json_output_sent = true;
} finally {
    if ($conn) {
        pg_close($conn);
    }
    if ($json_output_sent && ob_get_status() && ob_get_level() > 0) { 
        ob_end_flush(); 
    } else if (ob_get_status() && ob_get_level() > 0) { 
        ob_end_clean(); 
    }
}
?>
