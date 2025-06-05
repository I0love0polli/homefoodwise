document.addEventListener("DOMContentLoaded", function() {
    console.log('impostazioni.js');
    // Funzione per gestire il fetch dei dati del manager
    function fetchManagerData() {
    const managerUrl = "/foodwise/database/impostazioni/get_user.php";
    fetch(managerUrl)
        .then(response => {
            if (!response.ok) {
                console.log("Dettagli risposta (user):", response.status, response.statusText, "URL:", managerUrl);
                throw new Error("Errore nella risposta del server: " + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log("Dati manager ricevuti:", data);
            if (data.success && data.manager) {
                const manager = data.manager;

                console.log("Popolamento campi con:", manager);
                const fullNameInput = document.getElementById('fullName');
                if (fullNameInput) fullNameInput.value = manager.full_name || '';
                else console.warn("Elemento #fullName non trovato");

                const usernameInput = document.getElementById('username');
                if (usernameInput) usernameInput.value = manager.username || '';
                else console.warn("Elemento #username non trovato");

                const userPhoneInput = document.getElementById('userPhone');
                if (userPhoneInput) userPhoneInput.value = manager.telefono || '';
                else console.warn("Elemento #userPhone non trovato");

                const userEmailInput = document.getElementById('userEmail');
                if (userEmailInput) userEmailInput.value = manager.email || '';
                else console.warn("Elemento #userEmail non trovato");
/*
                const enable2FAInput = document.getElementById('enable2FA');
                if (enable2FAInput) {
                    enable2FAInput.checked = manager.two_factor_enabled === 't';
                } else {
                    console.warn("Elemento #enable2FA non trovato");
                }
*/
                const fullName = manager.full_name || '';
                const userNameElement = document.querySelector('.user-settings .user-name');
                if (userNameElement) {
                    userNameElement.textContent = fullName || 'Nome non disponibile';
                } else {
                    console.warn("Elemento .user-settings .user-name non trovato");
                }

                let initials = '';
                if (fullName) {
                    const parts = fullName.trim().split(' ');
                    if (parts.length >= 1) {
                        initials += parts[0].charAt(0).toUpperCase();
                    }
                    if (parts.length >= 2) {
                        initials += parts[1].charAt(0).toUpperCase();
                    }
                }
                initials = initials || 'NN';
                const initialsElement = document.querySelector('.user-settings .user-initials');
                if (initialsElement) {
                    initialsElement.textContent = initials;
                } else {
                    console.warn("Elemento .user-settings .user-initials non trovato");
                }
            } else {
                console.error("Nessun manager trovato o errore:", data.message || "Dati non validi");
            }
        })
        .catch(error => {
            console.error("Errore nel caricamento dei dati del user:", error);
            console.log("Verifica che il file get_user.php esista in C:\\xampp\\htdocs\\foodwise\\database\\impostazioni\\");
            console.log("Prova ad accedere a: http://localhost/foodwise/database/impostazioni/get_user.php");
        });



}
    

    // Funzione per gestire il fetch dei dati del ristorante
    function fetchRestaurantData() {
        const restaurantUrl = "/foodwise/database/impostazioni/get_ristorante.php";
        fetch(restaurantUrl)
            .then(response => {
                if (!response.ok) {
                    console.log("Dettagli risposta (ristorante):", response.status, response.statusText, "URL:", restaurantUrl);
                    throw new Error("Errore nella risposta del server: " + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log("Dati ristorante ricevuti:", data);
                if (data.success && data.manager) {
                    const restaurant = data.manager;

                    // Popola i campi del form ristorante
                    const restaurantNameInput = document.getElementById('restaurantName');
                    if (restaurantNameInput) {
                        restaurantNameInput.value = restaurant.id_ristorante || '';
                    } else {
                        console.warn("Elemento #restaurantName non trovato");
                    }

                    const addressInput = document.getElementById('address');
                    if (addressInput) {
                        addressInput.value = restaurant.indirizzo || '';
                    } else {
                        console.warn("Elemento #address non trovato");
                    }

                    const phoneInput = document.getElementById('phone');
                    if (phoneInput) {
                        phoneInput.value = restaurant.telefono || '';
                    } else {
                        console.warn("Elemento #phone non trovato");
                    }

                    const emailInput = document.getElementById('email');
                    if (emailInput) {
                        emailInput.value = restaurant.email || '';
                    } else {
                        console.warn("Elemento #email non trovato");
                    }

                    const websiteInput = document.getElementById('website');
                    if (websiteInput) {
                        websiteInput.value = restaurant.sito || '';
                    } else {
                        console.warn("Elemento #website non trovato");
                    }

                    const openingHoursInput = document.getElementById('openingHours');
                    if (openingHoursInput) {
                        openingHoursInput.value = restaurant.ore || '';
                    } else {
                        console.warn("Elemento #openingHours non trovato");
                    }

                    const vatCodeInput = document.getElementById('vatCode');
                    if (vatCodeInput) {
                        vatCodeInput.value = restaurant.iva || '';
                    } else {
                        console.warn("Elemento #vatCode non trovato");
                    }

                    const wifiNameInput = document.getElementById('wifiName');
                    if (wifiNameInput) {
                        wifiNameInput.value = restaurant.nome_wifi || '';
                    } else {
                        console.warn("Elemento #wifiName non trovato");
                    }

                    const wifiPasswordInput = document.getElementById('wifiPassword');
                    if (wifiPasswordInput) {
                        wifiPasswordInput.value = restaurant.pwd_wifi || '';
                    } else {
                        console.warn("Elemento #wifiPassword non trovato");
                    }

                    const acceptCardInput = document.getElementById('acceptCard');
                    if (acceptCardInput) {
                        acceptCardInput.checked = restaurant.carta === 't';
                    } else {
                        console.warn("Elemento #acceptCard non trovato");
                    }

                    const acceptCashInput = document.getElementById('acceptCash');
                    if (acceptCashInput) {
                        acceptCashInput.checked = restaurant.contanti === 't';
                    } else {
                        console.warn("Elemento #acceptCash non trovato");
                    }

                    const acceptAppPaymentsInput = document.getElementById('acceptAppPayments');
                    if (acceptAppPaymentsInput) {
                        acceptAppPaymentsInput.checked = restaurant.pagamenti_app === 't';
                    } else {
                        console.warn("Elemento #acceptAppPayments non trovato");
                    }

                    const coverChargeInput = document.getElementById('coverCharge');
                    if (coverChargeInput) {
                        const coverCharge = restaurant.coperto ? (parseInt(restaurant.coperto) / 100).toFixed(2) : '';
                        coverChargeInput.value = coverCharge;
                    } else {
                        console.warn("Elemento #coverCharge non trovato");
                    }

                    const restaurantImage = document.getElementById('restaurantImage');
                    const restaurantInitials = document.getElementById('restaurantInitials');
                    if (restaurant.url_img && restaurantImage && restaurantInitials) {
                        restaurantImage.src = restaurant.url_img;
                        restaurantImage.style.display = 'block';
                        restaurantInitials.style.display = 'none';
                    } else if (!restaurant.url_img && restaurantInitials) {
                        restaurantInitials.style.display = 'flex';
                        if (restaurantImage) restaurantImage.style.display = 'none';
                    } else {
                        console.warn("Elementi #restaurantImage o #restaurantInitials non trovati");
                    }

                    const restaurantName = restaurant.id_ristorante || '';
                    const restaurantNameElement = document.querySelector('.image-upload-section .user-name');
                    if (restaurantNameElement) {
                        restaurantNameElement.textContent = restaurantName || 'Ristorante non disponibile';
                    } else {
                        console.warn("Elemento .image-upload-section .user-name non trovato");
                    }

                    let restaurantInitialsText = '';
                    if (restaurantName) {
                        const parts = restaurantName.trim().split(' ');
                        if (parts.length >= 1) {
                            restaurantInitialsText += parts[0].charAt(0).toUpperCase();
                        }
                        if (parts.length >= 2) {
                            restaurantInitialsText += parts[1].charAt(0).toUpperCase();
                        }
                    }
                    restaurantInitialsText = restaurantInitialsText || 'NN';
                    if (restaurantInitials) {
                        restaurantInitials.textContent = restaurantInitialsText;
                    }
                } else {
                    console.error("Nessun ristorante trovato o errore:", data.message || "Dati non validi");
                }
            })
            .catch(error => {
                console.error("Errore nel caricamento dei dati del ristorante:", error);
                console.log("Verifica che il file get_ristorante.php esista in C:\\xampp\\htdocs\\foodwise\\database\\impostazioni\\");
                console.log("Prova ad accedere a: http://localhost/foodwise/database/impostazioni/get_ristorante.php");
            });
    }

    if(document.getElementById('userSettingsForm')) {
    document.getElementById('userSettingsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('section', 'userSettings'); // Add section parameter
        
        try {
            const response = await fetch('/foodwise/database/impostazioni/set_user.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
            } else {
                alert('Errore: ' + result.message);
            }
        } catch (error) {
            alert('Errore di rete: ' + error.message);
        }
    });


    }

    // Nuova funzione generica per gestire l'invio dei form
    async function submitForm(formId, url, successMessage) {
        const form = document.getElementById(formId);
        if (!form) {
            console.warn(`Form con ID ${formId} non trovato`);
            return;
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert(successMessage);
                } else {
                    alert('Errore: ' + result.message);
                }
            } catch (error) {
                console.error(`Errore nell'invio del form ${formId}:`, error);
                alert('Errore di rete: ' + error.message);
            }
        });
    }

    // Gestione dei form aggiuntivi
    submitForm('restaurantForm', '/foodwise/database/impostazioni/set_ristorante.php', 'Impostazioni ristorante salvate!');
submitForm('restaurantSecurityForm', '/foodwise/database/impostazioni/set_ristorante.php', 'Impostazioni di sicurezza ristorante salvate!');
submitForm('userSecurityForm', '/foodwise/database/impostazioni/set_user.php', 'Impostazioni di sicurezza utente salvate!');
submitForm('paymentForm', '/foodwise/database/impostazioni/set_ristorante.php', 'Impostazioni pagamenti salvate!');
submitForm('wifiForm', '/foodwise/database/impostazioni/set_ristorante.php', 'Impostazioni WiFi salvate!');
submitForm('userSecurityForm', '/foodwise/database/impostazioni/set_user.php', 'Impostazioni di sicurezza utente salvate!');
    // Gestione delle categorie
    const categoryForm = document.querySelector('#gestione-categorie .btn-action');
    if (categoryForm) {
        categoryForm.addEventListener('click', async function(e) {
            e.preventDefault();
            const categoriesList = document.querySelectorAll('.category-item');
            const categories = [];

            categoriesList.forEach(categoryItem => {
                const categoryTag = categoryItem.querySelector('.category-tag');
                const subcategories = [];
                categoryItem.querySelectorAll('.subcategory-item').forEach(subItem => {
                    const subTag = subItem.querySelector('.subcategory-tag');
                    subcategories.push({
                        name: subTag.textContent,
                        color: subTag.style.color,
                        rgba: subTag.style.backgroundColor
                    });
                });

                categories.push({
                    name: categoryTag.textContent,
                    color: categoryTag.style.color,
                    rgba: categoryTag.style.backgroundColor,
                    subcategories
                });
            });

            try {
                const response = await fetch('/foodwise/database/impostazioni/set_categorie.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ categories })
                });
                const result = await response.json();

                if (result.success) {
                    alert('Categorie salvate!');
                } else {
                    alert('Errore: ' + result.message);
                }
            } catch (error) {
                console.error('Errore nell\'invio delle categorie:', error);
                alert('Errore di rete: ' + error.message);
            }
        });
    }

    // Gestione del modale per aggiungere categorie/sottocategorie
    const addModalSave = document.querySelector('#addModal .btn-save');
    if (addModalSave) {
        addModalSave.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
            const itemName = document.getElementById('itemName').value;
            const selectedColor = document.querySelector('#colorOptions input[name="color"]:checked');
            const color = selectedColor ? selectedColor.value : '';
            const rgba = selectedColor ? selectedColor.dataset.rgba : '';

            if (!itemName) {
                alert('Il nome è obbligatorio');
                return;
            }

            const isCategory = document.getElementById('addModalLabel').textContent.includes('Category');
            if (isCategory) {
                const categoriesList = document.getElementById('categoriesList');
                const newCategory = document.createElement('div');
                newCategory.className = 'category-item';
                newCategory.innerHTML = `
                    <div class="category-header">
                        <span class="category-tag" style="background-color: ${rgba}; color: ${color};">${itemName}</span>
                        <div class="category-actions">
                            <button class="action-btn add-subcategory-btn" onclick="addSubcategory(this)">
                                <i data-lucide="plus" class="action-icon"></i>
                            </button>
                            <button class="action-btn edit-btn" onclick="editCategory(this)">
                                <i data-lucide="edit-2" class="action-icon"></i>
                            </button>
                            <button class="action-btn delete-btn" onclick="deleteCategory(this)">
                                <i data-lucide="trash-2" class="action-icon" style="color: #dc3545;"></i>
                            </button>
                        </div>
                    </div>
                    <div class="subcategory-list"></div>
                    <div class="category-separator"></div>
                `;
                categoriesList.appendChild(newCategory);
            } else {
                // Logica per aggiungere sottocategoria (da implementare se necessario)
                console.log('Aggiunta sottocategoria:', itemName, color, rgba);
            }

            modal.hide();
        });
    }

    // Gestione del modale per modificare categorie/sottocategorie
    const editModalSave = document.querySelector('#editModal .btn-save');
    if (editModalSave) {
        editModalSave.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            const itemName = document.getElementById('editItemName').value;
            const selectedColor = document.querySelector('#editColorOptions input[name="editColor"]:checked');
            const color = selectedColor ? selectedColor.value : '';
            const rgba = selectedColor ? selectedColor.dataset.rgba : '';

            if (!itemName) {
                alert('Il nome è obbligatorio');
                return;
            }

            // Logica per aggiornare categoria/sottocategoria nel DOM
            const isCategory = document.getElementById('editModalLabel').textContent.includes('Category');
            if (isCategory) {
                const activeCategory = document.querySelector('.category-item .category-tag[style*="color:"]'); // Da migliorare per selezionare la categoria corretta
                if (activeCategory) {
                    activeCategory.textContent = itemName;
                    activeCategory.style.color = color;
                    activeCategory.style.backgroundColor = rgba;
                }
            } else {
                // Logica per aggiornare sottocategoria (da implementare se necessario)
                console.log('Modifica sottocategoria:', itemName, color, rgba);
            }

            modal.hide();
        });
    }

    // Esegui il fetch solo se le rispettive viste sono attive
    if (window.sessionSettings && window.sessionSettings.showUserSettings) {
        fetchManagerData();
    }

    if (window.sessionSettings && window.sessionSettings.showRestaurantSettings) {
        fetchRestaurantData();
    }
});