let currentView = localStorage.getItem('tableView') || 'list';
let currentTable = null;
let currentTableId = null;

function applyView(view) {
    const tablesList = document.getElementById('tablesList');
    const viewToggleBtn = document.getElementById('viewToggleBtn');
    const viewIcon = viewToggleBtn.querySelector('.view-icon');

    tablesList.classList.remove('grid-view', 'list-view');
    tablesList.style.display = '';
    tablesList.style.flexDirection = '';
    tablesList.style.gap = '';

    if (view === 'grid') {
        tablesList.classList.add('grid-view');
        viewIcon.setAttribute('data-lucide', 'list');
        const tableItems = tablesList.getElementsByClassName('table-card');
        for (let item of tableItems) {
            item.style.width = '';
            item.style.maxWidth = '';
            item.style.height = '200px';
            item.classList.remove('mb-3');
            item.querySelector('.card-title').style.fontSize = '20px';
            item.querySelector('.capacity').style.fontSize = '16px';
        }
    } else {
        tablesList.classList.add('list-view');
        viewIcon.setAttribute('data-lucide', 'grid');
        const tableItems = tablesList.getElementsByClassName('table-card');
        for (let item of tableItems) {
            item.style.height = 'auto';
            item.style.width = '100%';
            item.style.maxWidth = 'none';
            item.classList.add('mb-3');
            item.querySelector('.card-title').style.fontSize = '22px';
            item.querySelector('.capacity').style.fontSize = '16px';
        }
    }
    lucide.createIcons();
}

function toggleView() {
    currentView = currentView === 'list' ? 'grid' : 'list';
    localStorage.setItem('tableView', currentView);
    applyView(currentView);
}

function showTableDetails(tableName, capacity, status, tableId) {
    currentTable = tableName;
    currentTableId = tableId;

    const tableModalLabel = document.getElementById('tableModalLabel');
    const modalTableCapacity = document.getElementById('modalTableCapacity');
    const capacityNumber = modalTableCapacity.querySelector('.capacity-number');
    const modalTableStatus = document.getElementById('modalTableStatus');
    const occupiedField = document.getElementById('occupiedField');
    const reservedFields = document.getElementById('reservedFields');

    tableModalLabel.textContent = tableName;
    capacityNumber.textContent = capacity;
    modalTableStatus.textContent = status;

    occupiedField.style.display = status === 'Occupied' ? 'block' : 'none';
    reservedFields.style.display = status === 'Reserved' ? 'block' : 'none';

    // Reset input fields
    document.getElementById('occupiedPeople').value = '';
    document.getElementById('customerName').value = '';
    document.getElementById('reservationTime').value = '';
    document.getElementById('reservationPeople').value = '';

    // If status is Reserved, fetch reservation details
    if (status === 'Reserved') {
        fetch(`../database/reservations.php?action=get_reservations&id_ristorante=${ID_RISTORANTE_CORRENTE}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const reservation = data.reservations.find(res => res.numero_tavolo === tableName);
                    if (reservation) {
                        document.getElementById('customerName').value = reservation.nome_prenotazione;
                        document.getElementById('reservationTime').value = reservation.ore;
                        document.getElementById('reservationPeople').value = reservation.numero_persone;
                    }
                }
            })
            .catch(error => console.error('Errore durante il recupero dei dettagli della prenotazione:', error));
    }

    const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
    tableModal.show();
    lucide.createIcons();

    // Update button states
    const liberoBtn = document.getElementById('liberoBtn');
    const occupatoBtn = document.getElementById('occupatoBtn');
    const riservatoBtn = document.getElementById('riservatoBtn');
    liberoBtn.classList.toggle('active', status === 'Free');
    occupatoBtn.classList.toggle('active', status === 'Occupied');
    riservatoBtn.classList.toggle('active', status === 'Reserved');
}

function updateTableStatus(status) {
    if (!currentTable) return;

    // Update modal display
    document.getElementById('modalTableStatus').textContent = status;
    document.getElementById('occupiedField').style.display = status === 'Occupied' ? 'block' : 'none';
    document.getElementById('reservedFields').style.display = status === 'Reserved' ? 'block' : 'none';

    // Update button states
    const liberoBtn = document.getElementById('liberoBtn');
    const occupatoBtn = document.getElementById('occupatoBtn');
    const riservatoBtn = document.getElementById('riservatoBtn');
    liberoBtn.classList.toggle('active', status === 'Free');
    occupatoBtn.classList.toggle('active', status === 'Occupied');
    riservatoBtn.classList.toggle('active', status === 'Reserved');
}

function getStatusIcon(status) {
    switch (status) {
        case 'Free': return 'check';
        case 'Occupied': return 'x';
        case 'Reserved': return 'calendar';
        default: return 'circle';
    }
}

function openQRModal() {
    const tableName = document.getElementById('tableModalLabel').textContent;
    document.getElementById('qrModalLabel').textContent = `QR Code per ${tableName}`;
    document.getElementById('qrCodeImage').src = '';
    document.getElementById('qrCodeLink').textContent = '';

    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    qrModal.show();
    lucide.createIcons();
}

function saveTableChanges() {
    if (!currentTable || !currentTableId) return;

    const newStatus = document.getElementById('modalTableStatus').textContent;
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('numero_tavolo', currentTable);
    formData.append('stato', newStatus);
    formData.append('id_ristorante', ID_RISTORANTE_CORRENTE);
    formData.append('id_tavolo', currentTableId);

    if (newStatus === 'Reserved') {
        const customerName = document.getElementById('customerName').value.trim();
        const reservationTime = document.getElementById('reservationTime').value;
        const reservationPeople = parseInt(document.getElementById('reservationPeople').value);

        if (!customerName || !reservationTime || isNaN(reservationPeople) || reservationPeople < 1) {
            alert('Per impostare lo stato a Riservato, compila tutti i campi: Nome Cliente, Ora e Numero di persone.');
            return;
        }

        formData.append('nome_prenotazione', customerName);
        formData.append('ore', reservationTime);
        formData.append('numero_persone', reservationPeople);
    } else if (newStatus === 'Occupied') {
        const occupiedPeople = parseInt(document.getElementById('occupiedPeople').value);
        if (isNaN(occupiedPeople) || occupiedPeople < 1) {
            alert('Per impostare lo stato a Occupato, inserisci un numero valido di persone sedute.');
            return;
        }
        formData.append('numero_persone', occupiedPeople);
    }

    fetch('../database/add_table.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update table card UI
                const tableCard = Array.from(document.getElementsByClassName('table-card')).find(
                    card => card.querySelector('.card-title').textContent === currentTable
                );
                if (tableCard) {
                    tableCard.setAttribute('data-status', newStatus);
                    const badge = tableCard.querySelector('.badge');
                    badge.className = `badge status-${newStatus.toLowerCase()}`;
                    badge.innerHTML = `<i data-lucide="${getStatusIcon(newStatus)}" class="status-icon"></i> ${newStatus === 'Free' ? 'Libero' : newStatus === 'Occupied' ? 'Occupato' : 'Prenotato'}`;

                    const reservationTag = tableCard.querySelector('.reservation-tag');
                    if (reservationTag) reservationTag.remove();

                    if (newStatus === 'Reserved') {
                        const customerName = document.getElementById('customerName').value;
                        const reservationTime = document.getElementById('reservationTime').value;
                        const reservationPeople = document.getElementById('reservationPeople').value;
                        const reservationInfo = document.createElement('span');
                        reservationInfo.className = 'reservation-tag';
                        reservationInfo.innerHTML = `
                            <div class="reservation-info-grid">
                                <span class="reservation-label">Prenotato per:</span>
                                <span class="reservation-value">${customerName}</span>
                                <span class="reservation-label">Ora:</span>
                                <span class="reservation-value">${reservationTime}</span>
                                <span class="reservation-label">Persone:</span>
                                <span class="reservation-value">${reservationPeople}</span>
                            </div>
                        `;
                        tableCard.querySelector('.card-body').appendChild(reservationInfo);
                    }
                    lucide.createIcons();
                }
                const tableModal = bootstrap.Modal.getInstance(document.getElementById('tableModal'));
                tableModal.hide();
                loadTablesFromDatabase();
            } else {
                alert('Errore durante l\'aggiornamento dello stato: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'aggiornamento dello stato:', error);
            alert('Errore durante l\'aggiornamento dello stato: ' + error.message);
        });
}

function searchTables() {
    let input = document.getElementById("searchTables").value.toLowerCase();
    let cards = document.getElementsByClassName("table-card");

    for (let i = 0; i < cards.length; i++) {
        let tableName = cards[i].querySelector('.card-title').textContent.toLowerCase();
        if (tableName.includes(input)) {
            cards[i].style.display = "";
        } else {
            cards[i].style.display = "none";
        }
    }
}

function updateReservationsModal() {
    const reservationsList = document.getElementById('reservationsList');
    const noReservationsMessage = document.getElementById('noReservationsMessage');
    const modalContent = document.querySelector('#reservationsModal .modal-content');

    const reservationItems = reservationsList.getElementsByClassName('reservation-item');
    const numReservations = reservationItems.length;

    let itemHeight = 42;
    let gap = 8;
    if (window.innerWidth <= 576) {
        itemHeight = 36;
    } else if (window.innerWidth <= 768) {
        itemHeight = 38;
    }

    const maxVisibleItems = 8;
    let listHeight;
    let modalHeight;

    if (numReservations === 0) {
        noReservationsMessage.style.display = 'block';
        reservationsList.style.height = '42px';
        reservationsList.style.overflowY = 'hidden';
        modalHeight = 42 + 40 + 32 + 12;
    } else {
        noReservationsMessage.style.display = 'none';
        if (numReservations <= maxVisibleItems) {
            listHeight = (numReservations * itemHeight) + ((numReservations - 1) * gap);
            reservationsList.style.overflowY = 'hidden';
        } else {
            listHeight = (maxVisibleItems * itemHeight) + ((maxVisibleItems - 1) * gap);
            reservationsList.style.overflowY = 'auto';
        }
        reservationsList.style.height = `${listHeight}px`;
        modalHeight = listHeight + 40 + 32 + 12;
    }

    modalContent.style.height = `${modalHeight}px`;
}

function openReservationsModal() {
    const reservationsList = document.getElementById('reservationsList');
    const noReservationsMessage = document.getElementById('noReservationsMessage');

    reservationsList.innerHTML = '';
    noReservationsMessage.style.display = 'none';

    fetch(`../database/reservations.php?action=get_reservations&id_ristorante=${ID_RISTORANTE_CORRENTE}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.reservations.length > 0) {
                data.reservations.forEach(reservation => {
                    const reservationItem = document.createElement('div');
                    reservationItem.className = 'reservation-item';
                    reservationItem.setAttribute('data-table', reservation.numero_tavolo);
                    reservationItem.setAttribute('data-table-id', reservation.id_tavolo);
                    reservationItem.innerHTML = `
                        <div class="reservation-details">
                            <span class="detail-table">${reservation.numero_tavolo}</span>
                            <span class="detail-name">${reservation.nome_prenotazione}</span>
                            <span class="detail-time">${reservation.ore}</span>
                            <span class="number-people">${reservation.numero_persone}</span>
                        </div>
                        <div class="reservation-actions">
                            <button class="action-btn set-occupied-btn" onclick="setTableOccupied('${reservation.numero_tavolo}', '${reservation.id_tavolo}')" title="Imposta Occupato">
                                <i data-lucide="user" class="action-icon"></i>
                            </button>
                            <button class="action-btn delete-reservation-btn" onclick="cancelReservation('${reservation.numero_tavolo}', '${reservation.id_tavolo}')" title="Cancella">
                                <i data-lucide="trash-2" class="action-icon"></i>
                            </button>
                        </div>
                    `;
                    reservationsList.appendChild(reservationItem);
                });
            } else {
                noReservationsMessage.style.display = 'block';
            }
            updateReservationsModal();
            const reservationsModal = new bootstrap.Modal(document.getElementById('reservationsModal'));
            reservationsModal.show();
            lucide.createIcons();
        })
        .catch(error => {
            console.error('Errore durante il recupero delle prenotazioni:', error);
            noReservationsMessage.style.display = 'block';
            updateReservationsModal();
            const reservationsModal = new bootstrap.Modal(document.getElementById('reservationsModal'));
            reservationsModal.show();
            lucide.createIcons();
        });
}

function setTableOccupied(tableName, tableId) {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('numero_tavolo', tableName);
    formData.append('stato', 'Occupied');
    formData.append('id_ristorante', ID_RISTORANTE_CORRENTE);
    formData.append('id_tavolo', tableId);

    fetch('../database/add_table.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tableCard = Array.from(document.getElementsByClassName('table-card')).find(
                    card => card.querySelector('.card-title').textContent === tableName
                );
                if (tableCard) {
                    tableCard.setAttribute('data-status', 'Occupied');
                    const badge = tableCard.querySelector('.badge');
                    badge.className = 'badge status-occupied';
                    badge.innerHTML = '<i data-lucide="x" class="status-icon"></i> Occupato';
                    const reservationTag = tableCard.querySelector('.reservation-tag');
                    if (reservationTag) reservationTag.remove();
                    lucide.createIcons();
                }
                const reservationsModal = bootstrap.Modal.getInstance(document.getElementById('reservationsModal'));
                if (reservationsModal) reservationsModal.hide();
                const reservationItem = document.querySelector(`.reservation-item[data-table="${tableName}"]`);
                if (reservationItem) {
                    reservationItem.remove();
                    updateReservationsModal();
                }
            } else {
                alert('Errore durante l\'aggiornamento dello stato: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'aggiornamento dello stato:', error);
            alert('Errore durante l\'aggiornamento dello stato: ' + error.message);
        });
}

function cancelReservation(tableName, tableId) {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('numero_tavolo', tableName);
    formData.append('stato', 'Free');
    formData.append('id_ristorante', ID_RISTORANTE_CORRENTE);
    formData.append('id_tavolo', tableId);

    fetch('../database/add_table.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const reservationItem = document.querySelector(`.reservation-item[data-table="${tableName}"]`);
                if (reservationItem) {
                    reservationItem.remove();
                    const reservationsList = document.getElementById('reservationsList');
                    const noReservationsMessage = document.getElementById('noReservationsMessage');
                    if (reservationsList.children.length === 0) {
                        noReservationsMessage.style.display = 'block';
                    }
                    updateReservationsModal();
                }
                const tableCard = Array.from(document.getElementsByClassName('table-card')).find(
                    card => card.querySelector('.card-title').textContent === tableName
                );
                if (tableCard) {
                    tableCard.setAttribute('data-status', 'Free');
                    const badge = tableCard.querySelector('.badge');
                    badge.className = 'badge status-free';
                    badge.innerHTML = '<i data-lucide="check" class="status-icon"></i> Libero';
                    const reservationTag = tableCard.querySelector('.reservation-tag');
                    if (reservationTag) reservationTag.remove();
                    lucide.createIcons();
                }
            } else {
                alert('Errore durante l\'aggiornamento dello stato: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'aggiornamento dello stato:', error);
            alert('Errore durante l\'aggiornamento dello stato: ' + error.message);
        });
}

function openAddTableModal() {
    const addTableModal = new bootstrap.Modal(document.getElementById('addTableModal'));
    const form = document.getElementById('addTableForm');
    form.reset();
    form.querySelectorAll('.form-control').forEach(input => input.classList.remove('is-invalid'));
    addTableModal.show();
    setTimeout(() => {
        document.getElementById('tableName').focus();
    }, 500);
    lucide.createIcons();
}

function addNewTable(tableName, capacity, status = 'Free', tableId, reservation = null) {
    const tablesList = document.getElementById('tablesList');
    const tableCard = document.createElement('div');
    tableCard.className = `table-card ${currentView === 'list' ? 'mb-3' : ''}`;
    tableCard.setAttribute('data-status', status);
    tableCard.setAttribute('data-table-id', tableId);
    let reservationInfo = '';
    if (status === 'Reserved' && reservation) {
        reservationInfo = `
           
        `;
    }
    tableCard.innerHTML = `
        <div class="card restaurant-info-card" onclick="showTableDetails('${tableName}', ${capacity}, '${status}', '${tableId}')">
            <div class="card-body flex-column p-3">
                <div class="table-header">
                    <h5 class="card-title mb-0">${tableName}</h5>
                    <span class="badge status-${status.toLowerCase()}">
                        <i data-lucide="${getStatusIcon(status)}" class="status-icon"></i> ${status === 'Free' ? 'Libero' : status === 'Occupied' ? 'Occupato' : 'Prenotato'}
                    </span>
                </div>
                <div class="capacity-container">
                    <span class="capacity">
                        <i data-lucide="users" class="capacity-icon"></i> ${capacity}
                    </span>
                </div>
                ${reservationInfo}
            </div>
        </div>
    `;
    tablesList.appendChild(tableCard);
    applyView(currentView);
    lucide.createIcons();
}

function loadTablesFromDatabase() {
    fetch('../database/get_tables.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tablesList = document.getElementById('tablesList');
                tablesList.innerHTML = '';
                fetch(`../database/reservations.php?action=get_reservations&id_ristorante=${ID_RISTORANTE_CORRENTE}`)
                    .then(res => res.json())
                    .then(resData => {
                        data.tables.forEach(table => {
                            const reservation = resData.success ? resData.reservations.find(res => res.numero_tavolo === table.numero_tavolo) : null;
                            addNewTable(table.numero_tavolo, table.clienti, table.stato, table.id_tavolo, reservation);
                        });
                    })
                    .catch(error => {
                        console.error('Errore durante il recupero delle prenotazioni:', error);
                        data.tables.forEach(table => {
                            addNewTable(table.numero_tavolo, table.clienti, table.stato, table.id_tavolo);
                        });
                    });
            } else {
                console.error('Errore durante il recupero dei tavoli:', data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante il recupero dei tavoli:', error);
        });
}

function deleteTable() {
    if (!currentTable || !currentTableId) {
        alert('Errore: nessun tavolo selezionato per l\'eliminazione.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_table');
    formData.append('numero_tavolo', currentTable);
    formData.append('id_ristorante', ID_RISTORANTE_CORRENTE);

    fetch('../database/delete_tables.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tableCard = Array.from(document.getElementsByClassName('table-card')).find(
                    card => card.querySelector('.card-title').textContent === currentTable
                );
                if (tableCard) tableCard.remove();
                const tableModal = bootstrap.Modal.getInstance(document.getElementById('tableModal'));
                tableModal.hide();
                loadTablesFromDatabase();
            } else {
                alert('Errore durante l\'eliminazione del tavolo: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'eliminazione del tavolo:', error);
            alert('Errore durante l\'eliminazione del tavolo: ' + error.message);
        });
}

document.addEventListener("DOMContentLoaded", () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    applyView(currentView);
    loadTablesFromDatabase();

    const inputs = document.querySelectorAll('#addTableModal input');
    inputs.forEach(input => {
        input.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    });

    const inputWrappers = document.querySelectorAll('#addTableModal .input-wrapper');
    inputWrappers.forEach(wrapper => {
        wrapper.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    });

    const tableNameInput = document.getElementById('tableName');
    tableNameInput.setAttribute('maxlength', '500');
    tableNameInput.addEventListener('input', () => {
        const value = tableNameInput.value.trim();
        if (value.length > 500) {
            tableNameInput.classList.add('is-invalid');
            tableNameInput.nextElementSibling.textContent = 'Il nome del tavolo non può superare i 500 caratteri';
        } else if (value && !isNaN(value) && parseInt(value) > 500) {
            tableNameInput.classList.add('is-invalid');
            tableNameInput.nextElementSibling.textContent = 'Il numero del tavolo non può essere maggiore di 500';
        } else {
            tableNameInput.classList.remove('is-invalid');
            tableNameInput.nextElementSibling.textContent = 'Inserisci un nome per il tavolo.';
        }
    });

    const addTableForm = document.getElementById('addTableForm');
    addTableForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const tableNameInput = document.getElementById('tableName');
        const tableCapacityInput = document.getElementById('tableCapacity');
        const tableName = tableNameInput.value.trim();
        const tableCapacity = parseInt(tableCapacityInput.value);

        let isValid = true;

        if (!tableName) {
            tableNameInput.classList.add('is-invalid');
            tableNameInput.nextElementSibling.textContent = 'Inserisci un nome per il tavolo.';
            isValid = false;
        } else if (tableName.length > 500) {
            tableNameInput.classList.add('is-invalid');
            tableNameInput.nextElementSibling.textContent = 'Il nome del tavolo non può superare i 500 caratteri';
            isValid = false;
        } else if (!isNaN(tableName) && parseInt(tableName) > 500) {
            tableNameInput.classList.add('is-invalid');
            tableNameInput.nextElementSibling.textContent = 'Il numero del tavolo non può essere maggiore di 500';
            isValid = false;
        } else {
            tableNameInput.classList.remove('is-invalid');
        }

        if (!tableCapacity || tableCapacity < 1) {
            tableCapacityInput.classList.add('is-invalid');
            isValid = false;
        } else {
            tableCapacityInput.classList.remove('is-invalid');
        }

        if (isValid) {
            const formData = new FormData();
            formData.append('action', 'add_table');
            formData.append('numero_tavolo', tableName);
            formData.append('clienti', tableCapacity);
            formData.append('id_ristorante', ID_RISTORANTE_CORRENTE);

            fetch('../database/add_table.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        addNewTable(tableName, tableCapacity, 'Free', data.id_tavolo);
                        const addTableModal = bootstrap.Modal.getInstance(document.getElementById('addTableModal'));
                        addTableModal.hide();
                        loadTablesFromDatabase();
                    } else {
                        tableNameInput.classList.add('is-invalid');
                        tableNameInput.nextElementSibling.textContent = data.message;
                    }
                })
                .catch(error => {
                    console.error('Errore durante l\'aggiunta del tavolo:', error);
                    alert('Errore durante l\'aggiunta del tavolo: ' + error.message);
                });
        }
    });
});

window.addEventListener('resize', updateReservationsModal);