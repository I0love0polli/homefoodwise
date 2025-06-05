document.addEventListener("DOMContentLoaded", () => {
    // Funzione per rendere la prima lettera maiuscola
    function ucfirst(str) {
        if (!str || typeof str !== "string") return "Senza categoria";
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Funzione per validare un URL
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Inizializzazione di Lucide
    if (typeof lucide !== "undefined") {
        lucide.createIcons();
        console.log("Lucide caricato e inizializzato");
    } else {
        console.error("Lucide non è stato caricato");
    }

    // Seleziona gli elementi DOM
    const categoryButtons = document.querySelectorAll(".category-btn");
    const editModal = document.getElementById("editModal");
    const addModal = document.getElementById("addModal");

    // Funzione per filtrare i menu items
    function filterItems(category) {
        const menuItems = document.querySelectorAll(".menu-item");
        menuItems.forEach(item => {
            const itemCategory = item.getAttribute("data-category");

            if (category === "all") {
                item.style.display = "block";
            } else if (itemCategory === category) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    }

    // Funzione di ricerca
    function searchMenu() {
        let input = document.getElementById("searchMenuInput").value.toLowerCase();
        let items = document.getElementsByClassName("menu-item");

        for (let i = 0; i < items.length; i++) {
            let name = items[i].querySelector(".menu-title").textContent.toLowerCase();
            let description = items[i].querySelector(".menu-description").textContent.toLowerCase();
            if (name.includes(input) || description.includes(input)) {
                items[i].style.display = "block";
            } else {
                items[i].style.display = "none";
            }
        }
    }

    // Funzione per caricare le portate dal database
    function loadMenu() {
        fetch('../database/get_menu.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const menuItemsContainer = document.querySelector(".menu-items");
                    menuItemsContainer.innerHTML = ""; // Pulisce il contenitore

                    data.data.forEach(item => {
                        // Recupera la categoria con fallback
                        const mainCategory = item.categoria || "senza-categoria";
                        console.log("Categoria per item", item.nome, ":", mainCategory); // Debug

                        const allergens = item.allergeni || [];
                        const imageUrl = item.url_img && isValidUrl(item.url_img) ? item.url_img : "https://placehold.co/150x150";
                        const isAvailable = item.disponibile === "available";

                        const newItem = document.createElement("div");
                        newItem.className = "menu-item mb-4";
                        newItem.setAttribute("data-id", item.id_portata);
                        newItem.setAttribute("data-category", mainCategory);
                        newItem.setAttribute("data-available", isAvailable);
                        newItem.innerHTML = `
                            <div class="menu-card">
                                <img src="${imageUrl}" class="menu-image" alt="${item.nome}" onerror="this.src='https://placehold.co/150x150';">
                                <div class="menu-content">
                                    <div class="menu-details">
                                        <div class="title-price">
                                            <h5 class="menu-title">${item.nome}</h5>
                                            <span class="price">$${parseFloat(item.prezzo).toFixed(2)}</span>
                                        </div>
                                        <p class="menu-description">${item.descrizione}</p>
                                        <div class="allergens">
                                            ${allergens.map(allergen => {
                                                const iconMap = {
                                                    "gluten": "wheat",
                                                    "dairy": "milk",
                                                    "tree-nuts": "nut",
                                                    "peanuts": "peanut",
                                                    "shellfish": "shrimp",
                                                    "fish": "fish",
                                                    "eggs": "egg",
                                                    "soy": "soy",
                                                    "sesame": "seed",
                                                    "sulfites": "alert-triangle",
                                                    "celery": "sprout",
                                                    "mustard": "leaf",
                                                    "lupin": "flower"
                                                };
                                                const iconName = iconMap[allergen] || "alert-circle";
                                                return `<i data-lucide="${iconName}" class="me-1 text-warning"></i>`;
                                            }).join("")}
                                        </div>
                                    </div>
                                    <div class="menu-footer">
                                        <span class="badge category-badge">${ucfirst(mainCategory)}</span>
                                        <span class="badge availability-badge ${isAvailable ? '' : 'unavailable'}">${isAvailable ? 'Available' : 'Unavailable'}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        menuItemsContainer.appendChild(newItem);

                        // Debug: verifica che l'ID sia impostato correttamente
                        console.log("ID impostato sulla card:", item.id_portata);

                        // Aggiungi evento di click alla card per aprire il modale di modifica
                        newItem.addEventListener("click", () => {
                            openEditModal(newItem);
                        });
                    });

                    // Inizializza le icone
                    lucide.createIcons();

                    // Filtra gli elementi in base alla categoria corrente
                    const activeCategory = document.querySelector(".category-btn.active")?.getAttribute("data-category") || "all";
                    filterItems(activeCategory);
                } else {
                    console.error("Errore nel caricamento del menu:", data.message);
                    alert("Errore durante il caricamento del menu: " + data.message);
                }
            })
            .catch(error => {
                console.error('Errore durante il caricamento del menu:', error);
                alert('Errore durante il caricamento del menu: ' + error.message);
            });
    }

    // Aggiungi evento ai pulsanti delle categorie
    categoryButtons.forEach(button => {
        button.addEventListener("click", () => {
            categoryButtons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");
            const category = button.getAttribute("data-category");
            filterItems(category);
        });
    });

    // Inizializza con "All Items" selezionato
    filterItems("all");

    // Funzione per aprire il modale di modifica
    function openEditModal(item) {
        const id = item.getAttribute("data-id");
        const image = item.querySelector(".menu-image").src;
        const name = item.querySelector(".menu-title").textContent;
        const description = item.querySelector(".menu-description").textContent;
        const price = item.querySelector(".price").textContent.replace("$", "");
        const mainCategory = item.getAttribute("data-category");
        const isAvailable = item.getAttribute("data-available") === "true";
        const allergens = Array.from(item.querySelectorAll(".allergens i[data-lucide]")).map(icon => {
            const iconName = icon.getAttribute("data-lucide");
            const allergenMap = {
                "wheat": "gluten",
                "milk": "dairy",
                "nut": "tree-nuts",
                "peanut": "peanuts",
                "shrimp": "shellfish",
                "fish": "fish",
                "egg": "eggs",
                "soy": "soy",
                "seed": "sesame",
                "alert-triangle": "sulfites",
                "sprout": "celery",
                "leaf": "mustard",
                "flower": "lupin"
            };
            return allergenMap[iconName] || iconName;
        });

        // Debug: verifica il valore dell'ID all'apertura
        console.log("ID della portata da modificare:", id);

        // Salva l'ID come attributo del modale
        editModal.setAttribute("data-item-id", id);

        // Popola il modale con i dati
        const modalItemImage = document.getElementById("modal-item-image");
        const modalItemName = document.getElementById("modal-item-name");
        const modalItemDescription = document.getElementById("modal-item-description");
        const priceInput = document.getElementById("price");
        const imageUrlInput = document.getElementById("image-url");
        const editMainCategoryValue = document.getElementById("edit-main-category-value");
        const editAllergensValue = document.getElementById("edit-allergens-value");

        modalItemImage.src = image;
        modalItemName.value = name;
        modalItemDescription.value = description;
        priceInput.value = price;
        imageUrlInput.value = image;

        // Pre-seleziona la categoria principale
        const categoryPlaceholder = document.querySelector("#edit-category .category-placeholder");
        if (categoryPlaceholder) {
            categoryPlaceholder.textContent = ucfirst(mainCategory);
        }
        editMainCategoryValue.value = mainCategory;

        // Pre-seleziona gli allergeni
        const allergenButtons = document.querySelectorAll("#editModal .allergen-btn");
        allergenButtons.forEach(btn => {
            btn.classList.remove("active");
            const allergen = btn.getAttribute("data-allergen");
            if (allergens.includes(allergen)) {
                btn.classList.add("active");
            }
        });
        const allergensPlaceholder = document.querySelector("#edit-allergens .allergens-placeholder");
        if (allergensPlaceholder) {
            allergensPlaceholder.textContent = allergens.length > 0 ? allergens.join(", ") : "No allergens selected";
        }
        editAllergensValue.value = allergens.join(",");

        // Inizializza le icone nel modale
        lucide.createIcons();

        // Aggiungi eventi ai pulsanti di eliminazione e "Mark Unavailable"
        const deleteBtn = document.querySelector("#editModal .delete-btn");
        const markUnavailableBtn = document.querySelector("#editModal .mark-unavailable-btn");

        deleteBtn.onclick = () => {
            const itemId = editModal.getAttribute("data-item-id");
            console.log("ID passato al delete:", itemId);
            deleteMenuItem(itemId);
        };

        markUnavailableBtn.onclick = () => {
            const itemId = editModal.getAttribute("data-item-id");
            markUnavailable(itemId, isAvailable ? "unavailable" : "available");
        };

        // Aggiorna il testo del pulsante "Mark Unavailable"
        markUnavailableBtn.textContent = isAvailable ? "Mark Unavailable" : "Mark Available";

        // Mostra il modale
        const modal = new bootstrap.Modal(editModal);
        modal.show();
    }

    // Funzione per eliminare una portata
    function deleteMenuItem(id) {
        const parsedId = parseInt(id, 10);
        if (!id || isNaN(parsedId) || parsedId <= 0) {
            console.error("ID non valido:", id);
            alert("Errore: ID della portata non valido.");
            return;
        }

        console.log("Eliminazione della portata con ID:", parsedId);

        const formData = new FormData();
        formData.append("action", "delete_menu");
        formData.append("id", parsedId);
        formData.append("id_ristorante", "Arlecchino");

        fetch('../database/delete_menu.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMenu();
                const modal = bootstrap.Modal.getInstance(editModal);
                modal.hide();
            } else {
                alert("Errore durante l'eliminazione: " + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'eliminazione:', error);
            alert('Errore durante l\'eliminazione: ' + error.message);
        });
    }

    // Funzione per marcare una portata come disponibile/non disponibile
    function markUnavailable(id, status) {
        const parsedId = parseInt(id, 10);
        if (!id || isNaN(parsedId) || parsedId <= 0) {
            console.error("ID non valido:", id);
            alert("Errore: ID della portata non valido.");
            return;
        }

        console.log("Aggiornamento stato della portata con ID:", parsedId, "a", status);

        const formData = new FormData();
        formData.append("action", "update_availability");
        formData.append("id", parsedId);
        formData.append("id_ristorante", "Arlecchino");
        formData.append("disponibile", status);

        fetch('../database/update_menu.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMenu();
                const modal = bootstrap.Modal.getInstance(editModal);
                modal.hide();
            } else {
                alert("Errore durante l'aggiornamento dello stato: " + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'aggiornamento:', error);
            alert('Errore durante l\'aggiornamento: ' + error.message);
        });
    }

    // Gestione del modale di modifica
    editModal.addEventListener("show.bs.modal", () => {
        const categoryMainButtons = document.querySelectorAll("#editModal .category-main");
        categoryMainButtons.forEach(btn => btn.classList.remove("active"));

        const allergenButtons = document.querySelectorAll("#editModal .allergen-btn");
        allergenButtons.forEach(btn => btn.classList.remove("active"));

        lucide.createIcons();
    });

    // Gestione del dropdown delle categorie nel modale di modifica
    const editCategoryMainButtons = document.querySelectorAll("#editModal .category-main");
    editCategoryMainButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            editCategoryMainButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const category = btn.getAttribute("data-category");
            document.getElementById("edit-main-category-value").value = category;
            document.querySelector("#edit-category .category-placeholder").textContent = ucfirst(category);
        });
    });

    // Gestione del dropdown degli allergeni nel modale di modifica
    const editAllergenButtons = document.querySelectorAll("#editModal .allergen-btn");
    editAllergenButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            btn.classList.toggle("active");
            const selectedAllergens = Array.from(document.querySelectorAll("#editModal .allergen-btn.active")).map(b => b.getAttribute("data-allergen"));
            document.getElementById("edit-allergens-value").value = selectedAllergens.join(",");
            document.querySelector("#edit-allergens .allergens-placeholder").textContent = selectedAllergens.length > 0 ? selectedAllergens.join(", ") : "No allergens selected";
        });
    });

    // Gestione del pulsante "Done" nei dropdown del modale di modifica
    const editDoneButtons = document.querySelectorAll("#editModal .done-btn");
    editDoneButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const dropdown = btn.closest(".dropdown");
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdown.querySelector(".dropdown-toggle"));
            if (bsDropdown) {
                bsDropdown.hide();
            }
        });
    });

    // Gestione del pulsante "Update" nel modale di modifica
    const updateBtn = document.querySelector(".update-btn");
    updateBtn.addEventListener("click", () => {
        const id = editModal.getAttribute("data-item-id");
        const name = document.getElementById("modal-item-name").value.trim();
        const description = document.getElementById("modal-item-description").value.trim();
        const price = document.getElementById("price").value.trim();
        const imageUrl = document.getElementById("image-url").value.trim();
        const mainCategory = document.getElementById("edit-main-category-value").value.trim();
        const allergens = document.getElementById("edit-allergens-value").value.split(",").filter(a => a);

        // Validazione dei campi obbligatori
        if (!id || isNaN(parseInt(id, 10)) || parseInt(id, 10) <= 0) {
            console.error("ID non valido:", id);
            alert("Errore: ID della portata non valido.");
            return;
        }
        if (!name || !description || !price || !mainCategory) {
            console.error("Campi obbligatori mancanti:", { name, description, price, mainCategory });
            alert("Compila tutti i campi obbligatori: Nome, Descrizione, Prezzo e Categoria.");
            return;
        }
        if (isNaN(price) || parseFloat(price) < 0) {
            console.error("Prezzo non valido:", price);
            alert("Il prezzo deve essere un numero valido e maggiore o uguale a 0.");
            return;
        }

        const formData = new FormData();
        formData.append("action", "update_menu");
        formData.append("id", id);
        formData.append("nome", name);
        formData.append("descrizione", description);
        formData.append("prezzo", price);
        formData.append("url_img", imageUrl);
        formData.append("category", mainCategory);
        formData.append("allergeni", JSON.stringify(allergens));
        formData.append("id_ristorante", "Arlecchino");

        // Debug: Log dei dati inviati
        console.log("Dati inviati al server:", {
            id,
            nome: name,
            descrizione: description,
            prezzo: price,
            url_img: imageUrl,
            category: mainCategory,
            allergeni: allergens,
            id_ristorante: "Arlecchino"
        });

        fetch('../database/update_menu.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMenu();
                const modal = bootstrap.Modal.getInstance(editModal);
                modal.hide();
            } else {
                console.error("Errore dal server:", data.message);
                alert("Errore durante l'aggiornamento: " + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'aggiornamento:', error);
            alert('Errore durante l\'aggiornamento: ' + error.message);
        });
    });

    // Gestione del modale di aggiunta
    addModal.addEventListener("show.bs.modal", () => {
        document.getElementById("add-item-name").value = "";
        document.getElementById("add-item-description").value = "";
        document.getElementById("add-price").value = "";
        document.getElementById("add-image-url").value = "";
        document.getElementById("add-main-category-value").value = "";
        document.getElementById("add-allergens-value").value = "";
        document.querySelector("#add-category .category-placeholder").textContent = "Select a category";
        document.querySelector("#add-allergens .allergens-placeholder").textContent = "No allergens selected";

        const categoryMainButtons = document.querySelectorAll("#addModal .category-main");
        categoryMainButtons.forEach(btn => btn.classList.remove("active"));

        const allergenButtons = document.querySelectorAll("#addModal .allergen-btn");
        allergenButtons.forEach(btn => btn.classList.remove("active"));

        lucide.createIcons();
    });

    // Gestione del dropdown delle categorie nel modale di aggiunta
    const addCategoryMainButtons = document.querySelectorAll("#addModal .category-main");
    addCategoryMainButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            addCategoryMainButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const category = btn.getAttribute("data-category");
            document.getElementById("add-main-category-value").value = category;
            document.querySelector("#add-category .category-placeholder").textContent = ucfirst(category);
        });
    });

    // Gestione del dropdown degli allergeni nel modale di aggiunta
    const addAllergenButtons = document.querySelectorAll("#addModal .allergen-btn");
    addAllergenButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            btn.classList.toggle("active");
            const selectedAllergens = Array.from(document.querySelectorAll("#addModal .allergen-btn.active")).map(b => b.getAttribute("data-allergen"));
            document.getElementById("add-allergens-value").value = selectedAllergens.join(",");
            document.querySelector("#add-allergens .allergens-placeholder").textContent = selectedAllergens.length > 0 ? selectedAllergens.join(", ") : "No allergens selected";
        });
    });

    // Gestione del pulsante "Done" nei dropdown del modale di aggiunta
    const addDoneButtons = document.querySelectorAll("#addModal .done-btn");
    addDoneButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const dropdown = btn.closest(".dropdown");
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdown.querySelector(".dropdown-toggle"));
            if (bsDropdown) {
                bsDropdown.hide();
            }
        });
    });

    // Gestione del pulsante "Add Item" nel modale di aggiunta
    const addBtn = document.querySelector(".add-btn");
    addBtn.addEventListener("click", () => {
        const name = document.getElementById("add-item-name").value.trim();
        const description = document.getElementById("add-item-description").value.trim();
        const price = document.getElementById("add-price").value.trim();
        const imageUrl = document.getElementById("add-image-url").value.trim();
        const mainCategory = document.getElementById("add-main-category-value").value.trim();
        const allergens = document.getElementById("add-allergens-value").value.split(",").filter(a => a);

        // Validazione dei campi obbligatori
        if (!name || !description || !price || !mainCategory) {
            console.error("Campi obbligatori mancanti:", { name, description, price, mainCategory });
            alert("Compila tutti i campi obbligatori: Nome, Descrizione, Prezzo e Categoria.");
            return;
        }

        if (isNaN(price) || parseFloat(price) < 0) {
            console.error("Prezzo non valido:", price);
            alert("Il prezzo deve essere un numero valido e maggiore o uguale a 0.");
            return;
        }

        const formData = new FormData();
        formData.append("action", "add_menu");
        formData.append("nome", name);
        formData.append("descrizione", description);
        formData.append("prezzo", price);
        formData.append("url_img", imageUrl);
        formData.append("category", mainCategory);
        formData.append("allergeni", JSON.stringify(allergens));
        formData.append("id_ristorante", "Arlecchino");

        fetch('../database/add_menu.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMenu();
                const modal = bootstrap.Modal.getInstance(addModal);
                modal.hide();
            } else {
                alert("Errore durante l'aggiunta: " + data.message);
            }
        })
        .catch(error => {
            console.error('Errore durante l\'aggiunta:', error);
            alert('Errore durante l\'aggiunta: ' + error.message);
        });
    });

    // Carica il menu all'avvio
    loadMenu();

    // Esporta la funzione di ricerca per l'uso nell'HTML
    window.searchMenu = searchMenu;
});