<?php
function fetchJobNews() {
    $apiKey = "0a1ce437e097409986adc852aeda4234";

    $params = http_build_query([
        'q' => 'job',
        'language' => 'en',
        'pageSize' => 5,
        'sortBy' => 'publishedAt',
        'apiKey' => $apiKey
    ]);

    $url = "https://newsapi.org/v2/everything?" . $params;

    $headers = [
        "User-Agent: NextWorkX/1.0"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Curl error: " . curl_error($ch);
        curl_close($ch);
        return [];
    }

    curl_close($ch);

    $data = json_decode($response, true);
    return $data['articles'] ?? [];
}


?>
