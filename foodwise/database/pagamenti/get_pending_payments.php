<?php
session_start();

ini_set('display_errors', 0); 
error_reporting(E_ALL);       
ini_set('log_errors', 1);     
ini_set('error_log', __DIR__.'/php_payments_error.log'); 
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
    error_log("get_pending_payments.php: Connessione al database fallita per ristorante ID $id_ristorante_sessione");
    echo json_encode(['success' => false, 'message' => 'Errore di connessione al database.']);
    if (ob_get_level() > 0) ob_end_flush();
    exit;
}

$tavoli_con_pagamenti_pendenti = [];
$json_output_sent = false;

function format_postgres_interval_agg($interval_string) {
    if ($interval_string === null || trim($interval_string) === '') {
        return "poco fa";
    }
    $tempo_trascorso_format = "";
    if (strpos($interval_string, '-') === 0) {
        $interval_string = ltrim($interval_string, '-');
    }
    preg_match('/(?:(\d+)\s*days?)?\s*(?:(\d{1,2}):(\d{1,2}):(\d{1,2})(?:\.\d+)?)?/', $interval_string, $matches);
    $days = !empty($matches[1]) ? (int)$matches[1] : 0;
    $hours = !empty($matches[2]) ? (int)$matches[2] : 0;
    $minutes = !empty($matches[3]) ? (int)$matches[3] : 0;
    if ($days > 0) $tempo_trascorso_format .= $days . ($days === 1 ? "g " : "gg ");
    if ($hours > 0) $tempo_trascorso_format .= $hours . "h ";
    if ($minutes > 0) $tempo_trascorso_format .= $minutes . "min ";
    $tempo_trascorso_format = trim($tempo_trascorso_format);
    return empty($tempo_trascorso_format) ? "poco fa" : $tempo_trascorso_format . " fa";
}

try {
    $query_ordini_per_tavolo = "
        SELECT 
            t.id_tavolo,
            t.numero_tavolo,
            o.id_ordine,
            o.data_ordine,
            AGE(now(), o.data_ordine) AS intervallo_eta_ordine,
            (SELECT SUM(det.prezzo_unitario_registrato * det.quantita) 
             FROM public.dettagli_ordine det 
             WHERE det.id_ordine = o.id_ordine) AS prezzo_totale_ordine
        FROM public.ordini AS o
        JOIN public.tavoli AS t ON o.id_tavolo = t.id_tavolo AND o.id_ristorante = t.id_ristorante
        WHERE o.id_ristorante = $1 AND o.stato = 'served' AND o.pagato = FALSE
        ORDER BY t.id_tavolo, o.data_ordine ASC";

    $result_ordini_per_tavolo = pg_query_params($conn, $query_ordini_per_tavolo, [$id_ristorante_sessione]);

    if (!$result_ordini_per_tavolo) {
        error_log("Errore SQL (query_ordini_per_tavolo) per Ristorante ID $id_ristorante_sessione: " . pg_last_error($conn));
        throw new Exception("Errore durante il recupero degli ordini per tavolo.");
    }

    $ordini_buffer_per_tavolo = [];
    while ($row = pg_fetch_assoc($result_ordini_per_tavolo)) {
        $ordini_buffer_per_tavolo[$row['id_tavolo']]['numero_tavolo'] = $row['numero_tavolo'] ?? 'Asporto';
        $ordini_buffer_per_tavolo[$row['id_tavolo']]['ordini'][] = [
            'id_ordine' => $row['id_ordine'],
            'data_ordine' => $row['data_ordine'],
            'intervallo_eta_ordine' => $row['intervallo_eta_ordine'],
            'prezzo_totale_ordine' => (float)$row['prezzo_totale_ordine']
        ];
    }

    $tutti_gli_id_ordini_pendenti = [];
    foreach ($ordini_buffer_per_tavolo as $tav_data) { // Removed $id_tav as it's not used here
        foreach ($tav_data['ordini'] as $ordine_info) {
            $tutti_gli_id_ordini_pendenti[] = $ordine_info['id_ordine'];
        }
    }
    $tutti_gli_id_ordini_pendenti = array_unique($tutti_gli_id_ordini_pendenti);

    $items_dettaglio_per_ordine = [];
    if (!empty($tutti_gli_id_ordini_pendenti)) {
        $placeholders_items = implode(',', array_map(function($i) { return '$'.($i+1); }, array_keys($tutti_gli_id_ordini_pendenti)));
        $query_dettagli_items = "
            SELECT det.id_ordine, det.id_dettaglio_ordine, p.nome AS nome_prodotto, 
                   det.quantita, det.prezzo_unitario_registrato, det.note_riga, det.stato_item
            FROM public.dettagli_ordine AS det 
            JOIN public.prodotti AS p ON det.id_portata = p.id_portata 
            WHERE det.id_ordine IN ($placeholders_items)
            ORDER BY det.id_ordine, det.id_dettaglio_ordine ASC";
        
        $result_dettagli_items = pg_query_params($conn, $query_dettagli_items, $tutti_gli_id_ordini_pendenti);

        if (!$result_dettagli_items) {
            error_log("Errore SQL (query_dettagli_items) per Ristorante ID $id_ristorante_sessione: " . pg_last_error($conn));
            throw new Exception("Errore durante il recupero dei dettagli degli item.");
        }
        while ($item_row = pg_fetch_assoc($result_dettagli_items)) {
            $items_dettaglio_per_ordine[$item_row['id_ordine']][] = $item_row;
        }
    }
    
    foreach ($ordini_buffer_per_tavolo as $id_tavolo => $tav_data) {
        $prezzo_totale_tavolo = 0;
        $lista_ordini_ids_tavolo = [];
        $items_aggregati_tavolo = [];
        $data_primo_ordine = null;
        $intervallo_eta_primo_ordine = null;

        foreach ($tav_data['ordini'] as $ordine_info) {
            $prezzo_totale_tavolo += $ordine_info['prezzo_totale_ordine'];
            $lista_ordini_ids_tavolo[] = $ordine_info['id_ordine'];

            if ($data_primo_ordine === null || $ordine_info['data_ordine'] < $data_primo_ordine) {
                $data_primo_ordine = $ordine_info['data_ordine'];
                $intervallo_eta_primo_ordine = $ordine_info['intervallo_eta_ordine'];
            }
            
            if (isset($items_dettaglio_per_ordine[$ordine_info['id_ordine']])) {
                foreach ($items_dettaglio_per_ordine[$ordine_info['id_ordine']] as $item_dett) {
                    $items_aggregati_tavolo[] = [
                        'id_ordine_origine' => $ordine_info['id_ordine'],
                        'id_dettaglio_ordine' => $item_dett['id_dettaglio_ordine'],
                        'nome_prodotto' => $item_dett['nome_prodotto'],
                        'quantita' => (int)$item_dett['quantita'],
                        'prezzo_unitario_registrato' => (float)$item_dett['prezzo_unitario_registrato'],
                        'note_riga' => $item_dett['note_riga']
                    ];
                }
            }
        }
        
        $tavoli_con_pagamenti_pendenti[] = [
            'id_tavolo' => $id_tavolo,
            'numero_tavolo' => $tav_data['numero_tavolo'],
            'lista_ordini_ids' => $lista_ordini_ids_tavolo,
            'items_tavolo' => $items_aggregati_tavolo,
            'prezzo_totale_tavolo_da_pagare' => number_format($prezzo_totale_tavolo, 2, '.', ''),
            'tempo_trascorso_primo_ordine' => format_postgres_interval_agg($intervallo_eta_primo_ordine),
            'payment_status_ui_tavolo' => 'pending'
        ];
    }

    if (ob_get_level() > 0) ob_clean(); 
    echo json_encode(['success' => true, 'tavoli' => $tavoli_con_pagamenti_pendenti]);
    $json_output_sent = true;

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    error_log("Exception in get_pending_payments.php (Ristorante: $id_ristorante_sessione): " . $errorMessage . " | Trace: " . $e->getTraceAsString()); 
    if (!$json_output_sent && ob_get_level() > 0) ob_clean(); 
    echo json_encode(['success' => false, 'message' => "Si è verificato un errore durante il caricamento dei pagamenti per tavolo. Riprova più tardi."]);
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
