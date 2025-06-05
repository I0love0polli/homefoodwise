<!-- Container tavoli -->
<div class="container tavoli-container">
    <!-- Header con Searchbar e Pulsanti -->
    <div class="row justify-content-center">
        <div class="col-12 mb-4">
            <div class="header-actions mb-4">
                <div class="search-bar-container">
                    <div class="search-bar">
                        <input type="text" id="searchTables" class="form-control" placeholder="Cerca tavoli..."
                            onkeyup="searchTables()">
                        <i data-lucide="search" class="search-icon"></i>
                    </div>
                </div>
                <div class="button-group">
                    <button class="btn btn-view-toggle" id="viewToggleBtn" onclick="toggleView()">
                        <i data-lucide="grid" class="view-icon"></i>
                    </button>
                    <button class="btn btn-reservations" onclick="openReservationsModal()">
                        <i data-lucide="calendar" class="reservations-icon"></i> Prenotazioni
                    </button>
                    <button class="btn btn-add-table" onclick="openAddTableModal()">
                        <i data-lucide="plus" class="add-table-icon"></i>Aggiungi Tavolo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Elenco dei tavoli -->
    <div class="tables-list" id="tablesList"></div>

    <!-- Modal per i dettagli del tavolo -->
    <div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title-container">
                        <h5 class="modal-title" id="tableModalLabel"></h5>
                        <button type="button" class="btn-qr" onclick="openQRModal()">
                            <i data-lucide="qr-code" class="qr-icon"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-info-container">
                        <p><span class="label">Stato:</span> <span id="modalTableStatus" class="detail-value"></span></p>
                        <p><span class="label">Capacità:</span> <span id="modalTableCapacity" class="detail-value"><span class="capacity-number"></span></span></p>
                    </div>
                    <div class="update-status-section">
                        <h4>Aggiorna Stato</h4>
                        <div class="status-buttons">
                            <button class="btn btn-status btn-free" id="liberoBtn" onclick="updateTableStatus('Free')">
                                <i data-lucide="check" class="status-icon"></i> Libero
                            </button>
                            <button class="btn btn-status btn-occupied" id="occupatoBtn" onclick="updateTableStatus('Occupied')">
                                <i data-lucide="x" class="status-icon"></i> Occupato
                            </button>
                            <button class="btn btn-status btn-reserved" id="riservatoBtn" onclick="updateTableStatus('Reserved')">
                                <i data-lucide="calendar" class="status-icon"></i> Riservato
                            </button>
                        </div>
                        <div id="occupiedField" class="status-field" style="display: none;">
                            <label for="occupiedPeople" class="form-label">Numero di persone sedute:</label>
                            <input type="number" class="form-control" id="occupiedPeople" min="1" placeholder="Es. 4">
                        </div>
                        <div id="reservedFields" class="status-field" style="display: none;">
                            <label for="customerName" class="form-label">Nome Cliente:</label>
                            <input type="text" class="form-control" id="customerName" placeholder="Es. Rossi">
                            <label for="reservationTime" class="form-label">Ora:</label>
                            <input type="time" class="form-control" id="reservationTime">
                            <label for="reservationPeople" class="form-label">Numero di persone:</label>
                            <input type="number" class="form-control" id="reservationPeople" min="1" placeholder="Es. 4">
                        </div>
                    </div>
                    <div class="popup-actions">
                        <button class="btn btn-delete" onclick="deleteTable()">
                            <i data-lucide="trash-2" class="action-icon"></i> Elimina Tavolo
                        </button>
                        <button class="btn btn-save" onclick="saveTableChanges()">
                            <i data-lucide="save" class="action-icon"></i> Salva Modifiche
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per il QR Code -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="qrModalLabel"></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="qr-code-container">
                        <img id="qrCodeImage" src="" alt="QR Code" style="max-width: 200px; max-height: 200px;">
                    </div>
                    <p>I clienti possono scansionare questo QR code per visualizzare il menu, effettuare ordini o richiedere assistenza.</p>
                    <p id="qrCodeLink"></p>
                    <button class="btn btn-share-qr">
                        <i data-lucide="share-2" class="action-icon"></i> Condividi QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per le prenotazioni attive -->
    <div class="modal fade" id="reservationsModal" tabindex="-1" aria-labelledby="reservationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationsModalLabel">Prenotazioni Attive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="reservations-list" id="reservationsList"></div>
                    <div class="no-reservations" id="noReservationsMessage" style="display: none;">
                        <p>Nessuna prenotazione attiva al momento.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal per l'aggiunta di un tavolo -->
    <div class="modal fade" id="addTableModal" tabindex="-1" aria-labelledby="addTableModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTableModalLabel">Aggiungi Tavolo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTableForm">
                        <div class="row">
                            <!-- Nome Tavolo -->
                            <div class="col-md-6 mb-3">
                                <label for="tableName" class="form-label">Nome Tavolo</label>
                                <div class="input-wrapper">
                                    <input type="text" class="form-control with-icon" id="tableName" placeholder="Es. Tavolo 6" name="tableName" required>
                                    <i data-lucide="utensils" class="input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Inserisci un nome per il tavolo.</div>
                            </div>
                            <!-- Capacità -->
                            <div class="col-md-6 mb-3">
                                <label for="tableCapacity" class="form-label">Capacità</label>
                                <div class="input-wrapper">
                                    <input type="number" class="form-control with-icon" id="tableCapacity" min="1" placeholder="Es. 4" name="tableCapacity" required>
                                    <i data-lucide="users" class="input-icon"></i>
                                </div>
                                <div class="invalid-feedback">Inserisci un numero positivo per la capacità.</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-action mt-2 w-100">
                            <i data-lucide="save" class="buttons-icon"></i>
                            Aggiungi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const ID_RISTORANTE_CORRENTE = <?php echo json_encode($_SESSION['login_restaurant']); ?>;
</script>
<link rel="stylesheet" href="assets/css/tavoli.css">
<script src="https://unpkg.com/lucide@latest"></script>
<script src="assets/script.js"></script>
<script src="assets/js/tavoli.js"></script>