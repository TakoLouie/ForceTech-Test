<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/config.php';
require_once dirname(__DIR__) . '/src/shortener.php';

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

$route = $_GET['route'] ?? '';

if ($route === 'api') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        jsonResponse(['error' => 'Method not allowed.'], 405);
    }

    $payload = json_decode((string) file_get_contents('php://input'), true);
    $url = is_array($payload) ? trim((string) ($payload['url'] ?? '')) : '';
    if ($url === '') {
        jsonResponse(['error' => 'Please enter a URL.'], 422);
    }
    if (!isValidUrl($url)) {
        jsonResponse(['error' => 'Enter a valid http or https URL.'], 422);
    }

    try {
        $code = createShortCode($url);
        $baseUrl = rtrim(env('APP_URL', 'http://localhost:8000'), '/');
        jsonResponse(['code' => $code, 'shortUrl' => $baseUrl . '/' . $code], 201);
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
        jsonResponse(['error' => 'Could not save the URL. Check the database configuration.'], 500);
    }
}

if ($route === 'redirect') {
    $code = (string) ($_GET['code'] ?? '');
    try {
        $url = findOriginalUrl($code);
    } catch (Throwable $exception) {
        http_response_code(500);
        exit('Database connection failed.');
    }

    if ($url === null) {
        http_response_code(404);
        exit('Short URL not found.');
    }

    header('Location: ' . $url, true, 302);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Short URL Generator</title>
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
  <main class="card">
    <p class="eyebrow">SIMPLE, SHAREABLE LINKS</p>
    <h1>Shorten a long link.</h1>
    <p class="intro">Paste any valid HTTP or HTTPS URL and get a compact link in seconds.</p>
    <form id="shorten-form" novalidate>
      <label for="url">Long URL</label>
      <div class="input-row">
        <input id="url" name="url" type="url" inputmode="url" placeholder="https://example.com/a-very-long-link" required autofocus>
        <button type="submit">Shorten</button>
      </div>
    </form>
    <p id="message" class="message" role="status" aria-live="polite"></p>
    <section id="result" class="result" hidden>
      <span>Your short link</span>
      <div class="result-row"><a id="short-url" target="_blank" rel="noopener"></a><button id="copy" type="button">Copy</button></div>
    </section>
  </main>
  <script src="/assets/app.js"></script>
</body>
</html>
