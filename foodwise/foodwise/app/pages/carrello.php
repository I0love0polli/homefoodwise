<?php

// Verifica sessione
$ristorante = isset($_SESSION['login_restaurant']) ? $_SESSION['login_restaurant'] : null;
if (!$ristorante || !isset($_SESSION['table_token']) || !isset($_SESSION['table_id'])) {
    header('Location: /foodwise/');
    exit;
}

// Carrello dalla sessione
$cart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
?>

<!-- Sistema di notifiche toast -->
<div id="toast-container" class="toast-container"></div>

<div class="carrello-container <?php echo isset($_SESSION['table_token']) ? 'with-sidebar' : ''; ?>">
    <div class="carrello-content">
        <?php if (empty($cart)): ?>
            <!-- Carrello vuoto -->
            <div class="carrello-empty">
                <i data-lucide="shopping-cart" class="carrello-icon"></i>
                <h2 class="carrello-title">Carrello vuoto</h2>
                <p class="carrello-text">Aggiungi qualche piatto dal menu per iniziare il tuo ordine</p>
                <div class="carrello-form">
                    <a href="/foodwise/<?php echo urlencode(lcfirst($ristorante)); ?>/menu" class="category-btn btn rounded-pill">
                        <i data-lucide="utensils" class="button-icon"></i>
                        Vai al menu
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Info tavolo -->
            <div class="carrello-header-info">
                <i data-lucide="users" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                Tavolo #<?php echo $_SESSION['table_id']; ?> - <?php echo count($cart); ?> prodott<?php echo count($cart) > 1 ? 'i' : 'o'; ?>
            </div>

            <!-- Items del carrello -->
            <div class="carrello-items" id="cart-items-container">
                <?php foreach ($cart as $dishId => $item): ?>
                    <?php
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;
                    ?>
                    <div class="carrello-item" data-dish-id="<?php echo htmlspecialchars($dishId); ?>">
                        <!-- Immagine placeholder o reale -->
                        <img src="/foodwise/app/assets/images/placeholder-dish.jpg" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="carrello-item-image"
                             onerror="this.style.display='none'">
                        
                        <div class="carrello-item-details">
                            <div class="carrello-item-info">
                                <h3 class="carrello-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="carrello-item-price">€<?php echo number_format($item['price'], 2); ?> cad.</p>
                            </div>
                            
                            <div class="carrello-item-actions">
                                <span class="carrello-item-total">€<?php echo number_format($subtotal, 2); ?></span>
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
                <span>€<span id="cart-total"><?php echo number_format($total, 2); ?></span></span>
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

<link rel="stylesheet" href="/foodwise/app/assets/css/menu.css">
<script src="/foodwise/app/assets/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js"></script>

<style>
/* Stili per il carrello - usando i colori del progetto */
.carrello-header {
    padding: 20px;
    background-color: #00FF7F;
    color: #121212;
    text-align: center;
    font-weight: 600;
    font-size: 18px;
}

.carrello-header-info {
    padding: 12px 16px;
    background-color: #1e1e1e;
    border-bottom: 1px solid #333;
    font-size: 14px;
    color: #00FF7F;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Sistema toast - usando i colori del progetto */
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
    min-width: 300px;
    padding: 16px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transform: translateX(100%);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #333;
}

.toast.show {
    transform: translateX(0);
}

.toast.success {
    background-color: #00FF7F;
    color: #121212;
}

.toast.error {
    background-color: #121212;
    color: #00FF7F;
    border-color: #00FF7F;
}

.toast.warning {
    background-color: #1e1e1e;
    color: #00FF7F;
    border-color: #00FF7F;
}

.toast.info {
    background-color: #1e1e1e;
    color: #00FF7F;
    border-color: #00FF7F;
}

.toast-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 600;
    margin-bottom: 2px;
}

.toast-message {
    font-size: 14px;
    opacity: 0.9;
}

/* Riduzione spazio generale */
.carrello-content {
    padding: 0;
}

.carrello-items {
    padding: 10px;
}

.carrello-item {
    margin-bottom: 12px;
    padding: 12px;
}

.carrello-summary {
    padding: 16px 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // Sistema di notifiche toast
    function showToast(type, title, message, duration = 4000) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        let icon;
        switch(type) {
            case 'success':
                icon = 'check-circle';
                break;
            case 'error':
                icon = 'x-circle';
                break;
            case 'warning':
                icon = 'alert-triangle';
                break;
            case 'info':
                icon = 'info';
                break;
            default:
                icon = 'bell';
        }
        
        toast.innerHTML = `
            <i data-lucide="${icon}" class="toast-icon"></i>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
        `;
        
        container.appendChild(toast);
        lucide.createIcons();
        
        // Animazione di entrata
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Rimozione automatica
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (container.contains(toast)) {
                    container.removeChild(toast);
                }
            }, 300);
        }, duration);
    }

    const updateCart = (dishId, quantity) => {
        console.log('Updating cart:', { dishId, quantity });
        fetch('/foodwise/database/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                dishId: dishId,
                quantity: quantity,
                ristorante: '<?php echo urlencode(lcfirst($ristorante)); ?>'
            })
        })
        .then(response => {
            console.log('Risposta add_to_cart:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Dati add_to_cart:', data);
            if (data.success) {
                if (quantity === 0) {
                    const itemElement = document.querySelector(`.carrello-item[data-dish-id="${dishId}"]`);
                    if (itemElement) {
                        itemElement.remove();
                        showToast('success', 'Prodotto rimosso', 'Il prodotto è stato rimosso dal carrello');
                    }
                } else {
                    showToast('success', 'Carrello aggiornato', 'Quantità modificata con successo');
                }
                updateCartTotals();
                checkEmptyCart();
            } else {
                console.error('Errore nell\'aggiornamento del carrello:', data.message);
                showToast('error', 'Errore carrello', data.message || 'Impossibile aggiornare il carrello');
            }
        })
        .catch(error => {
            console.error('Errore AJAX:', error);
            showToast('error', 'Errore di connessione', 'Problemi di rete, riprova più tardi');
        });
    };

    const checkEmptyCart = () => {
        const cartItems = document.querySelectorAll('.carrello-item');
        if (cartItems.length === 0) {
            showToast('info', 'Carrello vuoto', 'Il tuo carrello è ora vuoto');
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    };

    const updateCartTotals = () => {
        const cartItems = document.querySelectorAll('.carrello-item');
        let total = 0;
        
        cartItems.forEach(item => {
            const quantity = parseInt(item.querySelector('.cart-quantity-count').textContent);
            const priceText = item.querySelector('.carrello-item-price').textContent.match(/€([\d.]+)/)[1];
            const price = parseFloat(priceText);
            const subtotal = quantity * price;
            
            // Aggiorna il subtotale dell'item
            item.querySelector('.carrello-item-total').textContent = '€' + subtotal.toFixed(2);
            total += subtotal;
        });
        
        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2);
        }
        
        // Aggiorna info header
        const headerInfo = document.querySelector('.carrello-header-info');
        if (headerInfo) {
            const itemCount = cartItems.length;
            headerInfo.innerHTML = `<i data-lucide="users" style="width: 18px; height: 18px; margin-right: 8px;"></i>Tavolo #<?php echo $_SESSION['table_id']; ?> - ${itemCount} prodott${itemCount > 1 ? 'i' : 'o'}`;
            lucide.createIcons();
        }
    };

    // Event listeners per i pulsanti quantità
    document.addEventListener('click', (e) => {
        if (e.target.closest('.cart-plus-btn')) {
            const button = e.target.closest('.cart-plus-btn');
            const dishId = button.dataset.dishId;
            const countElement = button.parentElement.querySelector('.cart-quantity-count');
            let count = parseInt(countElement.textContent);
            count++;
            countElement.textContent = count;
            updateCart(dishId, count);
        }
        
        if (e.target.closest('.cart-minus-btn')) {
            const button = e.target.closest('.cart-minus-btn');
            const dishId = button.dataset.dishId;
            const countElement = button.parentElement.querySelector('.cart-quantity-count');
            let count = parseInt(countElement.textContent);
            if (count > 1) {
                count--;
                countElement.textContent = count;
                updateCart(dishId, count);
            }
        }
        
        if (e.target.closest('.carrello-item-remove')) {
            const button = e.target.closest('.carrello-item-remove');
            const dishId = button.dataset.dishId;
            
            // Toast di conferma invece di alert
            const confirmToast = document.createElement('div');
            confirmToast.className = 'toast warning show';
            confirmToast.style.position = 'fixed';
            confirmToast.style.top = '50%';
            confirmToast.style.left = '50%';
            confirmToast.style.transform = 'translate(-50%, -50%)';
            confirmToast.style.zIndex = '10000';
            confirmToast.style.minWidth = '350px';
            
            confirmToast.innerHTML = `
                <i data-lucide="alert-triangle" class="toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">Conferma rimozione</div>
                    <div class="toast-message">Vuoi rimuovere questo prodotto dal carrello?</div>
                    <div style="margin-top: 12px; display: flex; gap: 8px;">
                        <button id="confirm-remove" style="background: #121212; color: #00FF7F; border: 1px solid #00FF7F; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Rimuovi</button>
                        <button id="cancel-remove" style="background: #1e1e1e; color: #00FF7F; border: 1px solid #333; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Annulla</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(confirmToast);
            lucide.createIcons();
            
            document.getElementById('confirm-remove').onclick = () => {
                updateCart(dishId, 0);
                document.body.removeChild(confirmToast);
            };
            
            document.getElementById('cancel-remove').onclick = () => {
                document.body.removeChild(confirmToast);
            };
        }
    });

    // Pulsante svuota carrello
    const clearBtn = document.getElementById('clear-cart-btn');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            // Toast di conferma per svuotare carrello
            const confirmToast = document.createElement('div');
            confirmToast.className = 'toast warning show';
            confirmToast.style.position = 'fixed';
            confirmToast.style.top = '50%';
            confirmToast.style.left = '50%';
            confirmToast.style.transform = 'translate(-50%, -50%)';
            confirmToast.style.zIndex = '10000';
            confirmToast.style.minWidth = '350px';
            
            confirmToast.innerHTML = `
                <i data-lucide="alert-triangle" class="toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">Svuota carrello</div>
                    <div class="toast-message">Vuoi svuotare completamente il carrello?</div>
                    <div style="margin-top: 12px; display: flex; gap: 8px;">
                        <button id="confirm-clear" style="background: #121212; color: #00FF7F; border: 1px solid #00FF7F; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Svuota</button>
                        <button id="cancel-clear" style="background: #1e1e1e; color: #00FF7F; border: 1px solid #333; padding: 6px 12px; border-radius: 6px; cursor: pointer;">Annulla</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(confirmToast);
            lucide.createIcons();
            
            document.getElementById('confirm-clear').onclick = () => {
                const cartItems = document.querySelectorAll('.carrello-item');
                cartItems.forEach(item => {
                    const dishId = item.dataset.dishId;
                    updateCart(dishId, 0);
                });
                document.body.removeChild(confirmToast);
            };
            
            document.getElementById('cancel-clear').onclick = () => {
                document.body.removeChild(confirmToast);
            };
        });
    }

    // Pulsante ordina
    const orderBtn = document.getElementById('order-btn');
    if (orderBtn) {
        orderBtn.addEventListener('click', () => {
            const cartItems = [];
            document.querySelectorAll('.carrello-item').forEach(item => {
                const dishId = item.dataset.dishId;
                const quantity = parseInt(item.querySelector('.cart-quantity-count').textContent);
                const priceText = item.querySelector('.carrello-item-price').textContent.match(/€([\d.]+)/)[1];
                const price = parseFloat(priceText);
                const name = item.querySelector('.carrello-item-name').textContent;
                
                if (quantity > 0) {
                    cartItems.push({
                        id_portata: dishId,
                        nome: name,
                        quantita: quantity,
                        prezzo: price
                    });
                }
            });

            if (cartItems.length === 0) {
                showToast('warning', 'Carrello vuoto', 'Aggiungi almeno un prodotto per ordinare');
                return;
            }

            const orderData = {
                ristorante: '<?php echo urlencode(lcfirst($ristorante)); ?>',
                tableId: '<?php echo $_SESSION['table_id']; ?>',
                tableToken: '<?php echo $_SESSION['table_token']; ?>',
                prodotti: cartItems,
                prezzoTotale: parseFloat(document.getElementById('cart-total').textContent)
            };

            console.log('Invio ordine:', JSON.stringify(orderData));

            // Disabilita il pulsante durante l'invio
            orderBtn.disabled = true;
            orderBtn.innerHTML = '<i data-lucide="loader-2" class="button-icon animate-spin"></i>Invio in corso...';
            
            showToast('info', 'Invio ordine', 'Stiamo elaborando il tuo ordine...');

            fetch('/foodwise/database/add_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            })
            .then(response => {
                console.log('Risposta add_order:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Risposta raw add_order:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Dati add_order:', data);
                    if (data.success) {
                        showToast('success', 'Ordine inviato!', 'Il tuo ordine è stato inviato con successo');
                        setTimeout(() => {
                            window.location.href = '/foodwise/<?php echo urlencode(lcfirst($ristorante)); ?>/menu';
                        }, 2000);
                    } else {
                        console.error('Errore nell\'invio dell\'ordine:', data.message);
                        showToast('error', 'Errore ordine', data.message || 'Impossibile inviare l\'ordine');
                        // Ripristina il pulsante
                        orderBtn.disabled = false;
                        orderBtn.innerHTML = '<i data-lucide="send" class="button-icon"></i>Ordina ora';
                        lucide.createIcons();
                    }
                } catch (e) {
                    console.error('Errore di parsing JSON:', e);
                    showToast('error', 'Errore server', 'Risposta del server non valida');
                    // Ripristina il pulsante
                    orderBtn.disabled = false;
                    orderBtn.innerHTML = '<i data-lucide="send" class="button-icon"></i>Ordina ora';
                    lucide.createIcons();
                }
            })
            .catch(error => {
                console.error('Errore AJAX:', error);
                showToast('error', 'Errore di connessione', 'Problemi di rete, riprova più tardi');
                // Ripristina il pulsante
                orderBtn.disabled = false;
                orderBtn.innerHTML = '<i data-lucide="send" class="button-icon"></i>Ordina ora';
                lucide.createIcons();
            });
        });
    }

    // Inizializza i totali
    updateCartTotals();
});
</script>