<div class="container menu-container">
    <!-- Header del Menù con Searchbar e Pulsanti -->
    <div class="row justify-content-center">
        <div class="col-12 mb-4">
            <div class="header-actions mb-4">
                <div class="search-bar-container">
                    <div class="search-bar">
                        <input type="text" id="searchMenuInput" class="form-control" placeholder="Cerca elemento del menu..." onkeyup="searchMenu()">
                        <i data-lucide="search" class="search-icon"></i>
                    </div>
                </div>
                <div class="button-group">
                    <button class="btn btn-add-item" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i data-lucide="plus" class="add-item-icon"></i>Aggiungi Elemento
                    </button>
                </div>
            </div>

            <!-- Filtri Categorie -->
            <div class="menu-categories d-flex gap-2 mb-3 flex-wrap">
                <button type="button" class="category-btn btn btn-outline-success rounded-pill active" data-category="all" aria-label="Filter by all items">All Items</button>
                <button type="button" class="category-btn btn btn-outline-danger rounded-pill" data-category="pizza" aria-label="Filter by pizza">Pizza</button>
                <button type="button" class="category-btn btn btn-outline-primary rounded-pill" data-category="pasta" aria-label="Filter by pasta">Pasta</button>
                <button type="button" class="category-btn btn btn-outline-purple rounded-pill" data-category="desserts" aria-label="Filter by desserts">Desserts</button>
                <button type="button" class="category-btn btn btn-outline-success rounded-pill" data-category="other" aria-label="Filter by other">Other</button>
            </div>
        </div>
    </div>

    <!-- Lista dei Menu Items -->
    <div class="menu-items">
        <!-- Le card verranno popolate dinamicamente via JS -->
    </div>

<!-- Modale per Modifica -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <img id="modal-item-image" src="" alt="Item Image" class="img-fluid rounded" style="max-height: 150px;">
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="modal-item-name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="modal-item-name">
                    </div>
                    <div class="col-md-6">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" step="0.01">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <div class="dropdown" id="edit-category">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start category-placeholder" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Select a category
                            </button>
                            <input type="hidden" id="edit-main-category-value">
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item category-main" href="#" data-category="pizza">Pizza</a></li>
                                <li><a class="dropdown-item category-main" href="#" data-category="pasta">Pasta</a></li>
                                <li><a class="dropdown-item category-main" href="#" data-category="desserts">Desserts</a></li>
                                <li><a class="dropdown-item category-main" href="#" data-category="other">Other</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Allergens</label>
                        <div class="dropdown" id="edit-allergens">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start allergens-placeholder" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                No allergens selected
                            </button>
                            <input type="hidden" id="edit-allergens-value">
                            <ul class="dropdown-menu w-100">
                                <li>
                                    <div class="d-flex flex-wrap gap-2 p-2">
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="gluten"><i data-lucide="wheat"></i> Gluten</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="dairy"><i data-lucide="milk"></i> Dairy</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="tree-nuts"><i data-lucide="nut"></i> Tree Nuts</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="shellfish"><i data-lucide="shrimp"></i> Shellfish</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="fish"><i data-lucide="fish"></i> Fish</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="eggs"><i data-lucide="egg"></i> Eggs</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="sulfites"><i data-lucide="alert-triangle"></i> Sulfites</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="celery"><i data-lucide="sprout"></i> Celery</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="mustard"><i data-lucide="leaf"></i> Mustard</button>
                                        <button type="button" class="allergen-btn btn btn-outline-warning rounded-pill" data-allergen="lupin"><i data-lucide="flower"></i> Lupin</button>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item done-btn" type="button">Done</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="modal-item-description" class="form-label">Description</label>
                        <textarea class="form-control" id="modal-item-description"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="image-url" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="image-url">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger delete-btn">Delete</button>
                <button type="button" class="btn btn-warning mark-unavailable-btn">Mark Unavailable</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary update-btn">Update</button>
            </div>
        </div>
    </div>
</div>

    <!-- Modale per Aggiunta -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-item-name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="add-item-name" placeholder="Enter item name">
                        </div>
                        <div class="col-md-6">
                            <label for="add-price" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="add-price" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-category" class="form-label">Category</label>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="add-category" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="category-placeholder">Select a category</span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="add-category">
                                    <li>
                                        <h6 class="dropdown-header">Select a category:</h6>
                                    </li>
                                    <li>
                                        <div class="d-flex flex-wrap gap-2 p-2 category-main-selection">
                                            <button type="button" class="category-main btn btn-outline-danger rounded-pill" data-category="pizza">Pizza</button>
                                            <button type="button" class="category-main btn btn-outline-primary rounded-pill" data-category="pasta">Pasta</button>
                                            <button type="button" class="category-main btn btn-outline-purple rounded-pill" data-category="desserts">Desserts</button>
                                            <button type="button" class="category-main btn btn-outline-success rounded-pill" data-category="other">Other</button>
                                        </div>
                                    </li>
                                    <li>
                                        <button class="btn btn-success w-100 mt-2 done-btn" type="button">Done</button>
                                    </li>
                                </ul>
                                <input type="hidden" id="add-main-category-value">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="add-allergens" class="form-label">Allergens</label>
                            <div class="dropdown dropup">
                                <button class="btn btn-secondary dropdown-toggle w-100 text-start" type="button" id="add-allergens" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="allergens-placeholder">No allergens selected</span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="add-allergens">
                                    <li>
                                        <h6 class="dropdown-header">Select allergens contained in this item:</h6>
                                    </li>
                                    <li>
                                        <div class="d-flex flex-wrap gap-2 p-2">
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="gluten"><i data-lucide="wheat" class="me-1"></i> Gluten</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="dairy"><i data-lucide="milk" class="me-1"></i> Dairy</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="tree-nuts"><i data-lucide="nut" class="me-1"></i> Tree Nuts</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="shellfish"><i data-lucide="shrimp" class="me-1"></i> Shellfish</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="fish"><i data-lucide="fish" class="me-1"></i> Fish</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="eggs"><i data-lucide="egg" class="me-1"></i> Eggs</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="wheat"><i data-lucide="wheat" class="me-1"></i> Wheat</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="sulfites"><i data-lucide="alert-triangle" class="me-1"></i> Sulfites</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="celery"><i data-lucide="sprout" class="me-1"></i> Celery</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="mustard"><i data-lucide="leaf" class="me-1"></i> Mustard</button>
                                            <button type="button" class="allergen-btn btn btn-outline-warning" data-allergen="lupin"><i data-lucide="flower" class="me-1"></i> Lupin</button>
                                        </div>
                                    </li>
                                    <li>
                                        <button class="btn btn-success w-100 mt-2 done-btn" type="button">Done</button>
                                    </li>
                                </ul>
                                <input type="hidden" id="add-allergens-value">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="add-item-description" class="form-label">Description</label>
                            <textarea class="form-control" id="add-item-description" rows="3" placeholder="Enter description"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="add-image-url" class="form-label">Image URL</label>
                            <input type="text" class="form-control" id="add-image-url" placeholder="Enter image URL (optional)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success add-btn">Add Item</button>
                    <button type="button" class="btn btn-secondary cancel-btn" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="assets/css/menu.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="assets/js/menu.js"></script>
    <script src="assets/script.js"></script>
</div>