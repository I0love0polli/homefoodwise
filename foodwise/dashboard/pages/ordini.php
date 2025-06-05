<?php
// session_start(); // Assicurati che la sessione sia già avviata da un file genitore (es. index.php del dashboard)
// Se questo file viene caricato via AJAX in un contesto dove la sessione è già attiva, non serve riavviarla.
// Se è un punto di ingresso diretto, la sessione deve essere avviata.

// Verifica autenticazione - FONDAMENTALE PER LA PRODUZIONE
if (!isset($_SESSION['login_restaurant']) || empty($_SESSION['login_restaurant'])) {
    // Gestisci il caso di utente non autenticato. Potrebbe essere un redirect o un messaggio di errore.
    // Per ora, se la sessione non esiste (es. accesso diretto al file), potremmo voler impedire l'esecuzione. 
    header('Location: /login.php'); // Esempio di redirect
    exit;
    // OPPURE, se questo file è sempre incluso in una pagina protetta:
    // die("Accesso non autorizzato."); 
    // Per lo sviluppo, potremmo temporaneamente impostarla, MA RICORDA DI RIMUOVERLO
}
$id_ristorante_corrente = $_SESSION['login_restaurant'] ?? null; // Usa null coalescing per evitare errori se la sessione non è impostata

if (!$id_ristorante_corrente) {
    // Se dopo il controllo sopra, non abbiamo un ID ristorante, qualcosa è andato storto.
    // Potrebbe essere utile loggare questo caso o mostrare un errore specifico.
    // Per ora, usciamo per sicurezza se non c'è un ID ristorante.
    die("Errore: ID ristorante non disponibile. Sessione non valida o scaduta.");
}
?>
<link rel="stylesheet" href="assets/css/ordini_styles.css">

<div class="container ordini-container">
    <div id="waiter-view" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-12 mb-4">
                <div class="header-actions mb-4">
                    <div class="search-bar-container">
                        <div class="search-bar">
                            <input type="text" id="searchInputWaiter" class="form-control" placeholder="Cerca per Tavolo o ID Ordine..."
                                onkeyup="searchOrdersWaiter()">
                            <i data-lucide="search" class="search-icon"></i>
                        </div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-kitchen-view" onclick="showKitchenView()">
                            <i data-lucide="cooking-pot" class="kitchen-icon"></i>Vista Cucina
                        </button>
                    </div>
                </div>
                <div class="filter-tabs mb-4" id="waiterFilterTabs">
                    <button class="filter-tab active" data-status-filter="all">Tutti</button>
                    <button class="filter-tab" data-status-filter="pending">In attesa</button>
                    <button class="filter-tab" data-status-filter="preparing">In preparazione</button>
                    <button class="filter-tab" data-status-filter="ready">Pronto</button>
                    <button class="filter-tab" data-status-filter="served">Servito</button> 
                </div>
            </div>
        </div>
        <div class="row g-3" id="waiterOrdersContainer">
            <div class="loading-placeholder text-center mt-5" id="waiterLoadingPlaceholder">
                <p>Caricamento ordini...</p>
                </div>
        </div>
    </div>

    <div id="kitchen-view" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-12 mb-4">
                <div class="header-actions mb-4">
                    <div class="search-bar-container">
                        <div class="search-bar">
                            <input type="text" id="searchInputKitchen" class="form-control" placeholder="Cerca per Tavolo o ID Ordine..."
                                onkeyup="searchOrdersKitchen()">
                            <i data-lucide="search" class="search-icon"></i>
                        </div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-waiter-view" onclick="showWaiterView()">
                            <i data-lucide="hand-platter" class="waiter-icon"></i>Vista Cameriere
                        </button>
                    </div>
                </div>
                <div class="filter-tabs mb-4" id="kitchenFilterTabs">
                    <button class="filter-tab active" data-status-filter="all">Tutti</button>
                    <button class="filter-tab" data-status-filter="pending">In attesa</button>
                    <button class="filter-tab" data-status-filter="preparing">In preparazione</button>
                    <button class="filter-tab" data-status-filter="ready">Pronto</button>
                    <button class="filter-tab" data-status-filter="served">Servito</button> 
                </div>
            </div>
        </div>
        <div class="row g-3" id="kitchenOrdersContainer">
            <div class="loading-placeholder text-center mt-5" id="kitchenLoadingPlaceholder">
                <p>Caricamento ordini...</p>
                </div>
        </div>
    </div>
</div>

<div id="confirmationModal" class="modal-ordini" style="display:none;">
  <div class="modal-ordini-content">
    <span class="modal-ordini-close-button">&times;</span>
    <p id="confirmationModalMessage"></p>
    <div class="modal-ordini-actions">
        <button id="confirmModalButton" class="btn btn-danger">Conferma</button>
        <button id="cancelModalButton" class="btn btn-secondary">Annulla</button>
    </div>
  </div>
</div>

<div id="notificationContainer" class="notification-container"></div>


<script>
    // Passa l'ID del ristorante da PHP a JavaScript in modo sicuro
    // Questo ID è fondamentale per le chiamate API e altre logiche.
    // Assicurati che $id_ristorante_corrente sia sempre valorizzato correttamente.
    const ID_RISTORANTE_CORRENTE_ORDINI = <?php echo json_encode($id_ristorante_corrente); ?>;
    if (!ID_RISTORANTE_CORRENTE_ORDINI) {
        console.error("ID Ristorante non disponibile in JavaScript. Verifica la sessione PHP.");
        // Potresti voler mostrare un errore all'utente o bloccare ulteriori esecuzioni.
        document.body.innerHTML = "<p class='text-danger text-center p-5'>Errore critico: Configurazione ristorante mancante. Contattare l'assistenza.</p>";
    }
</script>
<script src="assets/js/ordini_scripts.js" defer></script>
<script src="assets/script.js"></script>