<?php
// Definisci FROM_INCLUDE per evitare che il file originale restituisca JSON
define('FROM_INCLUDE', true);

// Includi il file con la logica di recupero dati
$managerList = require('impostazioni.php'); // Assumo che il file originale si chiami manager_data.php

// Se non ci sono dati, mostra un messaggio
if (empty($managerList)) {
    echo "<p>Nessun manager trovato.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Manager</title>

</head>
<body>

            <?php foreach ($managerList as $manager): ?>
                <tr>
                    <td><?php echo htmlspecialchars($manager['username']); ?></td>
                    <td><?php echo htmlspecialchars($manager['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($manager['email']); ?></td>
                    <td><?php echo htmlspecialchars($manager['ruolo']); ?></td>
                    <td><?php echo htmlspecialchars($manager['id_ristorante']); ?></td>
                    <td><?php echo htmlspecialchars($manager['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($manager['hired']); ?></td>
                    <td>
                        <?php if (!empty($manager['url_img'])): ?>
                            <img src="<?php echo htmlspecialchars($manager['url_img']); ?>" alt="Immagine Manager">
                        <?php else: ?>
                            Nessuna immagine
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>