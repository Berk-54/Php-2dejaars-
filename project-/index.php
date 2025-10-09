<?php
declare(strict_types=1);

require __DIR__ . '/src/Product.php';
require __DIR__ . '/src/Music.php';
require __DIR__ . '/src/Movie.php';
require __DIR__ . '/src/Game.php';
require __DIR__ . '/src/ProductList.php';

use Webshop\{ProductList, Music, Movie, Game};

$list = new ProductList();

/* --- Data zoals in je voorbeeld --- */
// Music
$music1 = new Music('Test1', 5.00, 21, 1.86, 'Muziek dvd', 'Artiest 1');
$music1->addNumber('number 1');
$music1->addNumber('number 2');

$music2 = new Music('Test2', 10.00, 21, 2.50, 'Muziek dvd', 'Artiest 2');
$music2->addNumber('number 3');
$music2->addNumber('number 4');

// Movies
$movie1 = new Movie('Starwars 1', 10.00, 21, 2.50, 'Film', 'DVD');
$movie2 = new Movie('Starwars 2', 15.00, 21, 3.00, 'Film', 'Blueray');

// Games
$game1 = new Game('Call of Duty 1', 5.00, 21, 1.50, 'FPS', 'FPS');
$game1->addRequirement('8gb geheugen');
$game1->addRequirement('970 GTX');

$game2 = new Game('Call of Duty 2', 9.00, 21, 1.49, 'FPS', 'FPS');
$game2->addRequirement('16gb geheugen');
$game2->addRequirement('2070 RTX');

// Voeg toe aan lijst
foreach ([$music1,$music2,$movie1,$movie2,$game1,$game2] as $p) {
    $list->addProduct($p);
}

/* --- HTML render helper --- */
function bullets(array $items): string {
    if (!$items) return '';
    $html = "<ul>\n";
    foreach ($items as $it) {
        if (is_array($it)) {
            $html .= "<li>" . bullets($it) . "</li>\n";
        } else {
            $html .= "<li>" . htmlspecialchars((string)$it, ENT_QUOTES, 'UTF-8') . "</li>\n";
        }
    }
    $html .= "</ul>";
    return $html;
}
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Producten</title>
    <style>
        table { border-collapse: collapse; }
        th, td { border:1px solid #333; padding:8px 10px; vertical-align: top; }
        th { background:#f5f5f5; }
    </style>
</head>
<body>
<h2>Producten</h2>
<table>
    <thead>
        <tr>
            <th>Category</th>
            <th>Naam product</th>
            <th>Verkoopprijs</th>
            <th>Info</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($list->getProducts() as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product->getCategory(), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($product->getName(), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= number_format($product->getSalesPrice(), 2, '.', '') ?></td>
            <td>
                <?php
                $info = $product->getInfo();

                // Product-specifieke render
                if ($product instanceof Music) {
                    echo bullets([
                        'Artist ' . $info['artist'],
                        'Extra info' => $info['numbers']
                    ]);
                } elseif ($product instanceof Movie) {
                    echo bullets([$info['quality']]);
                } elseif ($product instanceof Game) {
                    echo bullets([
                        $info['genre'],
                        'Extra info' => $info['requirements']
                    ]);
                } else {
                    echo bullets($info);
                }
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
