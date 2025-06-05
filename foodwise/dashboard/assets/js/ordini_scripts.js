// Assicurati che ID_RISTORANTE_CORRENTE_ORDINI sia definito in ordini.php prima di questo script
if (typeof ID_RISTORANTE_CORRENTE_ORDINI === 'undefined' || !ID_RISTORANTE_CORRENTE_ORDINI) {
    console.error("CRITICAL: ID_RISTORANTE_CORRENTE_ORDINI non è definito. Lo script non può funzionare.");
    // Potresti voler mostrare un messaggio di errore all'utente o disabilitare la funzionalità.
    document.body.innerHTML = "<p style='color:red; text-align:center; padding: 20px;'>Errore di configurazione critico. Contattare l'amministratore.</p>";
    // Lancia un errore per fermare l'esecuzione ulteriore dello script se questo è fatale.
    throw new Error("ID_RISTORANTE_CORRENTE_ORDINI non definito.");
}

let allOrdersDataGlobal = [];
let currentView = localStorage.getItem("currentViewOrders") || "waiter";
let lastOrdersHash = ""; // Per il polling intelligente (opzionale, implementazione base)
let pollingIntervalId = null;
const POLLING_RATE = 15000; // Millisecondi (15 secondi)

// Elementi del DOM
const waiterContainer = document.getElementById('waiterOrdersContainer');
const kitchenContainer = document.getElementById('kitchenOrdersContainer');
const waiterLoadingPlaceholder = document.getElementById('waiterLoadingPlaceholder');
const kitchenLoadingPlaceholder = document.getElementById('kitchenLoadingPlaceholder');
const waiterFilterTabs = document.getElementById('waiterFilterTabs');
const kitchenFilterTabs = document.getElementById('kitchenFilterTabs');
const searchInputWaiter = document.getElementById('searchInputWaiter');
const searchInputKitchen = document.getElementById('searchInputKitchen');

// Modale e Notifiche
const confirmationModal = document.getElementById('confirmationModal');
const confirmationModalMessage = document.getElementById('confirmationModalMessage');
const confirmModalButton = document.getElementById('confirmModalButton');
const cancelModalButton = document.getElementById('cancelModalButton');
const modalCloseButton = confirmationModal ? confirmationModal.querySelector('.modal-ordini-close-button') : null;
let confirmCallback = null;

const notificationContainer = document.getElementById('notificationContainer');


// --- Funzioni di Notifica e Modale ---
function showNotification(message, type = 'success') { // types: success, error, warning
    if (!notificationContainer) return;

    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.textContent = message;
    notificationContainer.appendChild(toast);

    // Mostra e poi nascondi
    setTimeout(() => {
        toast.classList.add('show');
    }, 10); // Leggero ritardo per permettere il rendering iniziale

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) { // Verifica se l'elemento è ancora nel DOM
                 toast.remove();
            }
        }, 500); // Tempo per l'animazione di uscita
    }, 5000); // Durata della notifica
}

function openConfirmationModal(message, onConfirm) {
    if (!confirmationModal || !confirmationModalMessage) return;
    confirmationModalMessage.textContent = message;
    confirmCallback = onConfirm;
    confirmationModal.style.display = 'block';
}

function closeModal() {
    if (!confirmationModal) return;
    confirmationModal.style.display = 'none';
    confirmCallback = null;
}

if (confirmModalButton) {
    confirmModalButton.addEventListener('click', () => {
        if (typeof confirmCallback === 'function') {
            confirmCallback();
        }
        closeModal();
    });
}
if (cancelModalButton) cancelModalButton.addEventListener('click', closeModal);
if (modalCloseButton) modalCloseButton.addEventListener('click', closeModal);
window.addEventListener('click', (event) => {
    if (event.target === confirmationModal) {
        closeModal();
    }
});


// --- Funzioni Helper per UI ---
function getOrderStatusDetails(statusKey) {
    const statusMap = {
        'pending': { icon: 'clock', text: 'In attesa', badgeClass: 'status-pending' },
        'preparing': { icon: 'chef-hat', text: 'In preparazione', badgeClass: 'status-preparing' },
        'ready': { icon: 'check-circle', text: 'Pronto', badgeClass: 'status-ready' },
        'served': { icon: 'check-circle-2', text: 'Servito', badgeClass: 'status-served' }, // Icona diversa per servito
        'cancelled': { icon: 'x-circle', text: 'Annullato', badgeClass: 'status-cancelled' }
    };
    return statusMap[statusKey] || { icon: 'alert-circle', text: statusKey ? statusKey.charAt(0).toUpperCase() + statusKey.slice(1) : 'Sconosciuto', badgeClass: 'status-unknown' };
}

function getItemStatusIcon(statusKey) {
    const itemStatusMap = {
        'pending': { icon: 'circle-alert', colorClass: 'orange' }, // Lucide icon name
        'preparing': { icon: 'loader-circle', colorClass: 'blue' }, 
        'ready': { icon: 'circle-check', colorClass: 'green' },
        'completed': { icon: 'circle-check', colorClass: 'green' } // Usato internamente per UI
    };
    return itemStatusMap[statusKey] || { icon: 'circle-alert', colorClass: 'orange' };
}

// --- Creazione Card ---
function createWaiterOrderCard(order) {
    const statusDetails = getOrderStatusDetails(order.stato_ordine);
    let itemsHtml = '';
    if (order.items && order.items.length > 0) {
        order.items.forEach(item => {
            const itemDone = item.stato_item === 'ready' || item.stato_item === 'completed';
            const itemIconDetails = getItemStatusIcon(itemDone ? 'completed' : item.stato_item);
            itemsHtml += `
                <li class="d-flex align-items-center ${itemDone ? 'item-completed-waiter' : ''}">
                    <i data-lucide="${itemIconDetails.icon}" class="order-icon ${itemIconDetails.colorClass}"></i>
                    ${item.quantita}x ${item.nome_prodotto}
                    ${item.note_riga ? `<small class="text-muted fst-italic ms-2">(${escapeHtml(item.note_riga)})</small>` : ''}
                </li>
            `;
        });
    } else {
        itemsHtml = '<li class="text-muted">Nessun prodotto specificato.</li>';
    }

    return `
        <div class="col-12 mb-3 order-card" data-order-id="${order.id_ordine}" data-table-name="${escapeHtml(order.numero_tavolo)}" data-status="${order.stato_ordine}">
            <div class="card restaurant-info-card">
                <div class="card-body flex-column p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            Tavolo ${escapeHtml(order.numero_tavolo)}
                            <span class="badge ${statusDetails.badgeClass}">
                                <i data-lucide="${statusDetails.icon}" class="status-icon"></i>
                                ${statusDetails.text}
                            </span>
                        </h5>
                        <div class="d-flex align-items-center price-container">
                            <span class="price fw-bold">€${order.prezzo_totale_calcolato}</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-3 order-list">
                        ${itemsHtml}
                    </ul>
                    <div class="progress progress-bar-wrapper mb-1">
                        <div class="progress-bar" role="progressbar" style="width: ${order.percentuale_completamento}%;" aria-valuenow="${order.percentuale_completamento}"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-end text-muted small mb-2 completion-text">
                        ${order.percentuale_completamento}% completato
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small footer-container">
                        <span class="d-flex align-items-center time-text">
                            <i data-lucide="clock" class="clock-icon"></i>
                            ${order.tempo_trascorso} 
                        </span>
                        <span class="d-flex align-items-center order-text">
                            <i data-lucide="hash" class="hash-icon"></i>
                            Ordine ${order.id_ordine}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function createKitchenOrderCard(order) {
    const statusDetails = getOrderStatusDetails(order.stato_ordine);
    let itemsHtml = '';
    if (order.items && order.items.length > 0) {
        order.items.forEach(item => {
            const itemIconDetails = getItemStatusIcon(item.stato_item);
            const isLocked = order.stato_ordine === 'pending' || order.stato_ordine === 'served' || order.stato_ordine === 'cancelled' || (order.stato_ordine === 'ready' && item.stato_item === 'ready');
            itemsHtml += `
                <li class="d-flex align-items-center kitchen-order-item 
                    ${item.stato_item === 'ready' || item.stato_item === 'completed' ? 'completed' : ''} 
                    ${isLocked ? 'locked' : ''}" 
                    data-item-id="${item.id_dettaglio_ordine}" 
                    data-item-status="${item.stato_item}" 
                    onclick="toggleItemStatus(this, ${order.id_ordine})">
                    <i data-lucide="${itemIconDetails.icon}" class="order-icon ${itemIconDetails.colorClass}"></i>
                    <span class="item-name">${item.quantita}x ${escapeHtml(item.nome_prodotto)}</span>
                     ${item.note_riga ? `<small class="text-muted fst-italic ms-2">(${escapeHtml(item.note_riga)})</small>` : ''}
                </li>
            `;
        });
    } else {
        itemsHtml = '<li class="text-muted p-2">Nessun prodotto.</li>';
    }
    
    return `
        <div class="col-12 mb-3 kitchen-order-card" data-order-id="${order.id_ordine}" data-table-name="${escapeHtml(order.numero_tavolo)}" data-status="${order.stato_ordine}">
            <div class="card kitchen-info-card">
                <div class="card-body flex-column p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            Tavolo ${escapeHtml(order.numero_tavolo)}
                            <span class="badge ${statusDetails.badgeClass}">
                                <i data-lucide="${statusDetails.icon}" class="status-icon"></i>
                                ${statusDetails.text}
                            </span>
                        </h5>
                        <div class="d-flex align-items-center price-container">
                            <span class="price fw-bold">€${order.prezzo_totale_calcolato}</span>
                        </div>
                    </div>
                    <ul class="kitchen-order-list mb-3">
                        ${itemsHtml}
                    </ul>
                    <div class="kitchen-actions">
                        <!-- I pulsanti verranno aggiornati da updateKitchenActions -->
                    </div>
                    <div class="mt-2 d-flex justify-content-between align-items-center text-muted small footer-container">
                        <span class="d-flex align-items-center time-text">
                            <i data-lucide="clock" class="clock-icon"></i>
                            ${order.tempo_trascorso}
                        </span>
                        <div class="button-container delete-btn-container" style="display: none;">
                            <button class="action-btn delete-image-btn" onclick="confirmDeleteOrder(${order.id_ordine})">
                                <i data-lucide="trash-2" class="action-icon"></i>
                            </button>
                        </div>
                        <span class="d-flex align-items-center order-text">
                            <i data-lucide="hash" class="hash-icon"></i>
                            Ordine ${order.id_ordine}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function escapeHtml(unsafe) {
    if (unsafe === null || typeof unsafe === 'undefined') return '';
    return unsafe
         .toString()
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

// --- Logica di Caricamento e Rendering Ordini ---
async function loadOrders(forceFullRender = false) {
    if (!waiterContainer || !kitchenContainer) {
        console.error("Elementi contenitore ordini non trovati nel DOM.");
        return;
    }
    if (currentView === 'waiter' && waiterLoadingPlaceholder) waiterLoadingPlaceholder.style.display = 'block';
    if (currentView === 'kitchen' && kitchenLoadingPlaceholder) kitchenLoadingPlaceholder.style.display = 'block';

    try {
        // Per un polling intelligente, potresti inviare `lastOrdersHash` o un timestamp
        // const response = await fetch(`../database/ordini/get_ordini.php?lastHash=${lastOrdersHash}`);
        const response = await fetch('../database/ordini/get_ordini.php'); 
        const responseText = await response.text();
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (jsonError) {
            console.error('Errore parsing JSON da get_ordini.php:', jsonError);
            console.error('Testo risposta server:', responseText);
            showNotification('Errore nel formato dati dal server.', 'error');
            if (waiterLoadingPlaceholder) waiterLoadingPlaceholder.style.display = 'none';
            if (kitchenLoadingPlaceholder) kitchenLoadingPlaceholder.style.display = 'none';
            return;
        }

        if (data.success) {
            // Semplice controllo hash per vedere se i dati sono cambiati (per ridurre re-render non necessari)
            const newHash = JSON.stringify(data.ordini); // Semplice hash, per produzioni complesse usare algo più robusto
            if (newHash === lastOrdersHash && !forceFullRender) {
                if (waiterLoadingPlaceholder) waiterLoadingPlaceholder.style.display = 'none';
                if (kitchenLoadingPlaceholder) kitchenLoadingPlaceholder.style.display = 'none';
                return; // Nessun cambiamento, non fare nulla
            }
            lastOrdersHash = newHash;
            allOrdersDataGlobal = data.ordini;
            applyCurrentFiltersAndRender(); // Chiama la funzione che filtra e poi renderizza
        } else {
            console.error('Errore API caricamento ordini:', data.message);
            showNotification(data.message || 'Errore caricamento ordini.', 'error');
        }
    } catch (fetchError) {
        console.error('Errore Fetch caricamento ordini:', fetchError);
        showNotification('Impossibile connettersi al server.', 'error');
    } finally {
        if (waiterLoadingPlaceholder) waiterLoadingPlaceholder.style.display = 'none';
        if (kitchenLoadingPlaceholder) kitchenLoadingPlaceholder.style.display = 'none';
    }
}

function renderOrders(ordersToRender) {
    const fragmentWaiter = document.createDocumentFragment();
    const fragmentKitchen = document.createDocumentFragment();

    if (ordersToRender.length === 0) {
        const noOrdersMsg = '<p class="text-center text-muted mt-5">Nessun ordine da visualizzare.</p>';
        waiterContainer.innerHTML = noOrdersMsg;
        kitchenContainer.innerHTML = noOrdersMsg;
    } else {
        ordersToRender.forEach(order => {
            // Creazione elementi DOM invece di innerHTML += per performance e sicurezza
            const tempWaiterDiv = document.createElement('div');
            tempWaiterDiv.innerHTML = createWaiterOrderCard(order);
            fragmentWaiter.appendChild(tempWaiterDiv.firstElementChild);

            const tempKitchenDiv = document.createElement('div');
            tempKitchenDiv.innerHTML = createKitchenOrderCard(order);
            fragmentKitchen.appendChild(tempKitchenDiv.firstElementChild);
        });
        waiterContainer.innerHTML = ''; // Pulisci prima di aggiungere
        kitchenContainer.innerHTML = '';
        waiterContainer.appendChild(fragmentWaiter);
        kitchenContainer.appendChild(fragmentKitchen);
    }
    
    document.querySelectorAll('.kitchen-order-card').forEach(card => updateKitchenActions(card));
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
    } else {
        console.warn("Libreria Lucide Icons non trovata o createIcons non è una funzione.");
    }
}

// --- Logica Filtri e Ricerca ---
function applyCurrentFiltersAndRender() {
    let filteredData = [...allOrdersDataGlobal]; // Copia per non modificare l'originale

    const activeFilterTab = document.querySelector(`#${currentView}FilterTabs .filter-tab.active`);
    const searchTerm = (currentView === 'waiter' ? searchInputWaiter.value : searchInputKitchen.value).toLowerCase().trim();

    if (searchTerm) {
        filteredData = filteredData.filter(order => 
            order.id_ordine.toString().includes(searchTerm) || 
            (order.numero_tavolo && order.numero_tavolo.toString().toLowerCase().includes(searchTerm))
        );
    }
    
    if (activeFilterTab) {
        const statusFilter = activeFilterTab.getAttribute('data-status-filter');
        if (statusFilter !== 'all') {
            filteredData = filteredData.filter(order => order.stato_ordine === statusFilter);
        }
    }
    renderOrders(filteredData);
}

function setupFilterTabs(viewPrefix) {
    const filterTabsContainer = document.getElementById(`${viewPrefix}FilterTabs`);
    if (filterTabsContainer) {
        filterTabsContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('filter-tab')) {
                filterTabsContainer.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
                event.target.classList.add('active');
                applyCurrentFiltersAndRender();
            }
        });
    }
}


// --- Gestione Viste (Cameriere/Cucina) ---
function showView(viewToShow) {
    currentView = viewToShow;
    document.getElementById("waiter-view").style.display = (viewToShow === "waiter") ? "block" : "none";
    document.getElementById("kitchen-view").style.display = (viewToShow === "kitchen") ? "block" : "none";
    localStorage.setItem("currentViewOrders", viewToShow);
    applyCurrentFiltersAndRender(); // Applica filtri e renderizza per la nuova vista
    if (typeof lucide !== 'undefined') lucide.createIcons(); // Ricarica icone se necessario
}

window.showKitchenView = () => showView("kitchen");
window.showWaiterView = () => showView("waiter");


// --- Azioni sugli Ordini e Item (Chiamate API) ---
async function sendOrderUpdateRequest(formData, successMessage, errorMessagePrefix) {
    try {
        const response = await fetch('../database/ordini/update_ordine_stato.php', {
            method: 'POST',
            body: formData
        });
        const responseText = await response.text();
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (jsonError) {
            console.error(`Errore parsing JSON (${errorMessagePrefix}):`, jsonError, responseText);
            showNotification(`Errore formato risposta server (${errorMessagePrefix}).`, 'error');
            return false;
        }

        if (data.success) {
            showNotification(data.message || successMessage, 'success');
            loadOrders(true); // Forza re-render completo dopo un'azione
            return true;
        } else {
            console.error(`Errore API (${errorMessagePrefix}):`, data.message);
            showNotification(data.message || `Errore API (${errorMessagePrefix}).`, 'error');
            return false;
        }
    } catch (fetchError) {
        console.error(`Errore Fetch (${errorMessagePrefix}):`, fetchError);
        showNotification(`Errore comunicazione server (${errorMessagePrefix}).`, 'error');
        return false;
    }
}

async function updateOrderStatus(idOrdine, nuovoStato) {
    const formData = new FormData();
    formData.append('action', 'update_status_ordine');
    formData.append('id_ordine', idOrdine);
    formData.append('nuovo_stato', nuovoStato);
    sendOrderUpdateRequest(formData, 'Stato ordine aggiornato.', 'Agg. Stato Ordine');
}

async function updateItemStatus(idDettaglioOrdine, idOrdine, nuovoStato) {
    const formData = new FormData();
    formData.append('action', 'update_status_item');
    formData.append('id_dettaglio_ordine', idDettaglioOrdine);
    formData.append('id_ordine', idOrdine); 
    formData.append('nuovo_stato', nuovoStato);
    sendOrderUpdateRequest(formData, 'Stato item aggiornato.', 'Agg. Stato Item');
}

async function updateMultipleItemsStatus(idOrdine, itemIds, nuovoStatoAzione) {
    if (!itemIds || itemIds.length === 0) {
        showNotification('Nessun item selezionato per l\'aggiornamento.', 'warning');
        return;
    }
    const formData = new FormData();
    formData.append('action', nuovoStatoAzione); // es. 'mark_all_complete'
    formData.append('id_ordine', idOrdine);
    formData.append('item_ids', JSON.stringify(itemIds));
    sendOrderUpdateRequest(formData, 'Stato items multipli aggiornato.', 'Agg. Multiplo Items');
}

// --- Funzioni Azioni Cucina (associate ai bottoni) ---
window.toggleItemStatus = function(itemElement, idOrdine) {
    const card = itemElement.closest('.kitchen-order-card');
    if (!card) return;
    const orderStatus = card.getAttribute('data-status');
    const idDettaglioOrdine = itemElement.getAttribute('data-item-id');
    let currentItemStatus = itemElement.getAttribute('data-item-status');

    if (orderStatus === 'pending' || orderStatus === 'served' || orderStatus === 'cancelled' || (orderStatus === 'ready' && currentItemStatus === 'ready')) {
        showNotification('Azione non permessa per questo item o stato ordine.', 'warning');
        return; 
    }
    const nuovoStatoItem = (currentItemStatus === 'pending' || currentItemStatus === 'preparing') ? 'ready' : 'preparing';
    updateItemStatus(idDettaglioOrdine, idOrdine, nuovoStatoItem);
}

function getKitchenCardItemIds(buttonElement) {
    const card = buttonElement.closest('.kitchen-order-card');
    if (!card) return { idOrdine: null, itemIds: [] };
    const idOrdine = card.getAttribute('data-order-id');
    const items = card.querySelectorAll('.kitchen-order-item:not(.locked)'); // Solo quelli non bloccati
    const itemIds = Array.from(items).map(item => item.getAttribute('data-item-id'));
    return { idOrdine, itemIds };
}

window.markAllComplete = function(button) {
    const { idOrdine, itemIds } = getKitchenCardItemIds(button);
    if (idOrdine && itemIds.length > 0) {
        updateMultipleItemsStatus(idOrdine, itemIds, 'mark_all_complete');
    } else if (idOrdine) {
        showNotification('Nessun item modificabile per marcare come completato.', 'info');
    }
}

window.markAllIncomplete = function(button) {
    const { idOrdine, itemIds } = getKitchenCardItemIds(button);
    if (idOrdine && itemIds.length > 0) {
        updateMultipleItemsStatus(idOrdine, itemIds, 'mark_all_incomplete');
    } else if (idOrdine) {
        showNotification('Nessun item modificabile per marcare come non completato.', 'info');
    }
}

window.startPreparing = function(button) {
    const card = button.closest('.kitchen-order-card');
    const idOrdine = card.getAttribute('data-order-id');
    updateOrderStatus(idOrdine, 'preparing');
}

window.markAsReady = function(button) {
    const card = button.closest('.kitchen-order-card');
    const idOrdine = card.getAttribute('data-order-id');
    updateOrderStatus(idOrdine, 'ready');
}

window.markAsServed = function(button) {
    const card = button.closest('.kitchen-order-card');
    const idOrdine = card.getAttribute('data-order-id');
    updateOrderStatus(idOrdine, 'served');
}

window.cancelOrder = function(button) { // "Rimetti in Preparazione"
    const card = button.closest('.kitchen-order-card');
    const idOrdine = card.getAttribute('data-order-id');
    updateOrderStatus(idOrdine, 'preparing'); // Riporta a 'preparing'
}

window.confirmDeleteOrder = function(idOrdine) {
    openConfirmationModal(`Sei sicuro di voler ANNULLARE l'ordine #${idOrdine}? Questa azione non può essere annullata.`, () => {
        updateOrderStatus(idOrdine, 'cancelled');
    });
}

// --- Aggiornamento UI Dinamico per Azioni Cucina ---
window.updateKitchenActions = function(cardElement) { 
    if (!cardElement) return;
    const status = cardElement.getAttribute('data-status');
    const actionsContainer = cardElement.querySelector('.kitchen-actions');
    if (!actionsContainer) return;
    
    const items = cardElement.querySelectorAll('.kitchen-order-item');
    const deleteBtnContainer = cardElement.querySelector('.delete-btn-container');
    
    // Determina se tutti gli item modificabili sono 'ready'
    let allItemsReady = true;
    let hasModifiableItems = false;
    items.forEach(item => {
        const itemStatus = item.getAttribute('data-item-status');
        const isItemLocked = status === 'pending' || status === 'served' || status === 'cancelled' || (status === 'ready' && itemStatus === 'ready');
        item.classList.toggle('locked', isItemLocked);
        if (!isItemLocked) {
            hasModifiableItems = true;
            if (itemStatus !== 'ready') {
                allItemsReady = false;
            }
        }
    });
    if (!hasModifiableItems && status === 'preparing') allItemsReady = false; // Se non ci sono item modificabili, non si può marcare come pronto


    if (deleteBtnContainer) {
         deleteBtnContainer.style.display = (status === 'served' || status === 'cancelled') ? 'flex' : 'none';
    }

    actionsContainer.innerHTML = ''; 

    if (status === 'pending') {
        actionsContainer.innerHTML = `<button class="btn btn-preparing" onclick="startPreparing(this)"><i data-lucide="chef-hat" class="action-icon"></i> Inizia preparazione</button>`;
    } else if (status === 'preparing') {
        if (hasModifiableItems) {
            actionsContainer.innerHTML = `<button class="btn btn-complete" onclick="markAllComplete(this)"><i data-lucide="check-square" class="action-icon"></i> Tutti Pronti</button>`;
        }
        if (allItemsReady && hasModifiableItems) { // Solo se ci sono item e sono tutti pronti
            actionsContainer.innerHTML += `<button class="btn btn-ready ms-2" onclick="markAsReady(this)"><i data-lucide="check-check" class="action-icon"></i> Ordine Pronto</button>`;
        } else if (!hasModifiableItems && allOrdersDataGlobal.find(o => o.id_ordine == cardElement.dataset.orderId)?.items.length === 0) {
            // Caso speciale: ordine senza item, permetti di marcarlo come pronto direttamente
             actionsContainer.innerHTML += `<button class="btn btn-ready ms-2" onclick="markAsReady(this)"><i data-lucide="check-check" class="action-icon"></i> Ordine Pronto</button>`;
        }
        if (hasModifiableItems) {
             actionsContainer.innerHTML += `<button class="btn btn-incomplete ms-2" onclick="markAllIncomplete(this)"><i data-lucide="x-square" class="action-icon"></i> Nessuno Pronto</button>`;
        }
    } else if (status === 'ready') {
        actionsContainer.innerHTML = `
            <button class="btn btn-cancel" onclick="cancelOrder(this)"><i data-lucide="rotate-ccw" class="action-icon"></i> Rimetti in Prep.</button>
            <button class="btn btn-served ms-2" onclick="markAsServed(this)"><i data-lucide="hand-platter" class="action-icon"></i> Segna Servito</button>`;
    } else if (status === 'served' || status === 'cancelled') {
        actionsContainer.innerHTML = `<p class="text-muted small m-0">Ordine ${status === 'served' ? 'servito' : 'annullato'}. Nessuna azione ulteriore.</p>`; 
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// --- Inizializzazione ---
document.addEventListener("DOMContentLoaded", function () {
    if (typeof ID_RISTORANTE_CORRENTE_ORDINI === 'undefined' || !ID_RISTORANTE_CORRENTE_ORDINI) {
        // Già gestito all'inizio dello script, ma una doppia verifica non fa male.
        return; 
    }

    showView(currentView); // Imposta la vista iniziale
    
    // Setup listeners per ricerca e filtri
    if (searchInputWaiter) searchInputWaiter.addEventListener('keyup', () => applyCurrentFiltersAndRender());
    if (searchInputKitchen) searchInputKitchen.addEventListener('keyup', () => applyCurrentFiltersAndRender());
    setupFilterTabs('waiter');
    setupFilterTabs('kitchen');

    loadOrders(true); // Caricamento iniziale forzato
    if (pollingIntervalId) clearInterval(pollingIntervalId); // Pulisci intervalli precedenti se ce ne sono
    pollingIntervalId = setInterval(() => loadOrders(false), POLLING_RATE); // Avvia polling
});
