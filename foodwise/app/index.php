<?php
session_start(); // Avvia la sessione

// Determina il ristorante, la pagina, il token e il tableid
$ristorante = isset($_GET['ristorante']) ? $_GET['ristorante'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 'menu';
$token = isset($_GET['token']) ? $_GET['token'] : null;
$tableid = isset($_GET['tableid']) ? $_GET['tableid'] : null;

// Estrai categoria e sottocategoria dai segmenti dell'URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));
$category = 'all';
$subcat = 'tutti';

if (count($segments) >= 3 && $segments[0] === 'foodwise' && $segments[2] === 'menu') {
    if (isset($segments[3]) && !empty($segments[3])) {
        $category = urldecode($segments[3]);
        if (isset($segments[4]) && !empty($segments[4])) {
            $subcat = urldecode($segments[4]);
        }
    }
}

// Sanitizza l'input
$ristorante = $ristorante ? htmlspecialchars($ristorante) : null;
$tableid = $tableid ? htmlspecialchars($tableid) : null;
$category = htmlspecialchars($category);
$subcat = htmlspecialchars($subcat);

// Passa le variabili a menu.php
$_GET['category'] = $category;
$_GET['subcat'] = $subcat;

// Se non c'è un ristorante, reindirizza a /home
if (!$ristorante) {
    header('Location: /foodwise');
    exit;
}

// Controlla se il ristorante è cambiato rispetto alla sessione
if (isset($_SESSION['ristorante']) && $_SESSION['ristorante'] !== $ristorante) {
    unset($_SESSION['table_token']);
    unset($_SESSION['table_id']);
    unset($_SESSION['ristorante']);
}

// Gestisci token e tableid
if ($token || $tableid) {
    if ($token) $_SESSION['table_token'] = $token;
    if ($tableid) $_SESSION['table_id'] = $tableid;
    $_SESSION['ristorante'] = $ristorante;
    if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
        header('Location: /foodwise/' . urlencode($ristorante) . '/' . $page);
        exit;
    }
}

// Reindirizza a /menu se non c'è token e la pagina è carrello o ordini
if (!isset($_SESSION['table_token']) || empty($_SESSION['table_token'])) {
    if ($page === 'carrello' || $page === 'recensioni') {
        header('Location: /foodwise/' . urlencode($ristorante) . '/menu');
        exit;
    }
}

// Determina se mostrare la navbar
$showNavbar = isset($_SESSION['table_token']) && !empty($_SESSION['table_token']);

// Funzione per formattare il titolo del ristorante
function formatTitle($ristorante) {
    // Sostituisci underscore con spazi e capitalizza la prima lettera di ogni parola
    $formatted = str_replace('_', ' ', $ristorante);
    $formatted = ucwords(strtolower($formatted));
    // Rimuovi gli spazi
    $formatted = str_replace(' ', '', $formatted);
    return $formatted;
}

$formattedTitle = $ristorante ? formatTitle($ristorante) : 'Foodwise';

// Gestisci token e tableid
if ($token || $tableid) {
    error_log("Token ricevuto: $token, TableID: $tableid, Ristorante: $ristorante"); // Debug
    if ($token) {
        $_SESSION['table_token'] = $token;
        error_log("Impostato table_token: " . $_SESSION['table_token']); // Debug
    }
    if ($tableid) {
        $_SESSION['table_id'] = $tableid;
        error_log("Impostato table_id: " . $_SESSION['table_id']); // Debug
    }
    $_SESSION['ristorante'] = $ristorante;
    error_log("Sessione attuale: " . print_r($_SESSION, true)); // Debug
    if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
        $redirect_url = '/foodwise/' . urlencode($ristorante) . '/' . $page;
        error_log("Reindirizzamento a: $redirect_url"); // Debug
        header('Location: ' . $redirect_url);
        exit;
    }
}

// Reindirizza a /menu se non c'è token e la pagina è carrello o ordini
error_log("Controllo table_token: " . (isset($_SESSION['table_token']) ? $_SESSION['table_token'] : 'non impostato')); // Debug
if (!isset($_SESSION['table_token']) || empty($_SESSION['table_token'])) {
    if ($page === 'carrello' || $page === 'recensioni') {
        error_log("Reindirizzamento a menu perché table_token non impostato"); // Debug
        header('Location: /foodwise/' . urlencode($ristorante) . '/menu');
        exit;
    }
}

$close=false;

if($close==true){
    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="76x76" href="/foodwise/favicon/76.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/foodwise/favicon/32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/foodwise/favicon/16.png">
    <link rel="shortcut icon" href="/foodwise/favicon/32.png">
    <link rel="stylesheet" href="/foodwise/app/assets/style.css">
    <title><?php echo $formattedTitle; ?></title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js" defer></script>
    <script src="/foodwise/app/assets/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($showNavbar): ?>
        <div class="main-content">
            <?php
            $pagePath = __DIR__ . "/pages/{$page}.php";
            if (file_exists($pagePath)) {
                include($pagePath);
            } else {
                include(__DIR__ . "/pages/menu.php");
            }
            ?>
        </div>
            
        <nav class="bottom-nav">
            <ul class="nav justify-content-between align-items-center w-100">
                <li class="nav-item">
                    <a href="/foodwise/<?php echo urlencode($ristorante); ?>/menu" 
                       class="nav-link <?php echo $page === 'menu' && $category === 'all' ? 'active' : ''; ?>">
                        <i data-lucide="utensils-crossed" class="nav-icon"></i>
                        <span class="nav-text">Menu</span>
                    </a>
                </li>
                <li class="nav-item carrello-container">
                    <span class="carrello-bg-circle"></span>
                    <a href="/foodwise/<?php echo urlencode(lcfirst($ristorante)); ?>/carrello" 
                       class="nav-link carrello <?php echo $page === 'carrello' ? 'active' : ''; ?>">
                        <i data-lucide="shopping-cart" class="nav-icon"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/foodwise/<?php echo urlencode($ristorante); ?>/recensioni" 
                       class="nav-link <?php echo $page === 'recensioni' ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-list" class="nav-icon"></i>
                        <span class="nav-text">Recensioni</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <?php include(__DIR__ . "/pages/menu.php"); ?>
    <?php endif; ?>

    <script>
        // Forza il titolo corretto e blocca override
        document.addEventListener("DOMContentLoaded", function () {
            const title = "<?php echo addslashes($formattedTitle); ?>";
            document.title = title;

            // Funzione per forzare il titolo
            const setTitle = () => {
                if (document.title !== title) {
                    console.warn('Titolo modificato in: ', document.title, 'Forzando a: ', title);
                    document.title = title;
                }
            };

            // Osserva modifiche al titolo
            const observer = new MutationObserver(setTitle);
            observer.observe(document.querySelector('title'), { childList: true, subtree: true });

            // Controllo periodico del titolo
            const titleCheckInterval = setInterval(setTitle, 50);
            setTimeout(() => clearInterval(titleCheckInterval), 20000);

            // Gestisci eventi di navigazione e interazioni
            window.addEventListener('popstate', setTitle);
            window.addEventListener('load', setTitle);
            window.addEventListener('hashchange', setTitle);
            document.addEventListener('click', () => setTimeout(setTitle, 10));
            document.addEventListener('touchstart', () => setTimeout(setTitle, 10));

            // Blocca override del titolo
            Object.defineProperty(document, 'title', {
                set: function (value) {
                    if (value !== title) {
                        console.warn('Tentativo di modificare il titolo in: ', value, 'Stack trace:');
                        console.trace();
                        document.querySelector('title').textContent = title;
                    }
                },
                get: function () {
                    return document.querySelector('title').textContent;
                }
            });

            lucide.createIcons();
        });
    </script>
</body>

</html>