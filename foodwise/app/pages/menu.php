<?php
session_start();

// Parametri GET per categoria - di default mostra tutto
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'all';

// Ristorante dalla sessione
$ristorante = isset($_SESSION['login_restaurant']) ? $_SESSION['login_restaurant'] : null;
if (!$ristorante) {
    header('Location: /foodwise/');
    exit;
}

// Determina se la navbar è visibile
$showNavbar = isset($_SESSION['table_token']) && !empty($_SESSION['table_token']);

// Mappatura degli allergeni per le icone Lucide
$allergen_map = [
    'gluten' => 'wheat', 'dairy' => 'milk', 'egg' => 'egg', 'nut' => 'nut',
    'shellfish' => 'shell', 'fish' => 'fish', 'sesame' => 'sprout', 'soy' => 'bean',
    'crustaceans' => 'shrimp', 'lupin' => 'flower', 'mustard' => 'droplet',
    'celery' => 'leafy-green', 'sulfites' => 'flask-conical', 'beef' => 'ham', 'vegan' => 'vegan'
];

// Categorie
$categories = [
    'all' => 'Tutto',
    'pizza' => 'Pizza',
    'pasta' => 'Pasta',
    'dessert' => 'Dessert',
    'other' => 'Altro'
];
?>

<div class="container menu-container">
    <div class="menu-header">
        <h1 class="menu-title">Menu</h1>
        <div class="menu-categories d-flex gap-2 mb-3 flex-wrap">
            <?php foreach ($categories as $key => $label): ?>
                <?php
                $active = $category === $key ? 'active' : '';
                $url = $key === 'all' 
                    ? "/foodwise/" . urlencode(lcfirst($ristorante)) . "/menu?category=all"
                    : "/foodwise/" . urlencode(lcfirst($ristorante)) . "/menu/$key";
                ?>
                <button type="button" class="category-btn btn rounded-pill <?php echo $active; ?>" 
                        onclick="navigateTo('<?php echo $url; ?>')">
                    <?php echo $label; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="menu-content">
        <div class="menu-items">
            <div class="row g-4" id="menu-items-container">
                <!-- I piatti verranno caricati dinamicamente tramite JavaScript -->
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/foodwise/app/assets/css/menu.css">
<script src="/foodwise/app/assets/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    window.navigateTo = function(url) {
        window.location.href = url;
        setTimeout(() => { document.title = 'Menu'; }, 50);
        setTimeout(() => { document.title = 'Menu'; }, 100);
        setTimeout(() => { document.title = 'Menu'; }, 500);
    };

    function loadMenuItems() {
        const ristorante = '<?php echo urlencode(lcfirst($ristorante)); ?>';
        const currentCategory = '<?php echo $category; ?>';

        console.log('=== DEBUG INFO ===');
        console.log('PHP $category:', '<?php echo $category; ?>');
        console.log('JavaScript currentCategory:', currentCategory);
        console.log('URL corrente:', window.location.href);
        console.log('==================');

        console.log('Caricamento piatti per:', JSON.stringify({ ristorante, currentCategory }));

        fetch(`/foodwise/database/get_menu_app.php?ristorante=${ristorante}`)
            .then(response => {
                console.log('Risposta fetch:', response.status, response.statusText);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.text();
            })
            .then(text => {
                console.log('Risposta raw:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Dati parsati:', data);
                    const container = document.getElementById('menu-items-container');
                    container.innerHTML = '';

                    if (data.success && data.data && data.data.length > 0) {
                        let piattiVisualizzati = 0;
                        data.data.forEach(item => {
                            const itemCategory = item.categoria || '';
                            console.log('Piatto:', JSON.stringify({ 
                                nome: item.nome, 
                                id_portata: item.id_portata, 
                                itemCategory,
                                currentCategory,
                                shouldShow: currentCategory === 'all' || itemCategory.toLowerCase() === currentCategory.toLowerCase()
                            }));

                            // Filtra solo per categoria principale
                            if (currentCategory !== 'all' && itemCategory.toLowerCase() !== currentCategory.toLowerCase()) {
                                console.log('Piatto filtrato:', item.nome);
                                return;
                            }
                            
                            console.log('Piatto mostrato:', item.nome);

                            piattiVisualizzati++;
                            const card = document.createElement('div');
                            card.className = `col-12 card-dishes ${item.disponibile === 'unavailable' ? 'unavailable' : ''}`;
                            card.innerHTML = `
                                <div class="card dish-card d-flex flex-row align-items-stretch">
                                    <img src="${item.url_img || 'https://placehold.co/150x150?text=No+Image'}" class="dish-img" alt="${item.nome}">
                                    <div class="card-body d-flex flex-row justify-content-between w-100">
                                        <div class="dish-info">
                                            <h5 class="card-title">${item.nome}</h5>
                                            <p class="card-text">${item.descrizione || 'Nessuna descrizione'}</p>
                                            <div class="dish-allergens">
                                                ${(item.allergeni || []).map(allergen => {
                                                    const iconName = <?php echo json_encode($allergen_map); ?>;
                                                    const icon = iconName[allergen] || 'alert-circle';
                                                    return `<i data-lucide="${icon}" class="allergen-icon allergen-${allergen}"></i>`;
                                                }).join('')}
                                            </div>
                                        </div>
                                        <div class="dish-actions d-flex flex-column align-items-end justify-content-between">
                                            <span class="price">€${(item.prezzo || 0).toFixed(2)}</span>
                                            <button class="action-btn add-category-btn" 
                                                    data-dish-id="${item.id_portata}" 
                                                    ${item.disponibile === 'unavailable' ? 'disabled' : ''}>
                                                <i data-lucide="plus" class="action-icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.appendChild(card);
                        });

                        lucide.createIcons();
                        attachAddButtonListeners();

                        console.log('Totale piatti visualizzati:', piattiVisualizzati);
                        
                        if (piattiVisualizzati === 0) {
                            console.log('Nessun piatto da mostrare per categoria:', currentCategory);
                            container.innerHTML = '<p>Nessun piatto disponibile per questa categoria.</p>';
                        }
                    } else {
                        console.log('Nessun dato ricevuto o errore:', data);
                        container.innerHTML = '<p>' + (data.message || 'Nessun piatto trovato.') + '</p>';
                    }
                } catch (e) {
                    console.error('Errore di parsing JSON:', e);
                    container.innerHTML = '<p>Errore nel formato dei dati ricevuti.</p>';
                }
            })
            .catch(error => {
                console.error('Errore nella fetch:', error);
                document.getElementById('menu-items-container').innerHTML = '<p>Errore nel caricamento del menu: ' + error.message + '</p>';
            });
    }

function attachAddButtonListeners() {
    const addButtons = document.querySelectorAll('.add-category-btn');
    console.log('Pulsanti trovati:', addButtons.length);
    addButtons.forEach(button => {
        const dishId = button.getAttribute('data-dish-id');
        const parent = button.parentNode;
        let count = 0;

        console.log('Associando listener al pulsante con dishId:', dishId);
        button.addEventListener('click', () => {
            console.log('Pulsante cliccato, dishId:', dishId);
            if (count === 0) {
                console.log('Creando quantity-group per dishId:', dishId);
                const group = document.createElement('div');
                group.className = 'quantity-group';
                group.style.display = 'flex';
                group.style.alignItems = 'center';
                group.style.transition = 'all 0.3s ease';

                const minusBtn = document.createElement('button');
                minusBtn.className = 'quantity-btn minus-btn';
                minusBtn.innerHTML = '<i data-lucide="minus" class="action-icon minus-icon"></i>';
                minusBtn.style.border = 'none';
                minusBtn.style.borderRadius = '50% 0 0 50%';
                minusBtn.style.width = '28px';
                minusBtn.style.height = '28px';
                minusBtn.style.cursor = 'pointer';
                minusBtn.style.margin = '0';
                minusBtn.style.padding = '0';

                const countDisplay = document.createElement('span');
                countDisplay.className = 'quantity-count';
                countDisplay.textContent = '1';
                countDisplay.style.backgroundColor = '#121212';
                countDisplay.style.color = 'white';
                countDisplay.style.width = '28px';
                countDisplay.style.height = '28px';
                countDisplay.style.display = 'flex';
                countDisplay.style.alignItems = 'center';
                countDisplay.style.justifyContent = 'center';
                countDisplay.style.fontSize = '1rem';

                const plusBtn = document.createElement('button');
                plusBtn.className = 'quantity-btn plus-btn';
                plusBtn.innerHTML = '<i data-lucide="plus" class="action-icon plus-icon"></i>';
                plusBtn.style.border = 'none';
                plusBtn.style.borderRadius = '0 50% 50% 0';
                plusBtn.style.width = '28px';
                plusBtn.style.height = '28px';
                plusBtn.style.cursor = 'pointer';
                plusBtn.style.margin = '0';
                plusBtn.style.padding = '0';

                group.appendChild(minusBtn);
                group.appendChild(countDisplay);
                group.appendChild(plusBtn);

                parent.replaceChild(group, button);
                count = 1;

                if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
                    lucide.createIcons();
                } else {
                    console.error('Libreria Lucide non caricata');
                }

                addToCart(dishId, count);

                minusBtn.addEventListener('click', () => {
                    console.log('Pulsante meno cliccato, dishId:', dishId, 'nuova quantità:', count - 1);
                    count--;
                    countDisplay.textContent = count;
                    addToCart(dishId, count);
                    if (count === 0) {
                        const newButton = document.createElement('button');
                        newButton.className = 'action-btn add-category-btn';
                        newButton.setAttribute('data-dish-id', dishId);
                        newButton.innerHTML = '<i data-lucide="plus" class="action-icon"></i>';
                        parent.replaceChild(newButton, group);
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                        attachAddButtonListeners(); // Riattacca i listener
                    }
                });

                plusBtn.addEventListener('click', () => {
                    console.log('Pulsante più cliccato, dishId:', dishId, 'nuova quantità:', count + 1);
                    count++;
                    countDisplay.textContent = count;
                    addToCart(dishId, count);
                });
            }
        });
    });
}

    function addToCart(dishId, quantity) {
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
            if (!data.success) console.error('Errore nell\'aggiunta al carrello:', data.message);
        })
        .catch(error => console.error('Errore AJAX:', error));
    }

    loadMenuItems();
});
</script>