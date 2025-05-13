<?php
include_once 'includes/simple_html_dom.php';

function fetchBangladeshiJobNews() {
    // Correct URL to real job news
    $html = file_get_html('https://www.thedailystar.net/youth/education');

    if (!$html) {
        return [];
    }

    $articles = [];

    foreach ($html->find('.card--listing .card') as $card) {
        $titleTag = $card->find('.card__title a', 0);
        $title = $titleTag ? trim($titleTag->plaintext) : '';
        $link = $titleTag ? 'https://www.thedailystar.net' . $titleTag->href : '';
        $desc = $card->find('.card__summary', 0)->plaintext ?? '';
        $image = $card->find('img', 0)->src ?? 'assets/img/default.jpg';
        $date = $card->find('.card__date', 0)->plaintext ?? '';

        if ($title && $link) {
            $articles[] = [
                'title' => $title,
                'url' => $link,
                'description' => trim($desc),
                'urlToImage' => $image,
                'publishedAt' => trim($date),
            ];
        }
    }

    return $articles;
}
?>
