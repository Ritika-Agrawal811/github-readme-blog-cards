<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Card.php';
require_once __DIR__ . './../src/error/ErrorCard.php';
require_once __DIR__ . './../src/error/URLValidator.php';

// Ignore favicon requests from browsers
if ($_SERVER['REQUEST_URI'] === '/favicon.ico') {
    http_response_code(204); // No content
    exit();
}

// Set default blog URL if 'url' param is missing or empty
$blogURL = isset($_GET['url']) && $_GET['url'] !== '' ? $_GET['url'] : null;

// Validate URL
$validator = new URLValidator($blogURL);

if (!$validator->validate()) {
    $errorCard = new ErrorCard($validator->getError(), 400);
    $errorCard->render();
    exit();
}

// get layout param
$layout = $_GET['layout'] ?? 'vertical';
$layout = match (strtolower($layout)) {
    'h', 'horizontal' => 'horizontal',
    'v', 'vertical' => 'vertical',
    default => 'vertical',
};

// get theme param
$theme = $_GET['theme'] ?? 'default';

$card = new Card($blogURL, $layout, $theme);

// Create ETag based on URL + theme (same URL = same ETag)
$etag = md5($blogURL . $theme . $layout);

// Set headers
header('Content-Type: image/svg+xml');
// header('Cache-Control: public, max-age=432000'); // 5 days
// header('Etag: "' . $etag . '"');
// header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime('-1 hour')) . ' GMT');

// Handle If-None-Match for 304 responses
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
    http_response_code(304);
    exit();
}

echo $card->render();
