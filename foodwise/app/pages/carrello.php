<?php
// session_start() viene già chiamato dal file index.php principale che include questa pagina.

// --- CONTROLLO DI SICUREZZA PER LA SESSIONE DEL CLIENTE ---
// Un cliente può vedere il carrello solo se ha scansionato un QR code,
// il che imposta queste variabili di sessione.
$ristorante = $_SESSION['ristorante'] ?? null;
$table_token = $_SESSION['table_token'] ?? null;
$table_id = $_SESSION['table_id'] ?? null;

// Se una di queste informazioni manca, l'utente non è "seduto" a un tavolo.
// Lo reindirizziamo alla pagina principale del sito per evitare errori.
if (!$ristorante || !$table_token || !$table_id) {
    // Reindirizza alla homepage principale se i dati del tavolo non sono in sessione.
    // L'URL deve essere assoluto partendo dalla root del dominio.
    header('Location: /'); 
    exit;
}
// --- FINE CONTROLLO DI SICUREZZA ---


// Carica il carrello dalla sessione, se esiste, altrimenti crea un array vuoto.
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!-- Sistema di notifiche toast -->
<div id="toast-container" class="toast-container"></div>

<div class="carrello-container <?php echo isset($_SESSION['table_token']) ? 'with-sidebar' : ''; ?>">
    <div class="carrello-content">
        <?php if (empty($cart)): ?>
            <!-- Sezione mostrata se il carrello è vuoto -->
            <div class="carrello-empty">
                <i data-lucide="shopping-cart" class="carrello-icon"></i>
                <h2 class="carrello-title">Il tuo carrello è vuoto</h2>
                <p class="carrello-text">Aggiungi qualche piatto dal menu per iniziare il tuo ordine.</p>
                <div class="carrello-form">
                    <a href="/foodwise/<?php echo urlencode(lcfirst($ristorante)); ?>/menu" class="category-btn btn rounded-pill">
                        <i data-lucide="utensils" class="button-icon"></i>
                        Vai al menu
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Sezione mostrata se ci sono articoli nel carrello -->
            <div class="carrello-header-info">
                <i data-lucide="users" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Tavolo #<?php echo htmlspecialchars($table_id); ?> - <?php echo count($cart); ?> prodott<?php echo count($cart) > 1 ? 'i' : 'o'; ?>
            </div>

            <!-- Lista degli articoli nel carrello -->
            <div class="carrello-items" id="cart-items-container">
                <?php foreach ($cart as $dishId => $item): ?>
                    <?php
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;
                    ?>
                    <div class="carrello-item" data-dish-id="<?php echo htmlspecialchars($dishId); ?>">
                        <img src="<?php echo htmlspecialchars($item['image'] ?: '/foodwise/app/assets/images/placeholder-dish.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="carrello-item-image"
                             onerror="this.onerror=null;this.src='/foodwise/app/assets/images/placeholder-dish.jpg';">
                        
                        <div class="carrello-item-details">
                            <div class="carrello-item-info">
                                <h3 class="carrello-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="carrello-item-price">€<?php echo number_format($item['price'], 2, ',', '.'); ?></p>
                            </div>
                            
                            <div class="carrello-item-actions">
                                <span class="carrello-item-total">€<?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                                <button class="carrello-item-remove" data-dish-id="<?php echo htmlspecialchars($dishId); ?>" title="Rimuovi dal carrello">
                                    <i data-lucide="trash-2" class="action-icon-bin"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Controlli quantità -->
                        <div class="cart-quantity-group">
                            <button class="cart-quantity-btn cart-minus-btn" data-dish-id="<?php echo htmlspecialchars($dishId); ?>">
                                <i data-lucide="minus" class="cart-minus-icon"></i>
                            </button>
                            <span class="cart-quantity-count"><?php echo $item['quantity']; ?></span>
                            <button class="cart-quantity-btn cart-plus-btn" data-dish-id="<?php echo htmlspecialchars($dishId); ?>">
                                <i data-lucide="plus" class="cart-plus-icon"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($cart)): ?>
        <!-- Riepilogo fisso in basso -->
        <div class="carrello-summary">
            <div class="carrello-summary-row carrello-total">
                <span>Totale ordine:</span>
                <span>€<span id="cart-total"><?php echo number_format($total, 2, ',', '.'); ?></span></span>
            </div>
            
            <div class="carrello-actions">
                <button class="carrello-action-btn carrello-clear-btn" id="clear-cart-btn">
                    <i data-lucide="trash-2" class="button-icon"></i>
                    Svuota
                </button>
                <button class="carrello-action-btn carrello-order-btn" id="order-btn">
                    <i data-lucide="send" class="button-icon"></i>
                    Ordina ora
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Collegamenti a fogli di stile e script esterni -->
<link rel="stylesheet" href="/foodwise/app/assets/style.css"> 
<script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js"></script>

<!-- CSS INLINE PER QUESTA PAGINA -->
<style>
    /* Stili CSS per il carrello */
    .carrello-header-info {
        padding: 12px 16px;
        background-color: #1e1e1e;
        border-bottom: 1px solid #333;
        font-size: 14px;
        color: #00FF7F;
        display: flex;
        align-items: center;
        justify-content: center;
        position: sticky;
        top: 0;
        z-index: 5;
    }
    
    .carrello-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 20px;
    }

    .carrello-icon {
        width: 80px;
        height: 80px;
        color: #444;
        margin-bottom: 20px;
    }

    .carrello-title {
        font-size: 1.5rem;
        color: white;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .carrello-text {
        font-size: 1rem;
        color: #a0a0a0;
        margin-bottom: 20px;
    }

    .carrello-form .category-btn {
        background-color: rgba(0, 255, 127, 0.2);
        color: #00FF7F;
        border: 2px solid #00FF7F;
        font-weight: 600;
    }

    .carrello-items {
        padding: 10px;
    }

    .carrello-item {
        display: flex;
        align-items: stretch;
        background-color: #1f1f1f;
        border-radius: 10px;
        margin-bottom: 12px;
        min-height: 120px;
        position: relative;
        overflow: hidden;
    }

    .carrello-item-image {
        width: 120px;
        height: auto;
        object-fit: cover;
        flex-shrink: 0;
        border-radius: 10px 0 0 10px;
    }

    .carrello-item-details {
        flex: 1;
        display: flex;
        justify-content: space-between;
        padding: 12px;
    }

    .carrello-item-info {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        gap: 4px;
        overflow: hidden;
    }

    .carrello-item-name {
        font-size: 1.1rem;
        color: #ffffff;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .carrello-item-price {
        font-size: 0.9rem;
        color: #a0a0a0;
    }

    .carrello-item-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: space-between;
    }

    .carrello-item-total {
        font-size: 1.1rem;
        color: #00FF7F;
        font-weight: 700;
    }

    .carrello-item-remove {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 5px;
        color: #EF4444;
    }

    .action-icon-bin {
        width: 18px;
        height: 18px;
    }
    
    .cart-quantity-group {
        display: flex;
        align-items: center;
        position: absolute;
        bottom: 12px;
        left: 132px;
    }

    .cart-quantity-btn {
        background-color: #2a2a2a;
        border: 1px solid #444;
        color: white;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .cart-minus-btn { border-radius: 50% 0 0 50%; }
    .cart-plus-btn { border-radius: 0 50% 50% 0; }
    .cart-minus-icon, .cart-plus-icon { width: 16px; height: 16px; }

    .cart-quantity-count {
        background-color: #1e1e1e;
        color: white;
        width: 35px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        border-top: 1px solid #444;
        border-bottom: 1px solid #444;
    }

    .carrello-summary {
        position: fixed;
        bottom: 70px; /* Sopra la navbar */
        left: 0;
        width: 100%;
        background-color: #1e1e1e;
        padding: 16px 20px;
        border-top: 1px solid #333;
        z-index: 999;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .carrello-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 1rem;
        color: #e0e0e0;
    }
    .carrello-total {
        font-size: 1.1rem;
        font-weight: 700;
        color: #00FF7F;
    }
    .carrello-actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }
    .carrello-action-btn {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .carrello-clear-btn {
        background-color: transparent;
        color: #EF4444;
        border: 2px solid #EF4444;
    }
    .carrello-order-btn {
        background-color: #00FF7F;
        color: #121212;
        border: 2px solid #00FF7F;
    }
    .button-icon {
        width: 18px;
        height: 18px;
    }

    /* Sistema di notifiche Toast */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .toast {
        min-width: 300px; padding: 16px 20px; border-radius: 8px;
        color: white; font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transform: translateX(120%); opacity: 0;
        transition: all 0.4s cubic-bezier(0.215, 0.610, 0.355, 1);
        display: flex; align-items: center; gap: 12px;
        border: 1px solid #333;
    }
    .toast.show { transform: translateX(0); opacity: 1; }
    .toast.success { background-color: #00FF7F; color: #121212; border-color: #00FF7F;}
    .toast.error { background-color: #1e1e1e; color: #EF4444; border-color: #EF4444;}
    .toast.warning { background-color: #1e1e1e; color: #F59E0B; border-color: #F59E0B;}
    .toast.info { background-color: #1e1e1e; color: #3B82F6; border-color: #3B82F6;}
    .toast-icon { width: 20px; height: 20px; flex-shrink: 0; }
    .toast-content { flex: 1; }
    .toast-title { font-weight: 600; margin-bottom: 2px; }
    .toast-message { font-size: 14px; opacity: 0.9; }

</style>

<!-- JAVASCRIPT INLINE PER QUESTA PAGINA -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    function showToast(type, title, message, duration = 4000) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        let icon;
        switch(type) {
            case 'success': icon = 'check-circle'; break;
            case 'error': icon = 'x-circle'; break;
            case 'warning': icon = 'alert-triangle'; break;
            default: icon = 'info';
        }
        toast.innerHTML = `<i data-lucide="${icon}" class="toast-icon"></i><div class="toast-content"><div class="toast-title">${title}</div><div class="toast-message">${message}</div></div>`;
        container.appendChild(toast);
        lucide.createIcons();
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => { if (container.contains(toast)) container.removeChild(toast); }, 400);
        }, duration);
    }

    const updateCart = (dishId, quantity) => {
        fetch('/foodwise/database/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                dishId: dishId,
                quantity: quantity,
                ristorante: '<?php echo urlencode(lcfirst($ristorante)); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (quantity === 0) {
                    const itemElement = document.querySelector(`.carrello-item[data-dish-id="${dishId}"]`);
                    if (itemElement) itemElement.remove();
                    showToast('info', 'Prodotto Rimosso', 'Il prodotto è stato rimosso dal carrello.');
                }
                updateCartTotals();
                checkEmptyCart();
            } else {
                showToast('error', 'Errore Carrello', data.message || 'Impossibile aggiornare il carrello.');
            }
        })
        .catch(error => showToast('error', 'Errore di Rete', 'Impossibile comunicare con il server.'));
    };

    const checkEmptyCart = () => {
        const cartItems = document.querySelectorAll('.carrello-item');
        if (cartItems.length === 0) {
            // Ricarica la pagina per mostrare la vista "carrello vuoto"
            location.reload();
        }
    };

    const updateCartTotals = () => {
        let total = 0;
        document.querySelectorAll('.carrello-item').forEach(item => {
            const quantity = parseInt(item.querySelector('.cart-quantity-count').textContent);
            const priceText = item.querySelector('.carrello-item-price').textContent.match(/€([\d,.]+)/)[1].replace('.', '').replace(',', '.');
            const price = parseFloat(priceText);
            const subtotal = quantity * price;
            item.querySelector('.carrello-item-total').textContent = '€' + subtotal.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            total += subtotal;
        });
        const totalElement = document.getElementById('cart-total');
        if (totalElement) totalElement.textContent = total.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    document.addEventListener('click', (e) => {
        const plusBtn = e.target.closest('.cart-plus-btn');
        if (plusBtn) {
            const dishId = plusBtn.dataset.dishId;
            const countElement = plusBtn.parentElement.querySelector('.cart-quantity-count');
            let count = parseInt(countElement.textContent) + 1;
            countElement.textContent = count;
            updateCart(dishId, count);
        }

        const minusBtn = e.target.closest('.cart-minus-btn');
        if (minusBtn) {
            const dishId = minusBtn.dataset.dishId;
            const countElement = minusBtn.parentElement.querySelector('.cart-quantity-count');
            let count = parseInt(countElement.textContent) - 1;
            countElement.textContent = count;
            updateCart(dishId, count);
        }

        const removeBtn = e.target.closest('.carrello-item-remove');
        if (removeBtn) {
            const dishId = removeBtn.dataset.dishId;
            updateCart(dishId, 0);
        }
    });

    const clearBtn = document.getElementById('clear-cart-btn');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            fetch('/foodwise/database/clear_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ristorante: '<?php echo urlencode(lcfirst($ristorante)); ?>' })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    showToast('error', 'Errore', data.message || 'Impossibile svuotare il carrello.');
                }
            });
        });
    }

    const orderBtn = document.getElementById('order-btn');
    if (orderBtn) {
        orderBtn.addEventListener('click', () => {
            const cartItems = Array.from(document.querySelectorAll('.carrello-item')).map(item => ({
                id_portata: item.dataset.dishId,
                quantita: parseInt(item.querySelector('.cart-quantity-count').textContent),
                prezzo: parseFloat(item.querySelector('.carrello-item-price').textContent.match(/€([\d,.]+)/)[1].replace('.', '').replace(',', '.')),
                nome: item.querySelector('.carrello-item-name').textContent
            }));
            
            const orderData = {
                ristorante: '<?php echo urlencode(lcfirst($ristorante)); ?>',
                tableId: '<?php echo $table_id; ?>',
                tableToken: '<?php echo $table_token; ?>',
                prodotti: cartItems,
                prezzoTotale: parseFloat(document.getElementById('cart-total').textContent.replace('.', '').replace(',', '.'))
            };

            orderBtn.disabled = true;
            orderBtn.innerHTML = '<i data-lucide="loader-2" class="button-icon animate-spin"></i> Invio in corso...';
            lucide.createIcons();
            showToast('info', 'Invio Ordine', 'Stiamo elaborando il tuo ordine...');

            fetch('/foodwise/database/add_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Ordine Inviato!', 'Il tuo ordine è stato ricevuto dalla cucina.');
                    setTimeout(() => {
                        window.location.href = '/foodwise/<?php echo urlencode(lcfirst($ristorante)); ?>/menu';
                    }, 2000);
                } else {
                    showToast('error', 'Errore Ordine', data.message || 'Impossibile inviare l\'ordine.');
                    orderBtn.disabled = false;
                    orderBtn.innerHTML = '<i data-lucide="send" class="button-icon"></i> Ordina ora';
                    lucide.createIcons();
                }
            })
            .catch(error => {
                showToast('error', 'Errore di Rete', 'Impossibile comunicare con il server.');
                orderBtn.disabled = false;
                orderBtn.innerHTML = '<i data-lucide="send" class="button-icon"></i> Ordina ora';
                lucide.createIcons();
            });
        });
    }
});
</script>
