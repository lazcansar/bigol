<?php

$url = 'https://onemillionpredictions.com/today-football-predictions/correct-score/';

// cURL ile HTML içeriği al
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    die('Sayfa alınamadı.');
}

// HTML'i parse et
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

// Tablo satırlarını seç
$rows = $xpath->query('//tr[starts-with(@class, "ninja_table_row_")]');

echo '<div class="relative overflow-x-auto">
<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="matchTable">
    <thead class="text-xs text-gray-700 uppercase bg-gray-400 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-6 py-3 text-center">Başlama Zamanı</th>
            <th scope="col" class="px-6 py-3 text-center">Ev Sahibi</th>
            <th scope="col" class="px-6 py-3 text-center">Skor</th>
            <th scope="col" class="px-6 py-3 text-center">Deplasman</th>
        </tr>
    </thead>
    <tbody>';

foreach ($rows as $row) {
    $cols = $xpath->query('.//td', $row);

    // Tarih/Saat
    $datetime = trim($cols[0]->textContent);

    // Takımları <br> etiketiyle ayır
    $teamCell = $cols[1];
    $teams = [];
    foreach ($teamCell->childNodes as $node) {
        if ($node->nodeType === XML_TEXT_NODE) {
            $teams[] = trim($node->textContent);
        }
    }

    $home = $teams[0] ?? '';
    $away = $teams[1] ?? '';

    // Skor ve oran
    $score = trim($cols[2]->textContent);
    $odd = trim($cols[3]->textContent);

    echo "<tr>
        <td>{$datetime}</td>
        <td>{$home}</td>
        <td>{$away}</td>
        <td>{$score}</td>
        <td>{$odd}</td>
    </tr>";
}

echo '    </tbody>
</table>
</div>';
