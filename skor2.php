<?php

date_default_timezone_set('UTC'); // Zaman dilimini ayarla (gerekirse güncelle)

$url = 'https://www.bettingclosed.com/predictions/date-matches/today/bet-type/correct-scores';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    die('Sayfa alınamadı.');
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

// Bugünün ve yarının tarihi
$today = date('m/d');
$tomorrow = date('m/d', strtotime('+1 day'));

$rows = $xpath->query('//tr[contains(@class, "rowincontriOdd") or contains(@class, "rowincontriEven")]');

// HTML başlangıç
echo <<<HTML
<div class="mb-4 flex gap-4">
    <button onclick="filterRows('today')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Bugün</button>
    <button onclick="filterRows('tomorrow')" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Yarın</button>
    <button onclick="filterRows('later')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Sonraki Günler</button>
</div>

<div class="relative overflow-x-auto">
<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="matchTable">
    <thead class="text-xs text-gray-700 uppercase bg-gray-400 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-6 py-3 text-center">Başlama Zamanı</th>
            <th scope="col" class="px-6 py-3 text-center">Ev Sahibi</th>
            <th scope="col" class="px-6 py-3 text-center">Skor</th>
            <th scope="col" class="px-6 py-3 text-center">Deplasman</th>
        </tr>
    </thead>
    <tbody>
HTML;

foreach ($rows as $row) {
    $cols = $xpath->query('.//td', $row);

    $date_time_raw = trim($cols[0]->textContent); // örn: 07/02 02:30
    $date_parts = explode(' ', $date_time_raw);
    $date = $date_parts[0]; // 07/02

    // Maç günü kategorisi
    if ($date === $today) {
        $data_day = 'today';
    } elseif ($date === $tomorrow) {
        $data_day = 'tomorrow';
    } else {
        $data_day = 'later';
    }

    $teamA = trim($cols[2]->textContent);
    $score = trim($cols[4]->textContent);
    $teamB = trim($cols[5]->textContent);

    echo <<<ROW
<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 match-row" data-day="$data_day">
    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">{$date_time_raw}</td>
    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">{$teamA}</td>
    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap dark:text-white text-center">{$score}</td>
    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">{$teamB}</td>
</tr>
ROW;
}

echo <<<HTML
    </tbody>
</table>
</div>

<script>
function filterRows(day) {
    const rows = document.querySelectorAll('.match-row');
    rows.forEach(row => {
        if (row.getAttribute('data-day') === day) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
HTML;
