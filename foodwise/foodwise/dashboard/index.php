<?php
include '../database/connection.php'; // Connessione al database
session_start(); // Avvia la sessione



// --- INIZIO SEZIONE CONTROLLO ACCESSI (RBAC) ---

// 1. DEFINIZIONE DEI RUOLI E DEI PERMESSI
// Qui associamo a ogni ruolo un array di pagine che può visualizzare.
$permissions = [
    'Chef' => ['home', 'analitiche', 'menu', 'ordini'],
    'Cameriere' => ['home', 'menu', 'tavoli', 'ordini'],
    'Cassiere' => ['home', 'ordini', 'pagamenti'],
    // NOTA: Non è necessario aggiungere 'impostazioni' qui, perché viene gestito dalla regola universale nella funzione sotto.
];

// 2. RECUPERA IL RUOLO DELL'UTENTE DALLA SESSIONE
$userRole = isset($_SESSION['ruolo']) ? $_SESSION['ruolo'] : 'guest';

// 3. FUNZIONE PER VERIFICARE I PERMESSI
/**
 * Controlla se un ruolo ha il permesso di visualizzare una pagina.
 * @param string $role Il ruolo dell'utente.
 * @param string $page La pagina richiesta.
 * @param array $permissions L'array dei permessi.
 * @return bool True se autorizzato, altrimenti false.
 */
function canViewPage($role, $page, $permissions) {
    // Regola Aggiunta: Se la sessione 'login_restaurant' è attiva, garantisci l'accesso completo.
    if (isset($_SESSION['login_restaurant']) && !empty($_SESSION['login_restaurant'])) {
        return true;
    }
    // Regola 1: Il manager può vedere tutto.
    if ($role === 'Manager') {
        return true;
    }
    // Regola 2: Tutti possono vedere la pagina 'impostazioni'.
    if ($page === 'impostazioni') {
        return true;
    }
    // Regola 3: Per le altre pagine, controlla i permessi specifici del ruolo.
    if (isset($permissions[$role])) {
        return in_array($page, $permissions[$role]);
    }
    // Per tutti gli altri casi (es. 'guest' o ruoli non definiti), nega l'accesso.
    return false;
}

// --- FINE SEZIONE CONTROLLO ACCESSI ---


// Gestisci la pagina di default se non viene fornita
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Gestisci lo stato della sidebar tramite sessione
$sidebarCollapsed = isset($_SESSION['sidebar_collapsed']) ? $_SESSION['sidebar_collapsed'] : false;

// Aggiorna lo stato della sidebar se viene passato tramite POST (da JavaScript)
if (isset($_POST['collapse_sidebar'])) {
    $sidebarCollapsed = $_POST['collapse_sidebar'] === 'true';
    $_SESSION['sidebar_collapsed'] = $sidebarCollapsed;
}

$title = "Gestione Staff"; // Imposta il titolo della pagina
$conn = connessione(); // Connessione al database PostgreSQL

// Query per ottenere i dati dello staff dal database
$query = "SELECT username, full_name, email, hired, telefono, ruolo 
          FROM personale";
$result = pg_query($conn, $query);

function getInitials($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $w) {
        if (strlen($w) > 0) {
            $initials .= mb_strtoupper(mb_substr($w, 0, 1));
        }
    }
    return $initials;
}

$profileName = "Utente"; // Default
if (!empty($_SESSION['login_restaurant'])) {
    $profileName = $_SESSION['login_restaurant'];
} elseif (!empty($_SESSION['login_username'])) {
    $profileName = $_SESSION['login_username'];
}

$profileInitials = getInitials($profileName);

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="76x76" href="../favicon/76.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon/32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon/16.png">
    <link rel="stylesheet" href="assets/style.css">
    <title>Dashboard</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js" defer></script>
    <script src="assets/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <div class="d-flex">
        <div class="sidebar <?php echo $sidebarCollapsed ? 'collapsed' : ''; ?>">
            <div class="sidebar-header" onclick="toggleSidebar()">
                <i data-lucide="chevron-left" class="collapse-icon"></i>
            </div>
            <hr class="sidebar-divider">
            <div class="nav-container">
                <ul class="nav flex-column mb-auto">
                    
                    <?php if (canViewPage($userRole, 'home', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/home" class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>">
                            <i data-lucide="home" class="nav-icon"></i>
                            <span class="nav-text">Home</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (canViewPage($userRole, 'analitiche', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/analitiche" class="nav-link <?php echo $page === 'analitiche' ? 'active' : ''; ?>">
                            <i data-lucide="bar-chart-3" class="nav-icon"></i>
                            <span class="nav-text">Analitiche</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (canViewPage($userRole, 'menu', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/menu" class="nav-link <?php echo $page === 'menu' ? 'active' : ''; ?>">
                            <i data-lucide="utensils-crossed" class="nav-icon"></i>
                            <span class="nav-text">Menù</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (canViewPage($userRole, 'tavoli', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/tavoli" class="nav-link <?php echo $page === 'tavoli' ? 'active' : ''; ?>">
                            <i data-lucide="layout-dashboard" class="nav-icon"></i>
                            <span class="nav-text">Tavoli</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (canViewPage($userRole, 'ordini', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/ordini" class="nav-link <?php echo $page === 'ordini' ? 'active' : ''; ?>">
                            <i data-lucide="clipboard-list" class="nav-icon"></i>
                            <span class="nav-text">Ordini</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (canViewPage($userRole, 'pagamenti', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/pagamenti" class="nav-link <?php echo $page === 'pagamenti' ? 'active' : ''; ?>">
                            <i data-lucide="credit-card" class="nav-icon"></i>
                            <span class="nav-text">Pagamenti</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (canViewPage($userRole, 'personale', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/personale" class="nav-link <?php echo $page === 'personale' ? 'active' : ''; ?>">
                            <i data-lucide="users" class="nav-icon"></i>
                            <span class="nav-text">Personale</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (canViewPage($userRole, 'impostazioni', $permissions)): ?>
                    <li class="nav-item">
                        <a href="/foodwise/dashboard/impostazioni" class="nav-link <?php echo $page === 'impostazioni' ? 'active' : ''; ?>">
                            <i data-lucide="settings" class="nav-icon"></i>
                            <span class="nav-text">Impostazioni</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="content <?php echo $sidebarCollapsed ? 'collapsed' : ''; ?>">
            <?php
            // Anche la logica qui sotto non cambia, perché eredita il comportamento aggiornato della funzione
            if (canViewPage($userRole, $page, $permissions)) {
                $pagePath = "pages/{$page}.php";
                if (file_exists($pagePath)) {
                    include($pagePath);
                } else {
                    http_response_code(404);
                    echo "<h2>Errore 404: Pagina non trovata.</h2>";
                    echo "<p>La pagina che stai cercando non esiste.</p>";
                }
            } else {
                http_response_code(403); 
                echo "<h2>Accesso Negato</h2>";
                echo "<p>Non disponi delle autorizzazioni necessarie per visualizzare questa pagina.</p>";
            }
            ?>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

</body>

</html>