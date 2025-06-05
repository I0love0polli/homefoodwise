<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Includi lo script per recuperare i dati della dashboard
$backend_script_path1 = '../database/home/get_home_dashboard_data.php'; 
$backend_script_path2 = '../../database/home/get_home_dashboard_data.php';

if (file_exists($backend_script_path1)) {
    include $backend_script_path1;
} elseif (file_exists($backend_script_path2)) { 
     include $backend_script_path2;
} else {
    if (function_exists('error_log')) {
        error_log("home.php: CRITICO - Impossibile trovare get_home_dashboard_data.php. Percorsi tentati: $backend_script_path1, $backend_script_path2");
    }
    // Imposta valori di default per evitare errori Notice Undefined Variable nell'HTML
    $user_full_name_data = "Utente"; $user_role_data = "N/D"; 
    $restaurant_id_data = "N/D"; $todays_revenue_data = 0.00;
    $active_orders_count_data = 0; $occupied_tables_data = 0; $total_active_tables_data = 0;
    $current_customers_at_occupied_tables_data = 0; $max_capacity_all_active_tables_data = 0; 
    $upcoming_reservations_data = []; $customer_reviews_data = []; 
    $average_rating_data = 0; $total_reviews_data = 0; $capacity_percentage_data = 0;
}

// Variabili per la visualizzazione con fallback
$user_full_name_display = isset($user_full_name_data) ? $user_full_name_data : 'Ospite';
$user_role_display = isset($user_role_data) ? $user_role_data : 'N/D';
$restaurant_id_display = isset($restaurant_id_data) ? htmlspecialchars($restaurant_id_data) : 'N/D';
$todays_revenue_display = isset($todays_revenue_data) ? number_format($todays_revenue_data, 2, ',', '.') : '0,00';
$active_orders_count_display = $active_orders_count_data ?? 0;
$occupied_tables_display = $occupied_tables_data ?? 0;
$total_active_tables_display = $total_active_tables_data ?? 0;
$current_customers_display = $current_customers_at_occupied_tables_data ?? 0;
$max_capacity_display = (isset($max_capacity_all_active_tables_data) && $max_capacity_all_active_tables_data > 0) ? $max_capacity_all_active_tables_data : '~';
$capacity_percentage_display = $capacity_percentage_data ?? 0;
$average_rating_display = (isset($average_rating_data) && $average_rating_data > 0) ? number_format($average_rating_data, 1) : 'N/A';
$total_reviews_display = $total_reviews_data ?? 0;

// Icona del ruolo basata sul ruolo effettivo
$role_icon_lucide = "user"; // Default
if (isset($user_role_data)) {
    switch (strtolower($user_role_data)) {
        case 'manager':
            $role_icon_lucide = "shield";
            break;
        case 'cameriere':
            $role_icon_lucide = "hand-platter";
            break;
        case 'cuoco':
            $role_icon_lucide = "chef-hat";
            break;
    }
}
?>
<div class="container home-container">
    <div class="welcome-section mb-4">
        <div class="welcome-header">
            <h2>Buongiorno <?php echo $user_full_name_display; ?>!</h2>
            <div class="welcome-icons">
                <i data-lucide="bell" class="header-icon" id="notification-toggle"></i>
                <i data-lucide="inbox" class="header-icon" id="inbox-toggle"></i>
                <i data-lucide="sparkles" class="header-icon-active" id="news-toggle"></i>
            </div>
        </div>
        <p class="welcome-text">Benvenuto nella dashboard del tuo ristorante: <?php echo $restaurant_id_display; ?></p>
        <span class="user-role badge">
            <i data-lucide="<?php echo $role_icon_lucide; ?>" class="role-icon"></i> <?php echo $user_role_display; ?>
        </span>
    </div>

    <div class="news-popup" id="news-popup" style="display: none;">
        <div class="news-content">
            <div class="news-header">
                <h2>Novità</h2>
                <button type="button" class="btn-close" id="close-news-popup" aria-label="Close"></button>
            </div>
            <div class="news-list">
                <div class="news-item">
                    <span class="news-date">GIU 05, 2025</span>
                    <div class="news-title"><i data-lucide="info" class="news-icon"></i> Gestione Menu Migliorata</div>
                    <p class="news-text">Interfaccia aggiornata per una più facile gestione dei prodotti e delle categorie.</p>
                </div>
                 <div class="news-item">
                    <span class="news-date">MAG 15, 2025</span>
                    <div class="news-title"><i data-lucide="bar-chart-2" class="news-icon"></i> Nuove Statistiche</div>
                    <p class="news-text">Introdotte nuove analitiche per monitorare le performance del ristorante.</p>
                </div>
            </div>
            <div class="empty-state" style="display: none;">
                <i data-lucide="sparkles" class="empty-icon"></i>
                <p class="empty-text">Non ci sono ancora novità</p>
            </div>
        </div>
    </div>

    <div class="notify-popup" id="notification-popup" style="display: none;">
        <div class="news-content">
            <div class="news-header">
                <h2>Notifiche</h2>
                <button type="button" class="btn-close" id="close-notification-popup" aria-label="Close"></button>
            </div>
            <div class="news-list">
                 <div class="news-item">
                    <span class="news-date">OGGI</span>
                    <div class="news-title"><i data-lucide="alert-circle" class="news-icon"></i> Scorte Basse</div>
                    <p class="news-text">Le scorte di "Salmone Fresco" sono quasi esaurite.</p>
                </div>
            </div>
            <div class="empty-state" style="display: none;">
                <i data-lucide="bell" class="empty-icon"></i>
                <p class="empty-text">Nessuna nuova notifica</p>
            </div>
        </div>
    </div>

    <div class="inbox-popup" id="inbox-popup" style="display: none;">
        <div class="news-content">
            <div class="news-header">
                <h2>Inbox</h2>
                <button type="button" class="btn-close" id="close-inbox-popup" aria-label="Close"></button>
            </div>
            <div class="news-list">
                <div class="news-item">
                    <span class="news-date">IERI</span>
                    <div class="news-title"><i data-lucide="mail" class="news-icon"></i> Feedback Cliente</div>
                    <p class="news-text">Un cliente ha lasciato un feedback tramite il modulo contatti.</p>
                </div>
            </div>
            <div class="empty-state" style="display: none;">
                <i data-lucide="inbox" class="empty-icon"></i>
                <p class="empty-text">Nessun nuovo messaggio</p>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-overlay" style="display: none;"></div>

    <div class="reservations-popup" id="reservations-popup" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h2>Tutte le Prenotazioni</h2>
                <button type="button" class="btn-close" id="close-reservations-popup" aria-label="Close"></button>
            </div>
            <div class="popup-list">
                <?php if (empty($upcoming_reservations_data)): ?>
                    <div class="empty-state" style="display: flex;">
                        <i data-lucide="calendar-x" class="empty-icon"></i>
                        <p class="empty-text">Nessuna prenotazione imminente.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcoming_reservations_data as $res): ?>
                    <div class="reservation-item">
                        <div class="reservation-info">
                            <p class="reservation-name"><?php echo htmlspecialchars($res['nome_prenotazione'] ?: 'N/A'); ?></p>
                            <p class="reservation-details">
                                Ore: <?php echo htmlspecialchars($res['ore'] ?: 'N/A'); ?> • 
                                <?php echo htmlspecialchars($res['numero_persone'] ?: 'N/A'); ?> ospiti • 
                                Tavolo <?php echo htmlspecialchars($res['numero_tavolo'] ?: 'N/D'); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="reviews-popup" id="reviews-popup" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h2>Tutte le Recensioni</h2>
                <button type="button" class="btn-close" id="close-reviews-popup" aria-label="Close"></button>
            </div>
            <div class="popup-list">
                <?php if (empty($customer_reviews_data)): ?>
                     <div class="empty-state" style="display: flex;">
                        <i data-lucide="message-square-x" class="empty-icon"></i>
                        <p class="empty-text">Nessuna recensione disponibile.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($customer_reviews_data as $rev): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <p class="reviewer-name"><?php echo htmlspecialchars($rev['nome'] ?: 'Anonimo'); ?></p>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i data-lucide="star" class="star <?php echo ($i <= (int)($rev['stelle'] ?? 0)) ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="review-comment"><?php echo htmlspecialchars($rev['descrizione'] ?: 'Nessun commento.'); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="summary-section mb-4">
        <h2>Riepilogo Giornaliero</h2>
        <div class="summary-cards">
            <div class="summary-card revenue-card">
                <div class="summary-icon revenue-icon">
                    <i data-lucide="trending-up"></i>
                </div>
                <div class="summary-info">
                    <h3>Incasso Odierno</h3>
                    <p class="summary-value">€<?php echo $todays_revenue_display; ?></p>
                </div>
            </div>
            <div class="summary-card active-orders-card">
                <div class="summary-icon active-orders-icon">
                    <i data-lucide="list"></i>
                </div>
                <div class="summary-info">
                    <h3>Ordini Attivi</h3>
                    <p class="summary-value"><?php echo $active_orders_count_display; ?></p>
                </div>
            </div>
            <div class="summary-card table-status-card">
                <div class="summary-icon table-status-icon">
                    <i data-lucide="grid"></i>
                </div>
                <div class="summary-info">
                    <h3>Stato Tavoli</h3>
                    <p class="summary-value"><?php echo $occupied_tables_display; ?> / <?php echo $total_active_tables_display; ?> Occupati</p>
                    <p class="summary-subtext">Liberi: <?php echo $total_active_tables_display - $occupied_tables_display; ?></p>
                </div>
            </div>
            <div class="summary-card current-customers-card">
                <div class="summary-icon current-customers-icon">
                    <i data-lucide="users"></i>
                </div>
                <div class="summary-info">
                    <h3>Clienti Attuali</h3>
                    <p class="summary-value"><?php echo $current_customers_display; ?> / <?php echo $max_capacity_display;?></p>
                    <?php if (isset($max_capacity_all_active_tables_data) && $max_capacity_all_active_tables_data > 0): ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $capacity_percentage_display; ?>%;"></div>
                    </div>
                    <p class="summary-subtext"><?php echo $capacity_percentage_display; ?>% Capacità</p>
                    <?php else: ?>
                    <p class="summary-subtext">Capacità non definita</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="reservations-section mb-4">
        <div class="reservations-card">
            <div class="reservations-header">
                <h2>Prossime Prenotazioni</h2>
                <button class="action-btn view-all-btn" id="reservations-view-all" onclick="window.location.href='/foodwise/dashboard/tavoli'">
                    <i data-lucide="external-link" class="action-icon"></i>
                </button>
            </div>
            <div class="reservations-content">
                <h3><i data-lucide="calendar" class="calendar-icon"></i> Prenotazioni di Oggi/Domani</h3>
                <div class="reservation-list">
                    <?php if (empty($upcoming_reservations_data)): ?>
                        <div class="empty-state" style="display: flex; padding: 20px 0;">
                            <i data-lucide="calendar-x" class="empty-icon" style="margin-bottom: 8px;"></i>
                            <p class="empty-text">Nessuna prenotazione imminente.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($upcoming_reservations_data as $res): ?>
                        <div class="reservation-item">
                            <div class="reservation-info">
                                <p class="reservation-name"><?php echo htmlspecialchars($res['nome_prenotazione'] ?: 'N/D'); ?></p>
                                <p class="reservation-details">
                                    Ore: <?php echo htmlspecialchars($res['ore'] ?: 'N/D'); ?> • 
                                    <?php echo htmlspecialchars($res['numero_persone'] ?: 'N/D'); ?> ospiti • 
                                    Tavolo <?php echo htmlspecialchars($res['numero_tavolo'] ?: 'N/D'); ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="/foodwise/dashboard/tavoli" class="btn btn-add-reservation">
                    <i data-lucide="plus"></i> Aggiungi / Gestisci Prenotazioni
                </a>
            </div>
        </div>
    </div>

    <div class="reviews-section">
        <div class="reviews-card">
            <div class="reviews-header">
                <h2>Recensioni Clienti</h2>
                <button class="action-btn view-all-btn" id="reviews-view-all">
                    <i data-lucide="external-link" class="action-icon"></i>
                </button>
            </div>
            <div class="reviews-content">
                <div class="reviews-info">
                    <p class="rating-value"><?php echo $average_rating_display; ?></p>
                    <div class="stars">
                        <?php 
                        if (isset($average_rating_data) && $average_rating_data > 0) {
                            $full_stars = floor($average_rating_data);
                            $half_star = ($average_rating_data - $full_stars) >= 0.25 && ($average_rating_data - $full_stars) < 0.75; 
                            $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                            if (($average_rating_data - $full_stars) >= 0.75 && $full_stars < 5) { 
                                $full_stars++;
                                $half_star = false;
                                $empty_stars = 5 - $full_stars;
                            }

                            for ($i = 0; $i < $full_stars; $i++) echo '<i data-lucide="star" class="star filled"></i>';
                            if ($half_star) echo '<i data-lucide="star-half" class="star filled"></i>';
                            for ($i = 0; $i < $empty_stars; $i++) echo '<i data-lucide="star" class="star"></i>';
                        } else {
                            for ($i = 0; $i < 5; $i++) echo '<i data-lucide="star" class="star"></i>';
                        }
                        ?>
                    </div>
                    <p class="reviews-count"><?php echo $total_reviews_display; ?> recensioni</p>
                </div>
            </div>
            <div class="reviews-list">
                <?php if (empty($customer_reviews_data)): ?>
                     <div class="empty-state" style="display: flex; padding: 20px 0;">
                        <i data-lucide="message-square-x" class="empty-icon" style="margin-bottom: 8px;"></i>
                        <p class="empty-text">Nessuna recensione disponibile.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($customer_reviews_data as $rev): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <p class="reviewer-name"><?php echo htmlspecialchars($rev['nome'] ?: 'Anonimo'); ?></p>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i data-lucide="star" class="star <?php echo ($i <= (int)($rev['stelle'] ?? 0)) ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="review-comment"><?php echo htmlspecialchars($rev['descrizione'] ?: 'Nessun commento.'); ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS (ottimizzato e pulito) */
    .home-container {
        padding: 20px; background-color: #121212;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        position: relative; overflow-x: hidden; box-sizing: border-box; color: #e0e0e0;
    }
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px);
        z-index: 1001; display: none;
    }
    .welcome-section { margin-bottom: 30px; }
    .welcome-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
    .welcome-section h2 { color: white; font-size: 28px; font-weight: 600; margin: 0; }
    .welcome-icons { display: flex; gap: 20px; padding-top: 10px; position: relative; }
    .header-icon { width: 24px; height: 24px; color: #ffffff; cursor: pointer; transition: color 0.3s ease; }
    .header-icon:hover { color: #00FF7F; } 
    .header-icon-active { width: 24px; height: 24px; color: #00FF7F; cursor: pointer; }
    .welcome-text { color: #a0a0a0; font-size: 16px; }
    .user-role {
        font-size: 12px; padding: 4px 8px; border-radius: 12px; display: inline-flex;
        align-items: center; gap: 4px; background-color: rgba(0, 255, 127, 0.2);
        border: 1px solid #00FF7F; color: #00FF7F; font-weight: 700;
    }
    .role-icon { width: 16px; height: 16px; color: inherit; }

    .news-popup, .notify-popup, .inbox-popup {
        position: absolute; background-color: #1e1e1e; border-radius: 10px;
        z-index: 1002; width: 300px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        padding: 0; height: 400px; overflow: hidden;
    }
    .news-content { display: flex; flex-direction: column; height: 100%; border-radius: 10px; }
    .news-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 15px 20px; 
        background-color: #1e1e1e; position: sticky; top: 0; z-index: 10; flex-shrink: 0;
    }
    .news-header h2 { color: white; font-size: 20px; font-weight: 600; margin: 0; }
    .btn-close { 
        filter: invert(1); 
    }
    .news-list { flex: 1; overflow-y: auto; padding: 0 20px 20px 20px; }
    .news-item { padding: 10px 0; border-bottom: 1px solid #333; } 
    .news-item:last-child { border-bottom: none; }
    .news-date { color: #888; font-size: 12px; display: block; margin-bottom: 5px; } 
    .news-title { color: white; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 5px; }
    .news-icon { width: 16px; height: 16px; color: #00FF7F; }
    .news-text { color: #bbb; font-size: 13px; margin: 0; line-height: 1.4; } 

    .reservations-popup, .reviews-popup {
        position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
        background-color: #1e1e1e; border-radius: 10px; z-index: 1003;
        width: 90%; max-width: 700px; 
        max-height: 85vh; 
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        padding: 0; overflow: hidden; display: none; box-sizing: border-box;
    }
    .popup-content { display: flex; flex-direction: column; height: 100%; border-radius: 10px; overflow: hidden; }
    .popup-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 15px 20px; background-color: #1e1e1e;
        position: sticky; top: 0; z-index: 10; flex-shrink: 0; border-bottom: 1px solid #333;
    }
    .popup-header h2 { color: white; font-size: 20px; font-weight: 600; margin: 0; }
    .popup-list { flex: 1; overflow-y: auto; padding: 15px 20px; box-sizing: border-box; }
    .reservation-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 0; border-bottom: 1px solid #333; flex-wrap: wrap;
    }
    .reservation-info { flex: 1; min-width: 0; }
    .reservation-name { color: white; font-size: 15px; font-weight: 600; margin: 0 0 5px 0; overflow-wrap: break-word; }
    .reservation-details { color: #a0a0a0; font-size: 13px; margin: 0; overflow-wrap: break-word; }
    .reservation-actions { display: flex; gap: 10px; flex-shrink: 0; margin-top: 5px; } 
    
    .review-item { border-top: 1px solid #333; padding: 15px 0; }
    .review-item:first-child { border-top: none; padding-top: 0; }
    .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; flex-wrap: wrap; }
    .reviewer-name { color: white; font-size: 15px; font-weight: 500; margin: 0; }
    .review-comment { color: #bbb; font-size: 13px; margin: 0; overflow-wrap: break-word; line-height: 1.4;}
    .stars { display: flex; gap: 3px; } 
    .star { width: 16px; height: 16px; color: #F59E0B; } 
    .star.filled { fill: #F59E0B; } 


    .empty-state {
        flex: 1; display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 20px; text-align: center;
    }
    .empty-icon { width: 48px; height: 48px; color: #666; margin-bottom: 12px; } 
    .empty-text { color: #888; font-size: 15px; font-weight: 500; margin: 0; }

    .summary-section { margin-bottom: 30px; }
    .summary-section h2 { color: white; font-size: 24px; font-weight: 600; margin-bottom: 15px; }
    .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; } 
    .summary-card {
        background-color: #1e1e1e; border-radius: 10px; padding: 20px;
        display: flex; align-items: center; min-height: 120px;
        position: relative; overflow: hidden; box-sizing: border-box; border-left: 5px solid; 
    }
    .revenue-card { border-color: #0EA5E9; }
    .active-orders-card { border-color: #F97316; }
    .table-status-card { border-color: #14B8A6; }
    .current-customers-card { border-color: #A855F7; }
    
    .summary-icon {
        width: 40px; height: 40px; border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; margin-right: 15px;
    }
    .revenue-icon { background-color: rgba(14, 165, 233, 0.2); color: #0EA5E9; }
    .active-orders-icon { background-color: rgba(249, 115, 22, 0.2); color: #F97316; }
    .table-status-icon { background-color: rgba(20, 184, 166, 0.2); color: #14B8A6; }
    .current-customers-icon { background-color: rgba(168, 85, 247, 0.2); color: #A855F7; }
    .summary-icon i { width: 20px; height: 20px; } 

    .summary-info { flex: 1; position: relative; z-index: 1; }
    .summary-info h3 { font-size: 13px; font-weight: 500; color: #a0a0a0; margin-bottom: 5px; text-transform: uppercase; }
    .summary-value { font-size: 22px; font-weight: 600; color: white; margin-bottom: 5px; }
    .summary-subtext { font-size: 13px; color: #a0a0a0; margin: 0; }
    .progress-bar { width: 100%; height: 6px; background-color: #333; border-radius: 3px; overflow: hidden; margin: 5px 0; }
    .progress-fill { height: 100%; background-color: #A855F7; transition: width 0.3s ease; border-radius: 3px;}

    .reservations-section { margin-bottom: 30px; }
    .reservations-card {
        background-color: #1e1e1e; border-radius: 10px; padding: 20px;
        position: relative; overflow: hidden; box-sizing: border-box; border-left: 5px solid #00FF7F;
    }
    .reservations-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 15px; position: relative; z-index: 1;
    }
    .reservations-header h2 { color: white; font-size: 22px; font-weight: 600; margin: 0; }
    .reservations-content h3 {
        color: #00FF7F; font-size: 15px; font-weight: 500;
        margin-bottom: 15px; display: flex; align-items: center;
    }
    .calendar-icon { width: 18px; height: 18px; margin-right: 8px; color: #00FF7F; }
    .reservation-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; }
    
    .action-btn { 
        background: #2a2a2a; border: none; padding: 6px; cursor: pointer;
        border-radius: 50%; transition: background-color 0.2s ease;
        width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
    }
    .action-btn:hover { background-color: #383838; }
    .action-btn .action-icon { color: #00FF7F; width: 16px; height: 16px; }
    .action-btn.delete-image-btn .action-icon { color: #dc3545; } 


    .btn-add-reservation {
        width: 100%; background-color: rgba(0, 255, 127, 0.15); 
        color: #00FF7F; border: 1px solid #00FF7F; 
        padding: 10px; border-radius: 8px; font-size: 15px; font-weight: 600; 
        display: flex; align-items: center; justify-content: center;
        gap: 6px; transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-add-reservation i { width: 18px; height: 18px; margin-right: 6px; }
    .btn-add-reservation:hover {
        background-color: rgba(0, 255, 127, 0.25); transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 255, 127, 0.2);
    }
    
    .reviews-section { margin-bottom: 30px; }
    .reviews-card {
        background-color: #1e1e1e; border-radius: 10px; padding: 20px;
        position: relative; overflow: hidden; box-sizing: border-box; border-left: 5px solid #F59E0B;
    }
    .reviews-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 15px; position: relative; z-index: 1;
    }
    .reviews-card h2 { color: white; font-size: 22px; font-weight: 600; margin: 0; }
    .reviews-info { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
    .rating-value { color: white; font-size: 28px; font-weight: 600; margin: 0; }
    .reviews-count { color: #a0a0a0; font-size: 13px; margin: 0; align-self: flex-end; padding-bottom: 5px;}
    .reviews-list { display: flex; flex-direction: column; gap: 12px; position: relative; z-index: 1; }

    @media (max-width: 768px) {
        .summary-cards { grid-template-columns: 1fr; } 
        .home-container { padding: 15px; }
        .welcome-section h2 { font-size: 24px; }
        .summary-card { padding: 15px; }
        .news-popup, .notify-popup, .inbox-popup { width: calc(100% - 30px); max-width: 320px; right: 15px !important; top: 65px !important; }
        .reservations-popup, .reviews-popup { width: 95%; max-height: 80vh; }
        .popup-header h2 { font-size: 18px; }
        .reservation-actions { margin-top: 8px; width:100%; justify-content: flex-end;}
        .review-header { flex-direction: column; align-items: flex-start; gap: 5px;}
        .stars { order: -1; margin-bottom: 5px;} 
    }
     @media (max-width: 480px) {
        .welcome-icons { gap: 15px; }
        .header-icon, .header-icon-active { width: 20px; height: 20px; }
        .summary-icon { width: 35px; height: 35px; }
        .summary-icon i { width: 18px; height: 18px; }
        .summary-value { font-size: 20px; }
        .reservations-header h2, .reviews-card h2 { font-size: 20px; }
        .btn-add-reservation { font-size: 14px; padding: 8px; }
        .rating-value { font-size: 24px; }
        .star { width: 14px; height: 14px; }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const isNewsEmpty = true; 
        const isNotificationsEmpty = false;
        const isInboxEmpty = false;
        const isReservationsEmpty = <?php echo json_encode(empty($upcoming_reservations_data)); ?>;
        const isReviewsEmpty = <?php echo json_encode(empty($customer_reviews_data)); ?>;

        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }

        const newsToggle = document.getElementById('news-toggle');
        const newsPopup = document.getElementById('news-popup');
        const closeNewsPopup = document.getElementById('close-news-popup');

        const notificationToggle = document.getElementById('notification-toggle');
        const notificationPopup = document.getElementById('notification-popup');
        const closeNotificationPopup = document.getElementById('close-notification-popup');

        const inboxToggle = document.getElementById('inbox-toggle');
        const inboxPopup = document.getElementById('inbox-popup');
        const closeInboxPopup = document.getElementById('close-inbox-popup');

        const reservationsViewAll = document.getElementById('reservations-view-all');
        const reservationsPopup = document.getElementById('reservations-popup');
        const closeReservationsPopup = document.getElementById('close-reservations-popup');

        const reviewsViewAll = document.getElementById('reviews-view-all');
        const reviewsPopup = document.getElementById('reviews-popup');
        const closeReviewsPopup = document.getElementById('close-reviews-popup');

        const modalOverlay = document.getElementById('modal-overlay');

        const popups = [newsPopup, notificationPopup, inboxPopup, reservationsPopup, reviewsPopup];
        const toggles = [newsToggle, notificationToggle, inboxToggle, reservationsViewAll, reviewsViewAll];
        const closeButtons = [closeNewsPopup, closeNotificationPopup, closeInboxPopup, closeReservationsPopup, closeReviewsPopup];

        const checkEmptyState = (popupElement) => {
            if (!popupElement) return;
            const list = popupElement.querySelector('.news-list') || popupElement.querySelector('.popup-list');
            const emptyState = popupElement.querySelector('.empty-state');
            let isEmpty;

            if (popupElement === newsPopup) isEmpty = isNewsEmpty;
            else if (popupElement === notificationPopup) isEmpty = isNotificationsEmpty;
            else if (popupElement === inboxPopup) isEmpty = isInboxEmpty;
            else if (popupElement === reservationsPopup) isEmpty = isReservationsEmpty;
            else if (popupElement === reviewsPopup) isEmpty = isReviewsEmpty;
            else return;

            if (list && emptyState) {
                 list.style.display = isEmpty ? 'none' : 'block';
                 emptyState.style.display = isEmpty ? 'flex' : 'none';
            }
        };
        
        popups.forEach(popup => { if (popup) checkEmptyState(popup); });

        const disableScroll = () => { document.body.style.overflow = 'hidden'; };
        const enableScroll = () => { document.body.style.overflow = ''; };

        const togglePopup = (popup, toggleButton, isCentered = false) => {
            if (!popup || !toggleButton) return;
            const isVisible = popup.style.display === 'block';
            
            popups.forEach(p => {
                if (p !== popup && p) p.style.display = 'none';
            });

            if (!isVisible) {
                if (!isCentered) {
                    const toggleRect = toggleButton.getBoundingClientRect();
                    const welcomeIconsEl = document.querySelector('.welcome-icons');
                    if (!welcomeIconsEl) return;
                    const welcomeIconsRect = welcomeIconsEl.getBoundingClientRect();
                    
                    popup.style.position = 'absolute'; 
                    popup.style.top = `${welcomeIconsRect.bottom + window.scrollY + 10}px`; 
                    
                    let rightOffset = document.documentElement.clientWidth - toggleRect.right;
                    if ( (toggleRect.right + popup.offsetWidth) > document.documentElement.clientWidth ) {
                         rightOffset = 5; 
                    }
                    popup.style.right = `${rightOffset}px`;
                    popup.style.left = 'auto'; 
                    
                } else {
                    if(modalOverlay) modalOverlay.style.display = 'block';
                    disableScroll();
                }
                popup.style.display = 'block';
                checkEmptyState(popup);
            } else {
                popup.style.display = 'none';
                if (isCentered && modalOverlay) {
                    modalOverlay.style.display = 'none';
                    enableScroll();
                }
            }
        };

        toggles.forEach((toggle, index) => {
            if(toggle){ 
                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isCentered = index >= 3; 
                    togglePopup(popups[index], toggle, isCentered);
                });
            }
        });

        closeButtons.forEach((closeBtn, index) => {
             if(closeBtn){ 
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if(popups[index]) popups[index].style.display = 'none';
                    if (index >= 3 && modalOverlay) {
                        modalOverlay.style.display = 'none';
                        enableScroll();
                    }
                });
            }
        });

        if(modalOverlay){
            modalOverlay.addEventListener('click', () => {
                popups.forEach((popup, index) => {
                    if (index >= 3 && popup && popup.style.display === 'block') { 
                        popup.style.display = 'none';
                    }
                });
                modalOverlay.style.display = 'none';
                enableScroll();
            });
        }

        document.addEventListener('click', (e) => {
            const isClickInsideAnyPopup = popups.some(popup => popup && popup.contains(e.target) && popup.style.display === 'block');
            const isClickOnAnyToggle = toggles.some(toggle => toggle && (toggle === e.target || toggle.contains(e.target)));
            
            if (!isClickInsideAnyPopup && !isClickOnAnyToggle) {
                popups.forEach((popup, index) => {
                    if (popup && popup.style.display === 'block') {
                        popup.style.display = 'none';
                        if (index >= 3 && modalOverlay) { 
                            modalOverlay.style.display = 'none';
                            enableScroll();
                        }
                    }
                });
            }
        });

        popups.forEach(popup => {
            if(popup) {
                popup.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }
        });
    });
</script>
<script src="assets/script.js"></script>