<?php
// ajax/gemini_chat.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['message'])) {
    echo json_encode(['reply' => 'Invalid request.']);
    exit;
}

$user_message = trim($_POST['message']);
if ($user_message === '') {
    echo json_encode(['reply' => 'Please enter a message.']);
    exit;
}

// Gemini API endpoint and key
$api_key = 'YOUR_GEMINI_API_KEY'; // <-- Replace with your Gemini API key
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $api_key;

// Prepare request payload
$payload = [
    'contents' => [
        [ 'parts' => [ [ 'text' => $user_message ] ] ]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($payload),
        'timeout' => 10
    ]
];
$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo json_encode(['reply' => 'Error contacting Gemini API.']);
    exit;
}

$data = json_decode($response, true);
$reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response.';

echo json_encode(['reply' => $reply]);
