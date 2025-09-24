<?php
// ajax/gemini_chat.php
// Simple proxy to Google's Generative Language API (Gemini) to keep the API key server-side.
header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['message'])) {
    http_response_code(400);
    echo json_encode(['reply' => 'Invalid request.']);
    exit;
}

$user_message = trim($_POST['message'] ?? '');
if ($user_message === '') {
    http_response_code(400);
    echo json_encode(['reply' => 'Please enter a message.']);
    exit;
}

// Load Gemini API key from environment (.env is loaded by includes/config.php)
$api_key = getenv('GEMINI_API_KEY');
if (!$api_key) {
    http_response_code(500);
    echo json_encode(['reply' => 'Server missing GEMINI_API_KEY. Add it to .env or your environment.']);
    exit;
}

// Gemini API endpoint (v1beta) and model
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

// Prepare request payload
$payload = [
    'contents' => [
        [
            'parts' => [ [ 'text' => $user_message ] ]
        ]
    ]
];

// Use cURL to set X-goog-api-key header and JSON body
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-goog-api-key: ' . $api_key,
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$errno = curl_errno($ch);
$err = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($errno) {
    http_response_code(502);
    echo json_encode(['reply' => 'Error contacting Gemini API: ' . $err]);
    exit;
}

if ($status < 200 || $status >= 300) {
    http_response_code($status ?: 500);
    echo json_encode(['reply' => 'Gemini API returned status ' . $status, 'raw' => $response]);
    exit;
}

$data = json_decode($response, true);

// Extract reply text robustly
$reply = '';
if (is_array($data) && isset($data['candidates'][0]['content']['parts'])) {
    $parts = $data['candidates'][0]['content']['parts'];
    if (is_array($parts)) {
        $texts = [];
        foreach ($parts as $p) {
            if (is_array($p) && isset($p['text']) && is_string($p['text'])) {
                $texts[] = $p['text'];
            }
        }
        if ($texts) {
            $reply = trim(implode("\n", $texts));
        }
    }
}

if ($reply === '') {
    $reply = 'No response.';
}

echo json_encode(['reply' => $reply]);
