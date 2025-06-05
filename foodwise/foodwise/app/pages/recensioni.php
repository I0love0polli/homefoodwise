<?php
// Start session only if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determina l'ID del ristorante dall'URL o dalla sessione
$ristorante_url_param = $_SESSION['ristorante_url_param'] ?? null; 

if (!$ristorante_url_param) {
    $uri_segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
    if (isset($uri_segments[1]) && $uri_segments[0] === 'foodwise' && (isset($uri_segments[2]) && $uri_segments[2] === basename(__FILE__, ".php"))) {
         $ristorante_url_param = $uri_segments[1];
    } else {
        if (isset($_GET['ristorante'])) { 
             $ristorante_url_param = $_GET['ristorante'];
        } else {
            error_log("recensioni.php: ID Ristorante non specificato nell'URL o sessione.");
            die("ID Ristorante non specificato. Impossibile caricare le recensioni.");
        }
    }
}
$id_ristorante_attuale = strtolower($ristorante_url_param);
?>

<div class="container recensioni-container">
    <div class="recensioni-header">
        <h1 class="recensioni-header-title">Recensioni per <?php echo htmlspecialchars(ucfirst($id_ristorante_attuale)); ?></h1>
    </div>

    <div class="recensioni-content">
        <!-- Sezione per lasciare una nuova recensione -->
        <div class="lascia-recensione-section card restaurant-info-card">
            <div class="card-body">
                <form id="formNuovaRecensione">
                    <input type="hidden" name="id_ristorante" value="<?php echo htmlspecialchars($id_ristorante_attuale); ?>">
                    <div class="mb-3">
                        <label for="reviewName" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="reviewName" name="nome" required placeholder="Il tuo nome">
                    </div>
                    <div class="mb-3">
                        <label for="reviewRating" class="form-label">Valutazione</label>
                        <div class="star-rating-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="stelle" value="<?php echo $i; ?>" required/>
                                <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stelle"><i data-lucide="star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviewDescription" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="reviewDescription" name="descrizione" rows="3" required placeholder="Scrivi qui la tua recensione..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit-review">
                        <i data-lucide="send" class="button-icon"></i> Invia Recensione
                    </button>
                </form>
                <div id="reviewFormMessage" class="mt-3"></div>
            </div>
        </div>

        <!-- Sezione per visualizzare le recensioni esistenti -->
        <div class="lista-recensioni-section mt-4">
            <h4 class="mb-3">Recensioni</h4>
            <div id="listaRecensioniContainer">
                <div class="alert alert-info text-center" role="alert" id="loadingReviewsMessage">
                    <i data-lucide="loader-2" class="me-2 spin"></i> Caricamento recensioni...
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .recensioni-container {
        background-color: #121212;
        color: #ffffff;
        padding: 0;
        margin: 0;
        border: none;
        width: 100%;
        max-width: none;
        box-sizing: border-box;
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .recensioni-header {
        position: sticky; 
        top: 0;
        left: 0;
        width: 100%;
        background-color: #121212;
        z-index: 10;
        padding: 10px 0;
        margin: 0;
        border-bottom: 1px solid #2c2c2c;
    }

    .recensioni-header-title {
        text-align: center;
        font-size: 20px;
        padding: 0;
        margin: 0;
        text-transform: uppercase;
        color: #ffffff;
        padding-bottom: 10px; 
        padding-top:10px;
    }

    .recensioni-content {
        padding: 20px 15px 70px; 
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #1e1e1e transparent;
        height: calc(100vh - 70px - 60px); /* Navbar app (70px) + Header recensioni (60px) */
        max-width: 800px; 
        margin-left: auto;
        margin-right: auto;
    }
    
    .recensioni-content::-webkit-scrollbar { width: 8px; }
    .recensioni-content::-webkit-scrollbar-track { background: transparent; }
    .recensioni-content::-webkit-scrollbar-thumb { background: #1e1e1e; border-radius: 10px; }
    .recensioni-content::-webkit-scrollbar-thumb:hover { background: #2a2a2a; }

    .restaurant-info-card { 
        background-color: #1f1f1f;
        border: 1px solid #2a2a2a;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        width: 100%;
    }
    .lascia-recensione-section .card-body { padding: 20px; }
    .lascia-recensione-section .form-label { color: #a0a0a0; font-size: 0.9rem; margin-bottom: 5px; }
    .lascia-recensione-section .form-control {
        background-color: #121212;
        border: 1px solid #3f3f3f;
        color: #e0e0e0;
        border-radius: 8px;
        padding: 10px 15px;
    }
    .lascia-recensione-section .form-control:focus {
        background-color: #121212;
        border-color: #00FF7F;
        box-shadow: 0 0 0 0.2rem rgba(0, 255, 127, 0.25);
        color: #e0e0e0;
    }
    .lascia-recensione-section .form-control::placeholder { color: #6c757d; }

    .star-rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; margin-bottom: 10px; }
    .star-rating-input input[type="radio"] { display: none; }
    .star-rating-input label { cursor: pointer; font-size: 2rem; color: #444; transition: color 0.2s; padding: 0 2px;}
    .star-rating-input label i { pointer-events: none; } 
    .star-rating-input input[type="radio"]:checked ~ label,
    .star-rating-input label:hover,
    .star-rating-input label:hover ~ label { color: #00FF7F !important; }
    .star-rating-input input[type="radio"]:checked + label i, 
    .star-rating-input input[type="radio"]:checked ~ label i, 
    .star-rating-input label:hover i, 
    .star-rating-input label:hover ~ label i 
    {
        color: #00FF7F !important; 
        fill: #00FF7F; 
    }
     .star-rating-input label i { 
        color: #444;
        fill: transparent; 
    }

    .text-muted{
        color : #c0c0c0 !important;

    }

    .btn-submit-review {
        background-color: rgba(0, 255, 127, 0.2);
        color: #00FF7F;
        border: 2px solid #00FF7F;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-submit-review:hover {
        background-color: rgba(0, 255, 127, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
    }
    .btn-submit-review .button-icon { width: 18px; height: 18px; margin-right: 8px; }

    #reviewFormMessage { font-size: 0.9rem; text-align: center; }
    #reviewFormMessage.success { color: #00FF7F; }
    #reviewFormMessage.error { color: #EF4444; }

    .lista-recensioni-section h4 { color: #e0e0e0; font-weight: 600; }
    .recensione-card .card-body { padding: 15px; }
    .reviewer-name { color: #00FF7F; font-weight: 600; }
    .star-rating-display .star-icon {
        width: 16px;
        height: 16px;
        color: #444; 
        margin-right: 2px;
    }
    .star-rating-display .star-icon.filled {
        color: #00FF7F; 
        fill: #00FF7F;
    }
    .review-date { font-size: 0.75rem; margin-top: -5px; }
    .review-description { color: #c0c0c0; font-size: 0.9rem; line-height: 1.5; }
    
    .alert.alert-info {
        background-color: #1f1f1f;
        color: #a0a0a0;
        border-color: #2a2a2a;
    }
    .alert.alert-info i {
        vertical-align: middle;
    }
     .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .alert-secondary { background-color: #2a2a2a; border-color: #3f3f3f; color: #a0a0a0;}
    .alert-secondary i { width: 20px; height: 20px; vertical-align: middle;}


    @media (max-width: 576px) {
        .recensioni-header-title { font-size: 18px; padding-bottom: 8px; padding-top: 8px;}
        .recensioni-content { 
            padding: 15px 10px 60px; 
            height: calc(100vh - 50px - 70px); /* Ajuste para header mais pequeno em mobile */
        }
        .lascia-recensione-section .card-body, .recensione-card .card-body { padding: 15px; }
        .star-rating-input label { font-size: 1.8rem; }
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
    }

    const idRistoranteAttuale = "<?php echo htmlspecialchars(addslashes($id_ristorante_attuale)); ?>";
    const listaRecensioniContainer = document.getElementById('listaRecensioniContainer');
    const loadingReviewsMessage = document.getElementById('loadingReviewsMessage');
    const formNuovaRecensione = document.getElementById('formNuovaRecensione');
    const reviewFormMessage = document.getElementById('reviewFormMessage');

    function displayStarsJS(num_stelle) {
        let starsHTML = '';
        num_stelle = parseInt(num_stelle, 10);
        for (let i = 1; i <= 5; i++) {
            starsHTML += `<i data-lucide="star" class="star-icon ${i <= num_stelle ? 'filled' : ''}"></i>`;
        }
        return starsHTML;
    }

    function loadReviews() {
        if (!idRistoranteAttuale) {
            listaRecensioniContainer.innerHTML = `<div class="alert alert-danger text-center" role="alert">ID Ristorante non disponibile.</div>`;
            if(loadingReviewsMessage) loadingReviewsMessage.style.display = 'none';
            return;
        }
        if(loadingReviewsMessage) loadingReviewsMessage.style.display = 'block';

        // Path corretto se recensioni.php è in app/pages/ e get_reviews.php è in database/
        fetch(`../database/get_reviews.php?ristorante=${encodeURIComponent(idRistoranteAttuale)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if(loadingReviewsMessage) loadingReviewsMessage.style.display = 'none';
                listaRecensioniContainer.innerHTML = ''; // Pulisci prima di aggiungere

                if (data.success && data.recensioni && data.recensioni.length > 0) {
                    data.recensioni.forEach(recensione => {
                        const card = document.createElement('div');
                        card.className = 'card restaurant-info-card recensione-card mb-3';
                        
                        let reviewDate = 'Data non disponibile';
                        if (recensione.data_recensione) {
                            try {
                                const dateObj = new Date(recensione.data_recensione);
                                // Formattazione manuale, puoi usare librerie come date-fns per opzioni più avanzate
                                const day = String(dateObj.getDate()).padStart(2, '0');
                                const monthNames = ["Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic"];
                                const month = monthNames[dateObj.getMonth()];
                                const year = dateObj.getFullYear();
                                const hours = String(dateObj.getHours()).padStart(2, '0');
                                const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                                reviewDate = `${day} ${month} ${year}, ${hours}:${minutes}`;
                            } catch (e) {
                                reviewDate = recensione.data_recensione; // Fallback alla stringa originale
                            }
                        }

                        card.innerHTML = `
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="card-title reviewer-name mb-1">${recensione.nome ? recensione.nome.replace(/</g, "&lt;").replace(/>/g, "&gt;") : 'Anonimo'}</h6>
                                    <div class="star-rating-display">
                                        ${displayStarsJS(recensione.stelle)}
                                    </div>
                                </div>
                                <p class="review-date text-muted small mb-2">${reviewDate}</p>
                                <p class="card-text review-description">${recensione.descrizione ? recensione.descrizione.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>") : ''}</p>
                            </div>
                        `;
                        listaRecensioniContainer.appendChild(card);
                    });
                } else if (data.success && (!data.recensioni || data.recensioni.length === 0)) {
                    listaRecensioniContainer.innerHTML = `
                        <div class="alert alert-secondary text-center" role="alert">
                            <i data-lucide="message-square-x" class="me-2"></i> Nessuna recensione presente per questo ristorante. Sii il primo a lasciarne una!
                        </div>`;
                } else {
                    listaRecensioniContainer.innerHTML = `<div class="alert alert-danger text-center" role="alert">${data.message || 'Impossibile caricare le recensioni.'}</div>`;
                }
                if (typeof lucide !== 'undefined' && lucide.createIcons) {
                    lucide.createIcons(); // Ricarica le icone dopo aver aggiunto nuovo HTML
                }
            })
            .catch(error => {
                if(loadingReviewsMessage) loadingReviewsMessage.style.display = 'none';
                console.error('Errore fetch recensioni:', error);
                listaRecensioniContainer.innerHTML = `<div class="alert alert-danger text-center" role="alert">Errore nel caricamento delle recensioni. Dettagli: ${error.message}</div>`;
            });
    }

    if (formNuovaRecensione) {
        formNuovaRecensione.addEventListener('submit', function (e) {
            e.preventDefault();
            reviewFormMessage.textContent = ''; 
            reviewFormMessage.className = 'mt-3'; 

            const formData = new FormData(formNuovaRecensione);
            // id_ristorante è già nel form, non serve aggiungerlo qui via JS.

            fetch('../database/add_review.php', { // Assicurati che il path sia corretto da app/pages/
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error("Server error: " + response.status + " " + text) });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    reviewFormMessage.textContent = data.message || 'Recensione inviata con successo!';
                    reviewFormMessage.classList.add('success');
                    formNuovaRecensione.reset();
                    // Ricarica le recensioni per mostrare quella nuova
                    loadReviews(); 
                } else {
                    reviewFormMessage.textContent = data.message || 'Errore nell\'invio della recensione.';
                    reviewFormMessage.classList.add('error');
                }
            })
            .catch(error => {
                console.error('Errore invio recensione:', error);
                reviewFormMessage.textContent = 'Errore di rete o risposta server non valida. Dettagli: ' + error.message;
                reviewFormMessage.classList.add('error');
            });
        });
    }

    // Carica le recensioni al caricamento della pagina
    loadReviews();
});
</script>
