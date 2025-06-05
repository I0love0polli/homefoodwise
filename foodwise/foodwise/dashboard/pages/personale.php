<div class="container personale-container">
    <div class="row justify-content-center">
        <div class="col-12 mb-4">
            <div class="header-actions mb-4">
                <div class="search-bar-container">
                    <div class="search-bar">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cerca staff..."
                            onkeyup="searchStaff()">
                        <i data-lucide="search" class="search-icon"></i>
                    </div>
                </div>
                <button class="btn btn-action" data-bs-toggle="modal" data-bs-target="#addStaffModal"> <i data-lucide="plus" class="buttons-icon">
                </i>Aggiungi
                    Staff</button>
            </div>

            <!-- Filtri -->
            <div class="filter-tabs mb-4">
                <button class="filter-tab active" onclick="filterStaff('all')">Tutti</button>
                <button class="filter-tab" onclick="filterStaff('manager')">Manager</button>
                <button class="filter-tab" onclick="filterStaff('chef')">Chef</button>
                <button class="filter-tab" onclick="filterStaff('cameriere')">Cameriere</button>
                <button class="filter-tab" onclick="filterStaff('barista')">Barista</button>
                <button class="filter-tab" onclick="filterStaff('cassiere')">Cassiere</button>
            </div>

            <!-- Card dello staff generate dinamicamente -->
            <div class="row staff-container">
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <div class="col-md-6 col-lg-4 mb-4 staff-card" data-role="<?= strtolower($row['ruolo']) ?>"
                        data-name="<?= $row['full_name'] ?>" data-hired="<?= $row['hired'] ?>">

                        <div class="card staff-card-content" onclick="openEditStaffModal(
                                    '<?= $row['username'] ?>', 
                                    '<?= $row['full_name'] ?>', 
                                    '<?= $row['email'] ?>', 
                                    '<?= $row['ruolo'] ?>', 
                                    '<?= $row['telefono'] ?>', 
                                    '<?= $row['hired'] ?>'
                                )">
                            <div class="card-body d-flex align-items-center">
                                <img src="../img/default-staff.jpg" alt="<?= $row['full_name'] ?>" class="staff-image me-3">
                                <div>
                                    <h5 class="card-title"><?= $row['full_name'] ?></h5>
                                    <span
                                        class="staff-role staff-role-<?= strtolower($row['ruolo']) ?>"><?= ucfirst($row['ruolo']) ?></span>
                                    <p class="card-text mb-1"><i data-lucide="mail" class="me-1"></i> <?= $row['email'] ?>
                                    </p>
                                    <p class="card-text mb-1"><i data-lucide="phone" class="me-1"></i>
                                        <?= $row['telefono'] ?></p>
                                    <p class="card-text"><i data-lucide="calendar" class="me-1"></i> Hired:
                                        <?= $row['hired'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal per aggiungere un nuovo membro dello staff -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md"> <!-- Aggiunto modal-lg -->
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="modal-title" id="addStaffModalLabel">Aggiungi Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addStaffForm">
                    <div class="row">
                        <!-- Username -->
                        <div class="col-md-6 mb-3">
                            <label for="staffUsername" class="form-label">Username</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control with-icon" id="staffUsername" placeholder="Username"
                                    name="staffUsername" required>
                                <i data-lucide="user" class="input-icon"></i>
                            </div>
                        </div>
                        <!-- Full Name -->
                        <div class="col-md-6 mb-3">
                            <label for="staffFullName" class="form-label">Nome Cognome</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control with-icon" id="staffFullName" placeholder="Nome e Congome"
                                    name="staffFullName" required>
                                <i data-lucide="users" class="input-icon"></i>
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="staffEmail" class="form-label">Email</label>
                            <div class="input-wrapper">
                                <input type="email" class="form-control with-icon" id="staffEmail" name="staffEmail" placeholder="Email"
                                    required>
                                <i data-lucide="mail" class="input-icon"></i>
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="col-md-6 mb-3">
                            <label for="staffPassword" class="form-label">Password</label>
                            <div class="input-wrapper">
                                <input type="password" class="form-control with-icon" id="staffPassword" placeholder="Password"
                                    name="staffPassword" minlength="6" required>
                                <i data-lucide="lock" class="input-icon"></i>
                            </div>
                        </div>
                        <!-- Telefono -->
                        <div class="col-md-6 mb-3">
                            <label for="staffPhone" class="form-label">Telefono</label>
                            <div class="input-wrapper">
                                <input type="tel" class="form-control with-icon" id="staffPhone" name="staffPhone" placeholder="Telefono"
                                    pattern="[0-9]{10}" required>
                                <i data-lucide="phone" class="input-icon"></i>
                            </div>
                        </div>
                        <!-- ID Ristorante -->
                        <div class="col-md-6 mb-3">
                            <label for="staffIdRistorante" class="form-label">Ristorante</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control with-icon" 
                                    id="staffIdRistorante" 
                                    name="staffIdRistorante" 
                                    value="<?php echo htmlspecialchars($_SESSION['login_restaurant']); ?>" 
                                    readonly>

                                <i data-lucide="utensils" class="input-icon"></i>
                            </div>
                        </div>
                        <!-- Ruolo -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Ruolo</label>
                            <div class="role-buttons">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input role-radio" type="radio" name="staffRole"
                                        id="addRoleManager" value="manager" onchange="selectRoleAdd('manager')">
                                    <label class="form-check-label role-label" for="addRoleManager">Manager</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input role-radio" type="radio" name="staffRole"
                                        id="addRoleChef" value="chef" onchange="selectRoleAdd('chef')">
                                    <label class="form-check-label role-label" for="addRoleChef">Chef</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input role-radio" type="radio" name="staffRole"
                                        id="addRoleCameriere" value="cameriere" onchange="selectRoleAdd('cameriere')">
                                    <label class="form-check-label role-label" for="addRoleCameriere">Cameriere</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input role-radio" type="radio" name="staffRole"
                                        id="addRoleBarista" value="barista" onchange="selectRoleAdd('barista')">
                                    <label class="form-check-label role-label" for="addRoleBarista">Barista</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input role-radio" type="radio" name="staffRole"
                                        id="addRoleCassiere" value="cassiere" onchange="selectRoleAdd('cassiere')">
                                    <label class="form-check-label role-label" for="addRoleCassiere">Cassiere</label>
                                </div>
                            </div>
                            <input type="hidden" id="staffRole" name="staffRole">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-action mt-2 w-100">
                        <i data-lucide="save" class="buttons-icon-modal"></i>
                        Aggiungi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal per modificare un membro dello staff -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="modal-title" id="editStaffModalLabel">Modifica Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="text-center mb-3">
                    <img src="../img/default-staff.jpg" alt="Staff Image" class="staff-image-modal mb-2">
                    <h5 id="editStaffName"></h5>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <div class="role-buttons">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input role-radio" type="radio" name="editStaffRole"
                                id="editRoleManager" value="manager" onchange="selectRole('manager')">
                            <label class="form-check-label role-label" for="editRoleManager">Manager</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input role-radio" type="radio" name="editStaffRole"
                                id="editRoleChef" value="chef" onchange="selectRole('chef')">
                            <label class="form-check-label role-label" for="editRoleChef">Chef</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input role-radio" type="radio" name="editStaffRole"
                                id="editRoleCameriere" value="cameriere" onchange="selectRole('cameriere')">
                            <label class="form-check-label role-label" for="editRoleCameriere">Cameriere</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input role-radio" type="radio" name="editStaffRole"
                                id="editRoleBarista" value="barista" onchange="selectRole('barista')">
                            <label class="form-check-label role-label" for="editRoleBarista">Barista</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input role-radio" type="radio" name="editStaffRole"
                                id="editRoleCassiere" value="cassiere" onchange="selectRole('cassiere')">
                            <label class="form-check-label role-label" for="editRoleCassiere">Cassiere</label>
                        </div>
                    </div>
                    <input type="hidden" id="editStaffRole" name="editStaffRole">
                </div>
                <div class="mb-3">
                    <label for="editStaffUsername" class="form-label">Username</label>
                    <input type="text" class="form-control" id="editStaffUsername" readonly>
                </div>
                <div class="mb-3">
                    <label for="editStaffEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="editStaffEmail" readonly>
                </div>
                <div class="mb-3">
                    <label for="editStaffPhone" class="form-label">Telefono</label>
                    <input type="text" class="form-control" id="editStaffPhone" readonly>
                </div>
                <div class="mb-3">
                    <label for="editStaffHired" class="form-label">Assunto</label>
                    <input type="text" class="form-control" id="editStaffHired" readonly>
                </div>
                <!-- Bottoni in basso -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-action w-50 me-2" onclick="updateStaffRole()"><i data-lucide="save" class="buttons-icon-modal"></i>Aggiorna</button>
                    <button type="button" class="btn btn-delete w-50 ms-2" onclick="deleteStaff()"><i data-lucide="trash-2" class="buttons-icon-modal"></i>Elimina</button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/personale.css">
<script src="assets/script.js"></script>
<script src="assets/js/personale.js"></script>