<?php
function fetchNewsFromGNews() {
    $apiKey = 'YOUR_GNEWS_API_KEY';  // Get from https://gnews.io/
    $url = "https://gnews.io/api/v4/search?q=job&lang=en&country=bd&max=5&apikey=$apiKey";

    $response = @file_get_contents($url);
    $data = json_decode($response, true);
    return $data['articles'] ?? [];
}

function fetchNewsFromNewsData() {
    $apiKey = 'YOUR_NEWSDATA_API_KEY'; // Get from https://newsdata.io/
    $url = "https://newsdata.io/api/1/news?apikey=$apiKey&country=bd&language=en&q=job";

    $response = @file_get_contents($url);
    $data = json_decode($response, true);
    if (!isset($data['results'])) return [];

    return array_map(function ($item) {
        return [
            'title' => $item['title'],
            'url' => $item['link'],
            'description' => $item['description'],
            'urlToImage' => $item['image_url'] ?? 'assets/img/default.jpg',
            'publishedAt' => $item['pubDate'] ?? '',
        ];
    }, $data['results']);
}

function fetchCombinedNews() {
    $gnews = fetchNewsFromGNews();
    $newsdata = fetchNewsFromNewsData();

    // Merge all and return
    return array_merge($gnews, $newsdata);
}
?>
