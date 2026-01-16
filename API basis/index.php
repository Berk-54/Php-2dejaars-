<?php
declare(strict_types=1);


final class Config
{
    public const LAT = 52.37;
    public const LON = 4.89;
    public const BASE_URL = 'https://api.open-meteo.com/v1/forecast';
    public const TIMEOUT_SECONDS = 6;
}

final class MeteoException extends RuntimeException {}

final class HttpResponse
{
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

final class MeteoClient
{
    public function getCurrentWeather(float $lat, float $lon): array
    {
        $url = $this->buildUrl($lat, $lon);

        $raw = $this->curlGet($url, Config::TIMEOUT_SECONDS);
        $data = json_decode($raw, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            throw new MeteoException('Response is geen geldige JSON: ' . json_last_error_msg());
        }

        if (!isset($data['current_weather']) || !is_array($data['current_weather'])) {
            throw new MeteoException("Geen 'current_weather' in de response.");
        }

        $cw = $data['current_weather'];

        if (!isset($cw['temperature'], $cw['windspeed'])) {
            throw new MeteoException("Ontbrekende velden in 'current_weather'.");
        }

        return [
            'temperature' => $cw['temperature'],
            'windspeed' => $cw['windspeed'],
            'winddirection' => $cw['winddirection'] ?? null,
            'time' => $cw['time'] ?? null,
            'source_url' => $url,
        ];
    }

    private function buildUrl(float $lat, float $lon): string
    {
        $query = http_build_query([
            'latitude' => $lat,
            'longitude' => $lon,
            'current_weather' => 'true',
        ]);

        return Config::BASE_URL . '?' . $query;
    }

    private function curlGet(string $url, int $timeoutSeconds): string
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new MeteoException('cURL kon niet initialiseren.');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $timeoutSeconds,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);

        $raw = curl_exec($ch);
        $errNo = curl_errno($ch);
        $err = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

      
        if ($errNo !== 0) {
            throw new MeteoException('Netwerkfout (cURL): ' . $err);
        }
        if ($raw === false || $raw === '') {
            throw new MeteoException('Lege response van de API.');
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            throw new MeteoException('API gaf HTTP status: ' . $httpCode);
        }

        return (string)$raw;
    }
}


if (isset($_GET['ajax'])) {
    try {
        $client = new MeteoClient();
        $weather = $client->getCurrentWeather(Config::LAT, Config::LON);

        HttpResponse::json([
            'ok' => true,
            'city' => 'Amsterdam',
            'current_weather' => $weather,
        ]);
    } catch (Throwable $e) {
        HttpResponse::json([
            'ok' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Open-Meteo – Amsterdam (OOP)</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#0b1220;color:#e8eefc}
    .wrap{max-width:820px;margin:40px auto;padding:0 16px}
    .card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:20px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    h1{margin:0 0 10px;font-size:22px}
    .muted{color:rgba(232,238,252,.72)}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px}
    .tile{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.10);border-radius:14px;padding:14px;min-height:84px}
    .label{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:rgba(232,238,252,.7)}
    .value{font-size:28px;margin-top:6px}
    .error{background:rgba(255,60,60,.12);border:1px solid rgba(255,60,60,.35);padding:12px;border-radius:12px;margin-top:12px}
    .loading{display:flex;align-items:center;gap:10px;margin-top:12px;padding:10px 12px;border-radius:12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12)}
    .spinner{width:18px;height:18px;border-radius:999px;border:2px solid rgba(232,238,252,.35);border-top-color:rgba(232,238,252,.95);animation:spin .9s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
    .footer{margin-top:14px;font-size:12px;color:rgba(232,238,252,.6)}
    button{margin-top:12px;border-radius:12px;padding:10px 12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);color:#e8eefc;cursor:pointer}
    button:hover{background:rgba(255,255,255,.12)}
    a{color:#b9d2ff}
    @media (max-width:560px){.row{grid-template-columns:1fr}}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Weer in Amsterdam (Open-Meteo, OOP)</h1>
      <div class="muted">GET-request → JSON parse → temperatuur & wind tonen.</div>

      <div id="status" class="loading" aria-live="polite">
        <div class="spinner" aria-hidden="true"></div>
        <div>Bezig met ophalen…</div>
      </div>

      <div id="error" class="error" style="display:none;"></div>

      <div class="row" id="result" style="display:none;">
        <div class="tile">
          <div class="label">Temperatuur</div>
          <div class="value" id="temp">—</div>
          <div class="muted" id="time"></div>
        </div>
        <div class="tile">
          <div class="label">Wind</div>
          <div class="value" id="wind">—</div>
          <div class="muted" id="winddir"></div>
        </div>
      </div>

      <button id="reload" type="button">Ververs</button>

      <div class="footer" id="meta"></div>
    </div>
  </div>

  <script>
    const statusEl = document.getElementById('status');
    const errorEl  = document.getElementById('error');
    const resultEl = document.getElementById('result');

    const tempEl = document.getElementById('temp');
    const windEl = document.getElementById('wind');
    const winddirEl = document.getElementById('winddir');
    const timeEl = document.getElementById('time');
    const metaEl = document.getElementById('meta');

    function setLoading(isLoading) {
      statusEl.style.display = isLoading ? 'flex' : 'none';
    }

    function showError(msg) {
      errorEl.textContent = 'Fout: ' + msg;
      errorEl.style.display = 'block';
      resultEl.style.display = 'none';
    }

    function showResult(data) {
      errorEl.style.display = 'none';
      resultEl.style.display = 'grid';

      const cw = data.current_weather;
      tempEl.textContent = cw.temperature + ' °C';
      windEl.textContent = cw.windspeed + ' km/h';
      timeEl.textContent = cw.time ? ('API-tijd: ' + cw.time) : '';
      winddirEl.textContent = (cw.winddirection ?? null) !== null ? ('Richting: ' + cw.winddirection + '°') : '';

      metaEl.innerHTML = 'Bron: <a target="_blank" rel="noopener" href="' + cw.source_url + '">Open-Meteo request</a>';
    }

    async function loadWeather() {
      setLoading(true);
      errorEl.style.display = 'none';

      try {
        const res = await fetch('?ajax=1', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        if (!res.ok || !data.ok) throw new Error(data.error || ('HTTP ' + res.status));
        showResult(data);
      } catch (e) {
        showError(e.message);
      } finally {
        setLoading(false);
      }
    }

    document.getElementById('reload').addEventListener('click', loadWeather);
    loadWeather();
  </script>
</body>
</html>
