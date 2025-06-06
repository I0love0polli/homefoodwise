<?php
// session_start() è già stato chiamato nel file principale index.php che include questa pagina.

// Recupera il nome del ristorante e i dati del tavolo dalla sessione
$ristorante = $_SESSION['ristorante'] ?? null;
$table_token = $_SESSION['table_token'] ?? null;
$table_id = $_SESSION['table_id'] ?? null;

// CONTROLLO FONDAMENTALE: se non ci sono i dati del tavolo, l'utente non può avere un carrello.
// Lo reindirizziamo al menu di quel ristorante (index.php gestirà il resto).
if (!$ristorante || !$table_token || !$table_id) {
    // Reindirizza alla pagina principale se non ci sono dati sufficienti
    header('Location: /foodwise/');
    exit;
}

// Carrello dalla sessione, con un array vuoto come default
$cart = $_SESSION['cart'] ?? [];
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
                Tavolo #<?php echo htmlspecialchars($table_id); ?> - <?php echo count($cart); ?> prodott<?php echo count($cart) > 1 ? 'i' : 'o'; ?>
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

<link rel="stylesheet" href="/foodwise/app/assets/style.css">
<script src="/foodwise/app/assets/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js"></script>

<style>
/* Stili CSS rimangono invariati... */
</style>

<script>
// JavaScript rimane invariato...
</script>
