<?php
require_once 'Game.php';
require_once 'Dice.php';
session_start();

if (!isset($_SESSION['game'])) {
    $_SESSION['game'] = new Game();
}

$game = $_SESSION['game'];


// Formulier afhandeling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['throw'])) {
        $game->play();
    } elseif (isset($_POST['reset'])) {
        $game->reset();
    }

    // Game weer terug in de sessie stoppen
    $_SESSION['game'] = $game;
}

// Data voor de view
$currentValues = $game->getDiceValues();
$history = $game->getHistory();
$color = $game->getDiceColor();
$specialMessage = $game->getSpecialMessage();
$isFinished = $game->isFinished();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Dobbelspel - OOP PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        h1, h2 {
            margin-bottom: 5px;
        }
        .dice-container {
            display: flex;
            flex-direction: row;
            margin: 10px 0;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            background-color: #f0f0f0;
            border-left: 4px solid #333;
        }
        table {
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 5px 10px;
            text-align: center;
        }
        .finished {
            color: darkred;
            font-weight: bold;
        }
        .total-score {
            margin-top: 10px;
            font-weight: bold;
        }
        .buttons {
            margin-top: 15px;
        }
        button {
            padding: 8px 16px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<h1>Dobbelspel (5 dobbelstenen, max. 3 worpen)</h1>

<div class="message">
    <strong>Uitleg:</strong><br>
    - Klik op <em>Gooien</em> om alle 5 dobbelstenen te werpen.<br>
    - Je mag maximaal <?= htmlspecialchars($game->getMaxThrows()) ?> keer gooien.<br>
    - <strong>Extra:</strong> Als alle dobbelstenen gelijk zijn, krijg je een bonus van 50 punten en een speciaal bericht.  
    Alle dobbelstenen worden bovendien lichtgroen weergegeven.
</div>

<h2>Huidige worp (worp <?= htmlspecialchars($game->getThrowCount()) ?>)</h2>

<div class="dice-container">
    <?php foreach ($currentValues as $value): ?>
        <?= $game->generateSvgForValue($value, $color); ?>
    <?php endforeach; ?>
</div>

<?php if ($specialMessage): ?>
    <div class="message" style="background-color:#d4ffd4; border-left-color:green;">
        <?= $specialMessage; ?>
    </div>
<?php endif; ?>

<?php if ($isFinished): ?>
    <p class="finished">Het spel is afgelopen. Je hebt alle <?= htmlspecialchars($game->getMaxThrows()) ?> worpen gebruikt.</p>
<?php endif; ?>

<form method="post" class="buttons">
    <button type="submit" name="throw" <?php if ($isFinished) echo 'disabled'; ?>>
        Gooien
    </button>
    <button type="submit" name="reset">
        Nieuw spel
    </button>
</form>

<h2>Scorebord (EXTRA functionaliteit)</h2>

<?php if (count($history) === 0): ?>
    <p>Nog geen worpen gedaan.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Worp</th>
            <th>Dobbelstenen</th>
            <th>Som ogen</th>
            <th>Score (incl. bonus)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($history as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['throw']); ?></td>
                <td>
                    <?php foreach ($row['values'] as $val): ?>
                        <?= htmlspecialchars($val); ?>&nbsp;
                    <?php endforeach; ?>
                </td>
                <td><?= htmlspecialchars($row['sum']); ?></td>
                <td><?= htmlspecialchars($row['score']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p class="total-score">
        Totaalscore (alle worpen samen, incl. eventuele bonussen): 
        <?= htmlspecialchars($game->getTotalScore()); ?>
    </p>
<?php endif; ?>

</body>
</html>
