<?php
include 'fetch_news.php'; // or use correct relative path

$news = fetchJobNews();

echo "<h2>Job News API Test</h2>";

if (empty($news)) {
    echo "<p style='color: red;'>❌ No news fetched or error in API.</p>";
} else {
    echo "<ul>";
    foreach ($news as $article) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($article['title']) . "</strong><br>";
        echo "<a href='" . $article['url'] . "' target='_blank'>Read more</a>";
        echo "</li><br>";
    }
    echo "</ul>";
}
?>
