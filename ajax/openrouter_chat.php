<?php
// ajax/openrouter_chat.php
// Server-side proxy to OpenRouter Chat Completions to keep API key hidden from the client.
// NOTE: For production, store the API key in an environment variable or a secure config.

header('Content-Type: application/json');
require_once(__DIR__ . '/../includes/config.php');

// Only accept POST with JSON body
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body']);
    exit;
}

$messages = $data['messages'] ?? null;
$model = $data['model'] ?? 'deepseek/deepseek-r1:free';
if (!$messages || !is_array($messages)) {
    http_response_code(400);
    echo json_encode(['error' => 'messages array is required']);
    exit;
}

// Load API key from config
// Select API key: prefer default; if model includes 'gemini', prefer Gemini key; if default is missing, fall back to Gemini key.
$default_key = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : null;
$gemini_key = defined('OPENROUTER_GEMINI_API_KEY') ? OPENROUTER_GEMINI_API_KEY : null;

// Start with default
$OPENROUTER_API_KEY = $default_key;

// If model mentions gemini and we have a gemini-specific key, use it
if (stripos($model, 'gemini') !== false && !empty($gemini_key)) {
    $OPENROUTER_API_KEY = $gemini_key;
}

// If still empty, fall back to gemini key if present
if (empty($OPENROUTER_API_KEY) && !empty($gemini_key)) {
    $OPENROUTER_API_KEY = $gemini_key;
}

if (!$OPENROUTER_API_KEY) {
    http_response_code(500);
    echo json_encode(['error' => 'Server missing OpenRouter API key for requested model. Set OPENROUTER_API_KEY or OPENROUTER_GEMINI_API_KEY.']);
    exit;
}

// Build headers
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$referer = $scheme . '://' . $host . '/';
$siteTitle = 'NextWorkX';

// Prepare request payload
$payload = [
    'model' => $model,
    'messages' => $messages,
    // You can tune params below if desired
    'temperature' => isset($data['temperature']) ? $data['temperature'] : 0.7,
    // 'max_tokens' => 512,
];

// Retry/backoff configuration
// Retry/backoff configuration
$maxAttempts = 4;
$baseDelay = 0.8; // seconds

$attempt = 0;
$response = null;
$lastErr = '';
$lastStatus = 0;

while ($attempt < $maxAttempts) {
    $attempt++;
    $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $OPENROUTER_API_KEY,
            'HTTP-Referer: ' . $referer,
            'X-Title: ' . $siteTitle,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HEADER => true, // capture headers
    ]);

    $raw_response = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $response = '';
    $response_headers = [];
    if (is_string($raw_response)) {
        $response = substr($raw_response, $header_size);
        $header_text = substr($raw_response, 0, $header_size);
        // parse headers into array
        $lines = preg_split('/\r?\n/', trim($header_text));
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$k, $v] = explode(':', $line, 2);
                $response_headers[strtolower(trim($k))] = trim($v);
            }
        }
    }

    $lastErr = $err;
    $lastStatus = $status;

    if ($errno) {
        // network error; break and return
        break;
    }

    if ($status >= 200 && $status < 300) {
        // success
        break;
    }

    // If we got a 429, we may retry
    // Determine if we should retry: 429 or 408 (timeout)
    if (($status === 429 || $status === 408) && $attempt < $maxAttempts) {
        // Honor Retry-After header if present (seconds)
        $wait = null;
        if (!empty($response_headers['retry-after'])) {
            $ra = (int)$response_headers['retry-after'];
            if ($ra > 0) $wait = $ra;
        }
        if ($wait === null) {
            // Exponential backoff with jitter
            $sleep = $baseDelay * pow(2, $attempt - 1);
            $jitter = rand(0, 1000) / 1000 * 0.5 * $sleep; // up to 50% extra
            $wait = $sleep + $jitter;
        }
        usleep((int)($wait * 1e6));
        continue;
    }

    // For other non-2xx statuses, don't retry
    break;
}

if (!is_string($response)) {
    http_response_code(502);
    echo json_encode(['error' => 'Upstream error', 'detail' => $lastErr]);
    exit;
}

if ($lastStatus < 200 || $lastStatus >= 300) {
    $decoded = json_decode($response, true);
    if (is_array($decoded) && isset($decoded['error'])) {
        http_response_code($lastStatus ?: 500);
        echo json_encode(['error' => 'Upstream provider error', 'detail' => $decoded['error']]);
        exit;
    }
    http_response_code($lastStatus ?: 500);
    echo $response ?: json_encode(['error' => 'Upstream non-2xx response']);
    exit;
}

$json = json_decode($response, true);

// Improved extraction: OpenRouter may use choices[].message.content or choices[].message.content[] or choices[].message
$reply = '';
$extracted = false;
if (is_array($json)) {
    // Try OpenAI-like shape
    if (isset($json['choices']) && is_array($json['choices'])) {
        foreach ($json['choices'] as $choice) {
            // new style: message.content (string)
            if (isset($choice['message']['content']) && is_string($choice['message']['content'])) {
                $reply = $choice['message']['content'];
                $extracted = true;
                break;
            }
            // alternative: message.content is array of parts
            if (isset($choice['message']['content']) && is_array($choice['message']['content'])) {
                // join parts
                $parts = [];
                foreach ($choice['message']['content'] as $c) {
                    if (is_string($c)) $parts[] = $c;
                    elseif (is_array($c) && isset($c['text'])) $parts[] = $c['text'];
                }
                if ($parts) {
                    $reply = implode("\n", $parts);
                    $extracted = true;
                    break;
                }
            }
            // older: 'text' field
            if (isset($choice['text']) && is_string($choice['text'])) {
                $reply = $choice['text'];
                $extracted = true;
                break;
            }
        }
    }
}

// As fallback, try to read top-level 'response' or 'output'
if (!$extracted) {
    if (isset($json['response']) && is_string($json['response'])) {
        $reply = $json['response'];
        $extracted = true;
    } elseif (isset($json['output']) && is_string($json['output'])) {
        $reply = $json['output'];
        $extracted = true;
    }
}

// Strip chain-of-thought tags
$reply = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $reply);
$reply = trim($reply);

$result = ['reply' => $reply, 'raw' => $json];

// If extraction failed and client asked for debug, include some diagnostics
$body = json_decode($raw, true);
if (empty($reply) && !empty($body['debug']) && $body['debug'] === true) {
    $result['debug'] = [
        'status' => $status,
        'curl_errno' => $errno,
        'curl_error' => $err,
        'available_keys' => array_keys($json ?? []),
    ];
}

echo json_encode($result);
