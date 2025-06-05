<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

include '../database/connection.php';
$conn = connessione();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_menu') {
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Errore di connessione al database']);
        exit;
    }

    if (!isset($_POST['nome'], $_POST['descrizione'], $_POST['prezzo'], $_POST['category'])) {
        echo json_encode(['success' => false, 'message' => 'Campi obbligatori mancanti']);
        pg_close($conn);
        exit;
    }

    $nome = pg_escape_string($conn, $_POST['nome']);
    $descrizione = pg_escape_string($conn, $_POST['descrizione']);
    $prezzo = floatval($_POST['prezzo']);

    if (!is_numeric($prezzo) || $prezzo < 0) {
        echo json_encode(['success' => false, 'message' => 'Prezzo non valido']);
        pg_close($conn);
        exit;
    }

    // La categoria è salvata come testo puro (stringa) nella colonna 'categoria'
    $categoria = pg_escape_string($conn, $_POST['category']);

    $allergeniArray = [];
    if (isset($_POST['allergeni']) && !empty($_POST['allergeni'])) {
        $allergeniArray = json_decode($_POST['allergeni'], true);
        if (!is_array($allergeniArray)) {
            echo json_encode(['success' => false, 'message' => 'Formato allergeni non valido']);
            pg_close($conn);
            exit;
        }
        $allergeniArray = array_filter(array_map('trim', $allergeniArray));
    }
    $allergeni = json_encode($allergeniArray);

    $urlImg = isset($_POST['url_img']) && !empty($_POST['url_img']) ? pg_escape_string($conn, $_POST['url_img']) : null;

    // Recupera l'id_ristorante dalla sessione
    if (!isset($_SESSION['login_restaurant'])) {
        echo json_encode(['success' => false, 'message' => 'ID ristorante mancante nella sessione']);
        pg_close($conn);
        exit;
    }
    $idRistorante = $_SESSION['login_restaurant'];
    $disponibile = 'available';

    // Inserisci nella tabella prodotti
    $queryProdotto = "INSERT INTO prodotti (nome, descrizione, prezzo, allergeni, categoria, url_img, disponibile)
                      VALUES ($1, $2, $3, $4::jsonb, $5, $6, $7) RETURNING id_portata";
    $paramsProdotto = [
        $nome,
        $descrizione,
        $prezzo,
        $allergeni,
        $categoria,
        $urlImg,
        $disponibile,
    ];

    $resultProdotto = pg_query_params($conn, $queryProdotto, $paramsProdotto);

    if ($resultProdotto && pg_num_rows($resultProdotto) === 1) {
        $row = pg_fetch_assoc($resultProdotto);
        $idPortata = $row['id_portata'];

        // Collega il prodotto al ristorante in menu_ristorante
        $queryMenu = "INSERT INTO menu_ristorante (id_ristorante, id_portata) VALUES ($1, $2)";
        $resultMenu = pg_query_params($conn, $queryMenu, [$idRistorante, $idPortata]);

        if ($resultMenu) {
            echo json_encode(['success' => true, 'message' => 'Prodotto aggiunto al menu con successo']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Prodotto inserito ma errore nel collegamento al menu']);
        }
    } else {
        $errorMessage = pg_last_error($conn);
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'inserimento del prodotto: ' . $errorMessage]);
    }

    pg_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Richiesta non valida']);
exit;
?>