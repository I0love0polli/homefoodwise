<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login_restaurant']) || empty($_SESSION['login_restaurant'])) {
    if (!isset($_SESSION['login_restaurant']) || empty($_SESSION['login_restaurant'])) {
        die("Accesso non autorizzato o sessione scaduta. Effettua nuovamente il login.");
    }
}
$id_ristorante_corrente = $_SESSION['login_restaurant'];
?>

<div class="container pagamenti-container">
    <div class="modal-overlay" id="modal-overlay-payments" style="display: none;"></div>

    <div class="row justify-content-center mb-3">
        <div class="col-12 text-center">
            <div class="total-pending-payments-display">
                Totale Pagamenti Pendenti: <span id="totalPendingAmount">€0.00</span>
            </div>
        </div>
    </div>

    <div id="waiter-payment-view" style="display: none;"> 
        <div class="row justify-content-center">
            <div class="col-12 mb-1">
                <div class="header-actions mb-4">
                    <div class="search-bar-container">
                        <div class="search-bar">
                            <input type="text" id="searchPaymentInputWaiter" class="form-control"
                                onkeyup="searchPaymentsWaiter()">
                            <i data-lucide="search" class="search-icon"></i>
                        </div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-cashier-view" onclick="showCashierPaymentView()">
                            <i data-lucide="credit-card" class="cashier-icon"></i>Vista Cassa
                        </button>
                    </div>
                </div>
                <div class="filter-tabs mb-4" id="waiterPaymentFilterTabs">
                    <button class="filter-tab active" data-status-filter="all">Tutti i Tavoli</button>
                    <button class="filter-tab" data-status-filter="pending">Da Pagare</button>
                    <button class="filter-tab" data-status-filter="processing">In Elaborazione</button>
                </div>
            </div>
        </div>
        <div class="row g-3" id="waiterPaymentsContainer">
            <div class="loading-placeholder text-center mt-5" id="waiterPaymentsLoadingPlaceholder">
                <p>Caricamento pagamenti tavoli...</p>
            </div>
        </div>
    </div>

    <div id="cashier-payment-view" style="display: none;"> 
        <div class="row justify-content-center">
            <div class="col-12 mb-1">
                <div class="header-actions mb-4">
                    <div class="search-bar-container">
                        <div class="search-bar">
                            <input type="text" id="searchPaymentInputCashier" class="form-control"
                                onkeyup="searchPaymentsCashier()">
                            <i data-lucide="search" class="search-icon"></i>
                        </div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-waiter-view" onclick="showWaiterPaymentView()">
                            <i data-lucide="hand-platter" class="waiter-icon"></i>Vista Cameriere
                        </button>
                    </div>
                </div>
                <div class="filter-tabs mb-4" id="cashierPaymentFilterTabs">
                    <button class="filter-tab active" data-status-filter="all">Tutti i Tavoli</button>
                    <button class="filter-tab" data-status-filter="pending">Da Pagare</button>
                    <button class="filter-tab" data-status-filter="processing">In Elaborazione</button>
                    <button class="filter-tab" data-status-filter="completed">Completati</button>
                    <button class="filter-tab" data-status-filter="failed">Falliti</button>
                </div>
            </div>
        </div>
        <div class="row g-3" id="cashierPaymentsContainer">
             <div class="loading-placeholder text-center mt-5" id="cashierPaymentsLoadingPlaceholder">
                <p>Caricamento pagamenti tavoli...</p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmTablePaymentModal" tabindex="-1" aria-labelledby="confirmTablePaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmTablePaymentModalLabel">Conferma Pagamento Tavolo #<span id="modalTableNumber"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Totale da pagare: <strong id="modalTableTotalAmount">€0.00</strong></p>
                    <p>Seleziona il metodo di pagamento:</p>
                    <div class="row g-3 payment-methods"> 
                        <div class="col-6"> 
                            <button class="btn btn-payment-method btn-cash w-100" onclick="processTablePaymentConfirmation('cash')">
                                <i data-lucide="hand-coins" class="payment-icon"></i>
                                <span>Contanti</span>
                            </button>
                        </div>
                        <div class="col-6"> 
                            <button class="btn btn-payment-method btn-card w-100" onclick="processTablePaymentConfirmation('card')">
                                <i data-lucide="credit-card" class="payment-icon"></i>
                                <span>Carta</span>
                            </button>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-cancel w-100" data-bs-dismiss="modal">Annulla</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pagamenti-container {
        background-color: #121212; color: #e0e0e0; padding: 20px; min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .modal-overlay { 
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);
        z-index: 1040; display: none;
    }
    .total-pending-payments-display {
        background-color: #1e1e1e; color: #00FF7F; padding: 10px 15px; border-radius: 8px;
        font-size: 1.1rem; font-weight: 600; border: 1px solid #00FF7F;
        display: inline-block; box-shadow: 0 2px 8px rgba(0, 255, 127, 0.1);
    }
    .header-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .search-bar-container { flex: 1 1 300px; display: flex; justify-content: flex-start; }
    .search-bar { position: relative; width: 100%; max-width: 450px; }
    .search-bar .form-control {
        background-color: #1e1e1e; border: 1px solid #333; color: #e0e0e0 !important; border-radius: 8px;
        padding: 10px 40px 10px 15px; height: 42px; box-sizing: border-box;
    }
    .search-bar .form-control:focus { border-color: #00FF7F; box-shadow: 0 0 0 0.2rem rgba(0, 255, 127, 0.25); }
    .search-bar .search-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #888; width: 20px; height: 20px; }
    .button-group { display: flex; gap: 10px; }
    .btn { 
        padding: 0 16px; border-radius: 8px !important; transition: all 0.2s ease-in-out;
        white-space: nowrap; height: 42px; display: inline-flex; 
        align-items: center; justify-content: center; box-sizing: border-box;
        gap: 8px; font-weight: 600 !important; border: 1px solid transparent !important;
        text-decoration: none !important; cursor: pointer; font-size: 0.9rem;
    }
    .btn:hover, .btn:focus { transform: translateY(-1px); box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .btn-cashier-view { background-color: rgba(139, 92, 246, 0.15); border-color: #8B5CF6 !important; color: #8B5CF6 !important; }
    .btn-cashier-view:hover { background-color: rgba(139, 92, 246, 0.25) !important; }
    .cashier-icon { width: 18px; height: 18px; }
    .btn-waiter-view { background-color: rgba(59, 130, 246, 0.15); border-color: #3B82F6 !important; color: #3B82F6 !important; }
    .btn-waiter-view:hover { background-color: rgba(59, 130, 246, 0.25) !important; }
    .waiter-icon { width: 18px; height: 18px; }
    .filter-tabs {
        display: flex; gap: 5px; border-bottom: 1px solid #333; padding-bottom: 8px;
        flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch;
        scrollbar-width: thin; scrollbar-color: #444 #2a2a2a;
    }
    .filter-tabs::-webkit-scrollbar { height: 6px; }
    .filter-tabs::-webkit-scrollbar-track { background: #2a2a2a; border-radius: 3px;}
    .filter-tabs::-webkit-scrollbar-thumb { background: #444; border-radius: 3px; }
    .filter-tab {
        background-color: transparent; border: none; color: #aaa; padding: 8px 15px; font-size: 0.9rem;
        transition: color 0.2s ease, border-color 0.2s ease; white-space: nowrap; cursor: pointer;
        border-bottom: 2px solid transparent; margin-bottom: -1px;
    }
    .filter-tab:hover { color: #e0e0e0; }
    .filter-tab.active { color: #00FF7F; border-bottom-color: #00FF7F; font-weight: 600; }
    .payment-card, .cashier-payment-card { margin-bottom: 1rem; } 
    .payment-info-card, .cashier-info-card { 
        min-height: 160px; height: auto; width: 100%; background-color: #1e1e1e;
        border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex; flex-direction: column; border: 1px solid #2b2b2b;
    }
    .text-muted {
        --bs-text-opacity: 1;
        color: #e0e0e0 !important;
    }



    .payment-info-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: pointer; }
    .cashier-info-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .card-body.flex-column { padding: 1rem; display: flex; flex-direction: column; flex-grow: 1;} 
    .card-title { margin-bottom: 0.75rem; font-size: 1.1rem; font-weight: 600; color: #e0e0e0; display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem;}
    .price-container { display: flex; align-items: center; margin-left:auto; } 
    .price { color: #00FF7F; font-weight: 700; font-size: 1rem; } 
    .payment-list, .cashier-payment-list { 
        padding-left: 0; list-style: none; margin-bottom: 1rem; 
        overflow-y: auto; scrollbar-width: thin; scrollbar-color: #444 #2a2a2a;
    }
    .payment-list { max-height: 80px; } 
    .payment-list li { min-height: 24px; padding: 2px 0; display: flex; align-items: center; color: #ccc; font-size: 0.85rem;}
    .cashier-payment-list { max-height: 150px; } 
    .cashier-payment-item { 
        display: flex; align-items: center; background-color: #282828; 
        padding: 8px 12px; margin-bottom: 6px; border-radius: 6px;
        border: 1px solid #383838; transition: background-color 0.2s ease, border-color 0.2s ease;
        cursor: default; min-height: 40px; box-sizing: border-box; font-size: 0.9rem;
    }
    .cashier-payment-item .item-name { flex: 1; margin-right: 10px; color: #ddd; }
    .order-icon, .payment-icon { width: 16px; height: 16px; margin-right: 0.5rem; flex-shrink: 0; } 
    .payment-icon.green { color: #00FF7F; } 
    .payment-icon.orange { color: #F97316; }
    .footer-container { display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: auto; }
    .time-text, .payment-text { color: #888; font-size: 0.75rem; display: inline-flex; align-items: center;}
    .clock-icon, .hash-icon { width: 12px; height: 12px; color: #888; margin-right: 0.25rem; }
    .delete-btn-container { position: absolute; left: 50%; transform: translateX(-50%); display: none; }
    .delete-image-btn { background: transparent; border: none; padding: 6px; cursor: pointer; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; }
    .delete-image-btn:hover { background-color: rgba(220, 53, 69, 0.2); }
    .delete-image-btn .action-icon { color: #dc3545; width: 16px; height: 16px; }
    .badge { margin-left: 0.5rem; padding: 0.3rem 0.6rem; border-radius: 50px; font-size: 0.7rem; display: inline-flex; align-items: center; font-weight: 600; line-height: 1;}
    .status-icon { width: 12px; height: 12px; margin-right: 0.3rem; }
    .status-pending { background-color: rgba(253, 126, 20, 0.15); border: 1px solid rgba(253, 126, 20, 0.5); color: #fd7e14; } 
    .status-pending .status-icon { color: #fd7e14; }
    .status-processing { background-color: rgba(13, 110, 253, 0.15); border: 1px solid rgba(13, 110, 253, 0.5); color: #0d6efd; } 
    .status-processing .status-icon { color: #0d6efd; }
    .status-completed { background-color: rgba(52, 199, 89, 0.15); border: 1px solid rgba(52, 199, 89, 0.5); color: #34c759; } 
    .status-completed .status-icon { color: #34c759; }
    .status-failed { background-color: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.5); color: #dc3545; } 
    .status-failed .status-icon { color: #dc3545; }
    .cashier-actions { display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 8px; width: 100%; margin-top: 1rem; }
    .cashier-actions .btn { flex: 1 1 auto; padding: 0 10px; text-align: center; min-width: 120px; font-size:0.85rem;}
    .action-icon { width: 16px; height: 16px; } 
    .btn-start-payment { background-color: rgba(13, 110, 253, 0.15); border-color: #0d6efd !important; color: #0d6efd !important; }
    .btn-start-payment:hover { background-color: rgba(13, 110, 253, 0.25) !important; }
    .btn-confirm-payment { background-color: rgba(52, 199, 89, 0.15); border-color: #34c759 !important; color: #34c759 !important; }
    .btn-confirm-payment:hover { background-color: rgba(52, 199, 89, 0.25) !important; }
    .btn-retry-payment { background-color: rgba(253, 126, 20, 0.15); border-color: #fd7e14 !important; color: #fd7e14 !important; }
    .btn-retry-payment:hover { background-color: rgba(253, 126, 20, 0.25) !important; }
    #confirmTablePaymentModal .modal-content { background-color: #232323; color: #e0e0e0; border-radius: 10px; border: 1px solid #444; }
    #confirmTablePaymentModal .modal-header { border-bottom: 1px solid #444; padding: 1rem 1.5rem;}
    #confirmTablePaymentModal .modal-title { font-size: 1.25rem; font-weight: 600; }
    #confirmTablePaymentModal .btn-close { filter: invert(1); opacity: 0.8;}
    #confirmTablePaymentModal .btn-close:hover { opacity: 1;}
    #confirmTablePaymentModal .modal-body { padding: 1.5rem; }
    #confirmTablePaymentModal .modal-body p { margin-bottom: 0.75rem; font-size: 1rem;}
    #confirmTablePaymentModal .modal-body strong { color: #00FF7F; }
    .payment-methods { 
        display: flex; 
        flex-direction: row; 
        gap: 1rem; 
        margin-top: 1rem; 
    }
    .payment-methods > .col-6 { /* Ensure columns take up 50% and allow buttons to fill */
        flex: 0 0 50%;
        max-width: 50%;
        display: flex; /* Make column a flex container for its button */
    }
    .btn-payment-method { 
        width: 100%; 
        min-height: 100px; 
        height: auto; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        font-size: 0.9rem; 
        border-radius: 10px !important; 
        padding: 0.75rem; 
        text-align: center; 
        line-height: 1.3; 
    }
    .btn-payment-method .payment-icon { width: 32px; height: 32px; margin-bottom: 0.5rem; } 
    .btn-cash { background-color: rgba(236, 72, 153, 0.15); border-color: #EC4899 !important; color: #EC4899 !important; }
    .btn-cash:hover { background-color: rgba(236, 72, 153, 0.25) !important; box-shadow: 0 0 10px rgba(236, 72, 153, 0.4); }
    .btn-card { background-color: rgba(14, 165, 233, 0.15); border-color: #0EA5E9 !important; color: #0EA5E9 !important; }
    .btn-card:hover { background-color: rgba(14, 165, 233, 0.25) !important; box-shadow: 0 0 10px rgba(14, 165, 233, 0.4); }
    #confirmTablePaymentModal .modal-footer { 
        border-top: 1px solid #444; 
        padding: 1rem 1.5rem; 
        display: flex; /* Added for centering */
        justify-content: center; 
    }
    .btn-cancel { 
        background-color: rgba(108, 117, 125, 0.15); border-color: #6c757d !important; color: #6c757d !important; 
        font-size: 1rem; padding: 0.75rem 1.5rem; /* Increased padding for better size */
        width: auto; /* Let the content and padding define width */
        min-width: 120px; /* Ensure a minimum decent width */
        max-width: 250px; /* Optional: constrain max width if needed */
    }
    .btn-cancel:hover { 
        background-color: rgba(108, 117, 125, 0.25) !important; 
    }
    .highlighted .card { 
        border: 2px solid #00FF7F;
        box-shadow: 0 0 15px rgba(0, 255, 127, 0.5);
    }
    .loading-placeholder p { color: #777; }

    @media (max-width: 576px) { 
        .header-actions { flex-direction: column; align-items: stretch; }
        .search-bar-container { width: 100%; max-width: 100%; }
        .button-group { flex-direction: column; width: 100%; }
        .btn-cashier-view, .btn-waiter-view { width: 100%; }
        .filter-tabs { justify-content: flex-start; }
        .cashier-actions { flex-direction: column; align-items: stretch; }
        .cashier-actions .btn { width: 100%; }
        .payment-methods { 
            flex-direction: column; 
        }
        .payment-methods > .col-6 { 
             width: 100%; 
             flex-basis: auto; /* Reset flex-basis for stacking */
             max-width: 100%; /* Allow full width when stacked */
             margin-bottom: 0.75rem; 
        }
        .payment-methods > .col-6:last-child {
             margin-bottom: 0;
        }
        .btn-payment-method { 
            height: auto; 
            padding: 0.75rem; 
        }
        .btn-payment-method .payment-icon { 
            width: 32px; 
            height: 32px; 
            margin-bottom: 0.5rem; 
        }
        #confirmTablePaymentModal .modal-dialog { 
            margin: 0.5rem; 
        }
        .btn-cancel {
            width: 100%; /* Full width for cancel button on small screens */
            max-width: none; /* Remove max-width constraint */
        }
    }
</style>

<script>
    // JavaScript code (optimized - comments removed for brevity)
    document.addEventListener("DOMContentLoaded", function () {
        const ID_RISTORANTE_SESS_PAGAMENTI = <?php echo json_encode($id_ristorante_corrente); ?>;
        if (!ID_RISTORANTE_SESS_PAGAMENTI) {
            console.error("CRITICAL: ID_RISTORANTE_SESS_PAGAMENTI non definito.");
            document.body.innerHTML = '<div style="color: red; text-align: center; padding: 20px;">Errore di configurazione: ID Ristorante mancante.</div>';
            return; 
        }
        
        let allTablesData = []; 
        let currentPaymentViewAggregated = localStorage.getItem("currentPaymentViewAggregated") || "waiter";
        let lastTablesHash = ""; 
        let paymentPollingIntervalAggregated;
        const PAYMENT_POLL_RATE_AGGREGATED = 15000;

        const waiterPaymentContAgg = document.getElementById('waiterPaymentsContainer');
        const cashierPaymentContAgg = document.getElementById('cashierPaymentsContainer');
        const waiterLoadingAgg = document.getElementById('waiterPaymentsLoadingPlaceholder');
        const cashierLoadingAgg = document.getElementById('cashierPaymentsLoadingPlaceholder');
        const searchWaiterInputAgg = document.getElementById('searchPaymentInputWaiter');
        const searchCashierInputAgg = document.getElementById('searchPaymentInputCashier');
        const totalPendingAmountElAgg = document.getElementById('totalPendingAmount');
        
        const tablePaymentModalEl = document.getElementById('confirmTablePaymentModal');
        let tablePaymentModalInstance; 
        const modalOverlayPaymentsAgg = document.getElementById('modal-overlay-payments');
        const modalTableNumberSpan = document.getElementById('modalTableNumber');
        const modalTableTotalAmountSpan = document.getElementById('modalTableTotalAmount');
        let currentProcessingTableId = null;

        function escapeHTML(str) {
            if (str === null || typeof str === 'undefined') return '';
            return str.toString().replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[match]));
        }

        function showPaymentNotificationAgg(message, type = 'success') {
            console.log(`NOTIFICATION (${type}): ${message}`);
            const notificationContainer = document.getElementById('notificationContainer');
            if (notificationContainer) {
                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`; 
                toast.textContent = message;
                notificationContainer.appendChild(toast);
                setTimeout(() => { if (toast.parentNode) toast.remove(); }, 5000);
            }
        }

        function showHideLoadingAgg(view, show) {
            const loader = view === 'waiter' ? waiterLoadingAgg : cashierLoadingAgg;
            if (loader) loader.style.display = show ? 'block' : 'none';
        }
        
        function disablePageScrollAgg() { document.body.style.overflow = 'hidden'; }
        function enablePageScrollAgg() { document.body.style.overflow = ''; }

        function getTablePaymentStatusDetailsUI(statusKey) {
            const map = {
                'pending': { icon: 'clock', text: 'Da Pagare', badgeClass: 'status-pending' },
                'processing': { icon: 'nfc', text: 'In Elaborazione', badgeClass: 'status-processing' },
                'completed': { icon: 'check-circle-2', text: 'Pagato', badgeClass: 'status-completed' },
                'failed': { icon: 'x-circle', text: 'Fallito', badgeClass: 'status-failed' }
            };
            return map[statusKey] || { icon: 'alert-circle', text: escapeHTML(statusKey) || 'Sconosciuto', badgeClass: 'status-unknown' };
        }

        function createWaiterTableCardHTML(tableData) {
            const statusDetails = getTablePaymentStatusDetailsUI(tableData.payment_status_ui_tavolo);
            let itemsSummary = tableData.items_tavolo.slice(0, 2).map(item => `${item.quantita}x ${escapeHTML(item.nome_prodotto)}`).join(', ');
            if (tableData.items_tavolo.length > 2) itemsSummary += '...';
            if (tableData.items_tavolo.length === 0) itemsSummary = 'Nessun prodotto specificato.';

            return `
                <div class="col-12 mb-3 payment-card" data-table-id="${tableData.id_tavolo}" data-table-name="${escapeHTML(tableData.numero_tavolo)}" data-status="${tableData.payment_status_ui_tavolo}">
                    <div class="card payment-info-card" onclick="handleWaiterTableCardClick('${tableData.id_tavolo}')">
                        <div class="card-body flex-column p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    Tavolo ${escapeHTML(tableData.numero_tavolo)}
                                    <span class="badge ${statusDetails.badgeClass}">
                                        <i data-lucide="${statusDetails.icon}" class="status-icon"></i>
                                        ${statusDetails.text}
                                    </span>
                                </h5>
                                <div class="d-flex align-items-center price-container">
                                    <span class="price fw-bold">€${tableData.prezzo_totale_tavolo_da_pagare}</span>
                                </div>
                            </div>
                            <ul class="list-unstyled mb-3 payment-list">
                                <li class="d-flex align-items-center">
                                    <i data-lucide="shopping-bag" class="order-icon orange"></i>
                                    ${itemsSummary} (Tot. Ordini: ${tableData.lista_ordini_ids.length})
                                </li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span class="d-flex align-items-center time-text">
                                    <i data-lucide="clock" class="clock-icon"></i>
                                    ${tableData.tempo_trascorso_primo_ordine}
                                </span>
                                <span class="d-flex align-items-center payment-text">
                                    <i data-lucide="hash" class="hash-icon"></i>
                                    Conto Tavolo #${tableData.id_tavolo}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>`;
        }

        function createCashierTableCardHTML(tableData) {
            const statusDetails = getTablePaymentStatusDetailsUI(tableData.payment_status_ui_tavolo);
            let itemsHtml = '';
            tableData.items_tavolo.forEach(item => {
                itemsHtml += `
                    <li class="d-flex align-items-center cashier-payment-item">
                        <i data-lucide="circle" class="payment-icon orange"></i> 
                        <span class="item-name">${item.quantita}x ${escapeHTML(item.nome_prodotto)} (da Ord. #${item.id_ordine_origine})</span>
                    </li>
                `;
            });
            if (tableData.items_tavolo.length === 0) itemsHtml = '<li class="text-muted p-2">Nessun prodotto per questo tavolo.</li>';

            return `
                <div class="col-12 mb-3 cashier-payment-card" data-table-id="${tableData.id_tavolo}" data-table-name="${escapeHTML(tableData.numero_tavolo)}" data-status="${tableData.payment_status_ui_tavolo}">
                    <div class="card cashier-info-card">
                        <div class="card-body flex-column p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0 d-flex align-items-center">
                                    Tavolo ${escapeHTML(tableData.numero_tavolo)} (Conto Tavolo #${tableData.id_tavolo})
                                    <span class="badge ${statusDetails.badgeClass}">
                                        <i data-lucide="${statusDetails.icon}" class="status-icon"></i>
                                        ${statusDetails.text}
                                    </span>
                                </h5>
                                <div class="d-flex align-items-center price-container">
                                    <span class="price fw-bold" id="cashier-table-total-${tableData.id_tavolo}">€${tableData.prezzo_totale_tavolo_da_pagare}</span>
                                </div>
                            </div>
                            <p class="text-muted small">Ordini inclusi: #${tableData.lista_ordini_ids.join(', #')}</p>
                            <ul class="cashier-payment-list mb-3">
                                ${itemsHtml}
                            </ul>
                            <div class="cashier-actions"></div>
                            <div class="d-flex justify-content-between align-items-center text-muted small footer-container">
                                <span class="d-flex align-items-center time-text">
                                    <i data-lucide="clock" class="clock-icon"></i>
                                    ${tableData.tempo_trascorso_primo_ordine}
                                </span>
                                <div class="button-container delete-btn-container" style="display: ${tableData.payment_status_ui_tavolo === 'completed' || tableData.payment_status_ui_tavolo === 'failed' ? 'flex' : 'none'};">
                                    <button class="action-btn delete-image-btn" onclick="handleDeleteCompletedTableCard(this, '${tableData.id_tavolo}')">
                                        <i data-lucide="trash-2" class="action-icon"></i>
                                    </button>
                                </div>
                                <span class="d-flex align-items-center payment-text">
                                    <i data-lucide="hash" class="hash-icon"></i>
                                    Conto Tavolo #${tableData.id_tavolo}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>`;
        }

        async function fetchAndRenderAggregatedPayments(forceFullRender = false) {
            if (!waiterPaymentContAgg || !cashierPaymentContAgg) {
                 showHideLoadingAgg('waiter', false); 
                 showHideLoadingAgg('cashier', false);
                 return;
            }
            showHideLoadingAgg(currentPaymentViewAggregated, true);

            try {
                const response = await fetch('../database/pagamenti/get_pending_payments.php'); 
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const responseText = await response.text();
                let data;
                try { data = JSON.parse(responseText); } 
                catch (e) { 
                    showPaymentNotificationAgg("Errore dati dal server (aggregato).", "error"); 
                    showHideLoadingAgg(currentPaymentViewAggregated, false);
                    return; 
                }

                if (data.success && data.tavoli) { 
                    const newHash = JSON.stringify(data.tavoli);
                    if (newHash === lastTablesHash && !forceFullRender) {
                        showHideLoadingAgg(currentPaymentViewAggregated, false);
                        return; 
                    }
                    lastTablesHash = newHash;
                    
                    allTablesData = data.tavoli.map(tavolo => {
                        const existingTable = allTablesData.find(t => t.id_tavolo.toString() === tavolo.id_tavolo.toString());
                        return {
                            ...tavolo,
                            payment_status_ui_tavolo: existingTable ? existingTable.payment_status_ui_tavolo : 'pending'
                        };
                    });

                    updateTotalPendingAmountAggregated();
                    filterAndRenderAggregatedPayments();
                } else {
                    showPaymentNotificationAgg(data.message || "Errore caricamento pagamenti per tavolo.", "error");
                }
            } catch (error) {
                showPaymentNotificationAgg("Errore connessione server (aggregato).", "error");
            } finally {
                showHideLoadingAgg(currentPaymentViewAggregated, false);
            }
        }

        function renderAggregatedPaymentCards(tablesToDisplay) {
            const waiterFrag = document.createDocumentFragment();
            const cashierFrag = document.createDocumentFragment();

            if (tablesToDisplay.length === 0) {
                const msg = '<p class="text-center text-muted mt-5">Nessun tavolo con pagamenti pendenti.</p>';
                if(waiterPaymentContAgg) waiterPaymentContAgg.innerHTML = msg;
                if(cashierPaymentContAgg) cashierPaymentContAgg.innerHTML = msg;
            } else {
                tablesToDisplay.forEach(tableData => {
                    const waiterCardHTML = createWaiterTableCardHTML(tableData);
                    const cashierCardHTML = createCashierTableCardHTML(tableData);
                    
                    const tempWaiter = document.createElement('div');
                    tempWaiter.innerHTML = waiterCardHTML.trim();
                    if (tempWaiter.firstChild) waiterFrag.appendChild(tempWaiter.firstChild);

                    const tempCashier = document.createElement('div');
                    tempCashier.innerHTML = cashierCardHTML.trim();
                    if (tempCashier.firstChild) cashierFrag.appendChild(tempCashier.firstChild);
                });

                if(waiterPaymentContAgg) {
                    waiterPaymentContAgg.innerHTML = ''; 
                    waiterPaymentContAgg.appendChild(waiterFrag);
                }
                if(cashierPaymentContAgg) {
                    cashierPaymentContAgg.innerHTML = ''; 
                    cashierPaymentContAgg.appendChild(cashierFrag);
                }
            }
            
            document.querySelectorAll('#cashier-payment-view .cashier-payment-card').forEach(updateCashierTablePaymentActions);
            if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        }
        
        function updateTotalPendingAmountAggregated() {
            const total = allTablesData
                .filter(t => t.payment_status_ui_tavolo === 'pending' || t.payment_status_ui_tavolo === 'processing')
                .reduce((sum, t) => sum + parseFloat(t.prezzo_totale_tavolo_da_pagare || 0), 0);
            if (totalPendingAmountElAgg) totalPendingAmountElAgg.textContent = `€${total.toFixed(2)}`;
        }

        function filterAndRenderAggregatedPayments() {
            let dataToRender = [...allTablesData];
            const activeFilterTab = document.querySelector(`#${currentPaymentViewAggregated}-payment-view .filter-tab.active`);
            const searchInputEl = document.getElementById(currentPaymentViewAggregated === 'waiter' ? 'searchPaymentInputWaiter' : 'searchPaymentInputCashier');
            const searchTerm = searchInputEl ? searchInputEl.value.toLowerCase().trim() : "";

            if (searchTerm) {
                dataToRender = dataToRender.filter(t =>
                    (t.numero_tavolo && t.numero_tavolo.toString().toLowerCase().includes(searchTerm)) ||
                    t.id_tavolo.toString().includes(searchTerm) 
                );
            }
            if (activeFilterTab) {
                const statusFilter = activeFilterTab.dataset.statusFilter;
                if (statusFilter !== 'all') {
                    dataToRender = dataToRender.filter(t => t.payment_status_ui_tavolo === statusFilter);
                }
            }
            renderAggregatedPaymentCards(dataToRender);
        }
        
        window.showCashierPaymentView = function() { 
            currentPaymentViewAggregated = "cashier";
            const waiterV = document.getElementById("waiter-payment-view");
            const cashierV = document.getElementById("cashier-payment-view");
            if (waiterV) waiterV.style.display = "none";
            if (cashierV) cashierV.style.display = "block";
            localStorage.setItem("currentPaymentViewAggregated", "cashier");
            filterAndRenderAggregatedPayments();
        }
        window.showWaiterPaymentView = function() { 
            currentPaymentViewAggregated = "waiter";
            const waiterV = document.getElementById("waiter-payment-view");
            const cashierV = document.getElementById("cashier-payment-view");
            if (cashierV) cashierV.style.display = "none";
            if (waiterV) waiterV.style.display = "block";
            localStorage.setItem("currentPaymentViewAggregated", "waiter");
            filterAndRenderAggregatedPayments();
        }
        
        window.handleWaiterTableCardClick = function(tableId) {
            const tableData = allTablesData.find(t => t.id_tavolo.toString() === tableId.toString());
            if (tableData && tableData.payment_status_ui_tavolo === 'pending') {
                tableData.payment_status_ui_tavolo = 'processing'; 
            }
            localStorage.setItem("selectedTableIdForCashier", tableId);
            showCashierPaymentView();
            setTimeout(() => {
                const targetCard = cashierPaymentContAgg.querySelector(`.cashier-payment-card[data-table-id="${tableId}"]`);
                if (targetCard) {
                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    targetCard.classList.add('highlighted');
                    setTimeout(() => targetCard.classList.remove('highlighted'), 2000);
                    updateCashierTablePaymentActions(targetCard); 
                }
            }, 100);
        }

        window.updateCashierTablePaymentActions = function(cardElement) {
            if (!cardElement) return;
            const status = cardElement.dataset.status; 
            const actionsContainer = cardElement.querySelector('.cashier-actions');
            const deleteBtnCont = cardElement.querySelector('.delete-btn-container');
            if (!actionsContainer) return;

            actionsContainer.innerHTML = ''; 
            if (deleteBtnCont) deleteBtnCont.style.display = 'none';

            if (status === 'pending') {
                actionsContainer.innerHTML = `<button class="btn btn-start-payment" onclick="handleStartTablePayment('${cardElement.dataset.tableId}')"><i data-lucide="play" class="action-icon"></i> Inizia Pagamento Tavolo</button>`;
            } else if (status === 'processing') {
                actionsContainer.innerHTML = `
                    <button class="btn btn-confirm-payment" onclick="handleOpenTablePaymentModal('${cardElement.dataset.tableId}')">
                        <i data-lucide="credit-card" class="action-icon"></i> Conferma Pagamento Tavolo
                    </button>`;
            } else if (status === 'failed') {
                actionsContainer.innerHTML = `<button class="btn btn-retry-payment" onclick="handleRetryTablePayment('${cardElement.dataset.tableId}')"><i data-lucide="refresh-cw" class="action-icon"></i> Riprova Pagamento Tavolo</button>`;
                 if (deleteBtnCont) deleteBtnCont.style.display = 'flex';
            } else if (status === 'completed') {
                actionsContainer.innerHTML = `<p class="text-muted small m-0 text-center w-100"><i data-lucide="check-circle-2" class="action-icon green me-1"></i>Pagamento Tavolo Completato</p>`;
                if (deleteBtnCont) deleteBtnCont.style.display = 'flex';
            }
            if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        }
        
        window.handleStartTablePayment = function(tableId) {
            const table = allTablesData.find(t => t.id_tavolo.toString() === tableId);
            if (table && table.payment_status_ui_tavolo === 'pending') {
                table.payment_status_ui_tavolo = 'processing';
                const card = cashierPaymentContAgg.querySelector(`.cashier-payment-card[data-table-id="${tableId}"]`);
                if (card) {
                    card.dataset.status = 'processing'; 
                    const badge = card.querySelector('.badge');
                    const statusDetails = getTablePaymentStatusDetailsUI('processing');
                    if(badge) {
                        badge.className = `badge ${statusDetails.badgeClass}`;
                        badge.innerHTML = `<i data-lucide="${statusDetails.icon}" class="status-icon"></i> ${statusDetails.text}`;
                    }
                    updateCashierTablePaymentActions(card);
                }
                updateTotalPendingAmountAggregated(); 
            }
        }

        window.handleOpenTablePaymentModal = function(tableId) {
            const tableData = allTablesData.find(t => t.id_tavolo.toString() === tableId);
            if (!tableData) return;
            currentProcessingTableId = tableId; 
            if (modalTableNumberSpan) modalTableNumberSpan.textContent = tableData.numero_tavolo;
            if (modalTableTotalAmountSpan) modalTableTotalAmountSpan.textContent = `€${tableData.prezzo_totale_tavolo_da_pagare}`;
            
            if (tablePaymentModalInstance) tablePaymentModalInstance.show();
            if (modalOverlayPaymentsAgg) modalOverlayPaymentsAgg.style.display = 'block';
            disablePageScrollAgg();
        }
        
        window.processTablePaymentConfirmation = async function(paymentMethod) {
            if (!currentProcessingTableId) return;
            const tableIdToProcess = currentProcessingTableId;
            currentProcessingTableId = null; 

            const table = allTablesData.find(t => t.id_tavolo.toString() === tableIdToProcess);
            if (!table) {
                showPaymentNotificationAgg(`Tavolo #${tableIdToProcess} non trovato.`, "error");
                if (tablePaymentModalInstance) tablePaymentModalInstance.hide();
                if (modalOverlayPaymentsAgg) modalOverlayPaymentsAgg.style.display = 'none';
                enablePageScrollAgg();
                return;
            }
            
            const paymentSucceeded = Math.random() > 0.05; 

            const formData = new FormData();
            formData.append('id_tavolo', tableIdToProcess); 
            formData.append('payment_method', paymentMethod);

            let apiSuccess = false;
            if (paymentSucceeded) {
                formData.append('action', 'confirm_payment_tavolo'); 
                apiSuccess = await sendTablePaymentAPIRequest(formData, `Pagamento per tavolo #${tableIdToProcess} confermato.`, 'Conferma Pag. Tavolo');
                if (apiSuccess) {
                    table.payment_status_ui_tavolo = 'completed';
                } else {
                    table.payment_status_ui_tavolo = 'failed';
                }
            } else {
                showPaymentNotificationAgg(`Pagamento per tavolo #${tableIdToProcess} fallito (simulato).`, "error");
                table.payment_status_ui_tavolo = 'failed';
            }
            
            const card = cashierPaymentContAgg.querySelector(`.cashier-payment-card[data-table-id="${tableIdToProcess}"]`);
            if (card) {
                card.dataset.status = table.payment_status_ui_tavolo;
                const badge = card.querySelector('.badge');
                const statusDetails = getTablePaymentStatusDetailsUI(table.payment_status_ui_tavolo);
                if(badge) {
                    badge.className = `badge ${statusDetails.badgeClass}`;
                    badge.innerHTML = `<i data-lucide="${statusDetails.icon}" class="status-icon"></i> ${statusDetails.text}`;
                }
                updateCashierTablePaymentActions(card);
            }

            if (tablePaymentModalInstance) tablePaymentModalInstance.hide();
            if (modalOverlayPaymentsAgg) modalOverlayPaymentsAgg.style.display = 'none';
            enablePageScrollAgg();
            updateTotalPendingAmountAggregated();
            if (apiSuccess || paymentSucceeded) {
                 fetchAndRenderAggregatedPayments(true); 
            }
        }

        async function sendTablePaymentAPIRequest(formData, successMsg, errorPrefix) {
            try {
                const response = await fetch('../database/pagamenti/process_order_payment.php', { 
                    method: 'POST',
                    body: formData
                });
                const responseText = await response.text();
                let data;
                try { data = JSON.parse(responseText); }
                catch (e) { console.error("JSON Parse Error:", e, responseText); showPaymentNotificationAgg(`Errore server (${errorPrefix}).`, "error"); return false; }

                if (data.success) {
                    showPaymentNotificationAgg(data.message || successMsg, 'success');
                    return true;
                } else {
                    showPaymentNotificationAgg(data.message || `Errore API (${errorPrefix}).`, 'error');
                    return false;
                }
            } catch (error) {
                console.error(`Fetch Error (${errorPrefix}):`, error);
                showPaymentNotificationAgg(`Errore connessione (${errorPrefix}).`, 'error');
                return false;
            }
        }

        window.handleRetryTablePayment = function(tableId) {
            const table = allTablesData.find(t => t.id_tavolo.toString() === tableId);
            if (table && table.payment_status_ui_tavolo === 'failed') {
                table.payment_status_ui_tavolo = 'processing'; 
                const card = cashierPaymentContAgg.querySelector(`.cashier-payment-card[data-table-id="${tableId}"]`);
                if (card) {
                    card.dataset.status = 'processing';
                     const badge = card.querySelector('.badge');
                    const statusDetails = getTablePaymentStatusDetailsUI('processing');
                    if(badge) {
                        badge.className = `badge ${statusDetails.badgeClass}`;
                        badge.innerHTML = `<i data-lucide="${statusDetails.icon}" class="status-icon"></i> ${statusDetails.text}`;
                    }
                    updateCashierTablePaymentActions(card);
                }
            }
        }
        
        window.handleDeleteCompletedTableCard = function(buttonElement, tableId) {
            const card = buttonElement.closest('.cashier-payment-card');
             if (card && (card.dataset.status === 'completed' || card.dataset.status === 'failed')) {
                allTablesData = allTablesData.filter(t => t.id_tavolo.toString() !== tableId);
                card.remove(); 
                showPaymentNotificationAgg(`Pagamento tavolo #${tableId} rimosso dalla vista.`, 'success');
                updateTotalPendingAmountAggregated(); 
            }
        }
        
        function setupFilterTabs(viewType) {
            const tabsContainerId = `${viewType}PaymentFilterTabs`; 
            const tabsContainer = document.getElementById(tabsContainerId);
            if (tabsContainer) {
                tabsContainer.addEventListener('click', (e) => {
                    if (e.target.classList.contains('filter-tab')) {
                        tabsContainer.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
                        e.target.classList.add('active');
                        filterAndRenderAggregatedPayments(); 
                    }
                });
            }
        }

        if(searchWaiterInputAgg) searchWaiterInputAgg.addEventListener('keyup', filterAndRenderAggregatedPayments);
        if(searchCashierInputAgg) searchCashierInputAgg.addEventListener('keyup', filterAndRenderAggregatedPayments);

        if (tablePaymentModalEl) { 
            tablePaymentModalInstance = new bootstrap.Modal(tablePaymentModalEl);
            tablePaymentModalEl.addEventListener('hidden.bs.modal', function () {
                if (modalOverlayPaymentsAgg) modalOverlayPaymentsAgg.style.display = 'none';
                enablePageScrollAgg();
                currentProcessingTableId = null; 
            });
        }
        
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                if (tablePaymentModalEl && tablePaymentModalEl.classList.contains('show') && tablePaymentModalInstance) {
                     tablePaymentModalInstance.hide(); 
                }
            }
        });
        
        showHideLoadingAgg(currentPaymentViewAggregated, true);
        fetchAndRenderAggregatedPayments(true); 
        if (paymentPollingIntervalAggregated) clearInterval(paymentPollingIntervalAggregated);
        paymentPollingIntervalAggregated = setInterval(() => fetchAndRenderAggregatedPayments(false), PAYMENT_POLL_RATE_AGGREGATED);

        setupFilterTabs('waiter'); 
        setupFilterTabs('cashier');
        
        if (currentPaymentViewAggregated === "cashier") {
            showCashierPaymentView();
        } else {
            showWaiterPaymentView();
        }
    });
</script>
<script src="assets/js/menu.js"></script>
<script src="assets/script.js"></script>
