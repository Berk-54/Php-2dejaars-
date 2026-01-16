<?php
// index.php - Starter voor GET-aanvraag
// We gebruiken de Chuck Norris Jokes API om een willekeurige grap op te halen
// We maken een GET-verzoek naar de API en verwerken de JSON-response
// We gebruiken cURL om het verzoek te doen en de response te verwerken
// Curl staat voor Client URL en is een bibliotheek om HTTP-verzoeken te maken

$endpoint = 'https://api.chucknorris.io/jokes/random'; // URL van de API die een willekeurige Chuck Norris grap retourneert
$ch = curl_init($endpoint); // Initialiseer cURL-sessie met de opgegeven endpoint
curl_setopt_array($ch, [ // Stel meerdere cURL-opties tegelijk in
    CURLOPT_RETURNTRANSFER => true, // Zorgt dat curl_exec() de response als string retourneert i.p.v. direct uitvoert
    CURLOPT_TIMEOUT => 10, // Stel een time-out in van 10 seconden voor het verzoek
]); // Sluit de array met cURL-opties
$response = curl_exec($ch); // Voer het cURL-verzoek uit en sla de response op
$err = curl_error($ch); // Haal eventuele foutmelding op
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Haal de HTTP-statuscode op (bijv. 200, 404, 500)
curl_close($ch); // Sluit de cURL-sessie en maak resources vrij
header('Content-Type: text/html; charset=utf-8'); // Stel de Content-Type header in voor de HTML-output
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <title>API Demo</title>
  <style>body{font-family:Arial,sans-serif} .card{border:1px solid #ddd;padding:12px;border-radius:8px;max-width:600px}</style>
</head>
<body>
<h1>API Demo (GET)</h1>
<?php
  // Controleer op fouten en verwerk de response
  if ($err): ?>
  <p style="color:red">cURL-fout: <?php echo htmlspecialchars($err); ?></p>
<?php elseif ($code !== 200): ?>
  <p style="color:red">HTTP-status: <?php echo (int)$code; ?></p>
<?php else: ?>
  <?php
    // 
    $data = json_decode($response, true); ?> 
  <?php if (json_last_error() !== JSON_ERROR_NONE): ?>
    <p style="color:red">JSON-fout: <?php echo htmlspecialchars(json_last_error_msg()); ?></p>
  <?php else: ?>
    <div class="card">
      <h2>Chuck Norris grap</h2>
      <p><?php 
        // print afbeelding $date['icon_url'];
        echo '<img src="' . htmlspecialchars($data['icon_url']) . '" alt="Chuck Norris"> ';
        echo "<br>";
        // Toon de grap uit de API-response
        echo htmlspecialchars($data['value']); ?></p>
      <small>ID: <?php echo htmlspecialchars($data['id']); ?></small>
    </div>
  <?php endif; ?>
<?php endif; ?>
</body>
</html>