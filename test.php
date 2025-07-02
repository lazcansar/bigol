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

// --- FİLTRE BUTONLARI ---
echo '
<div class="mb-4 flex space-x-2">
    <button id="btnToday" class="px-4 py-2 text-white rounded-lg shadow transition-colors duration-200">Bugün</button>
    <button id="btnTomorrow" class="px-4 py-2 text-white rounded-lg shadow transition-colors duration-200">Yarın</button>
    <button id="btnNextDay" class="px-4 py-2 text-white rounded-lg shadow transition-colors duration-200">Sonraki Gün</button>
    <button id="btnAll" class="px-4 py-2 text-white rounded-lg shadow transition-colors duration-200">Tümünü Göster</button>
</div>';

// --- TABLO BAŞLANGICI ---
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

$i = 0;
foreach ($rows as $row) {
    $i++;
    if ($i <= 6) {
        continue;
    }
    $cols = $xpath->query('.//td', $row);
    if ($cols->length < 4) {
        continue;
    }

    // =================================================================
    // *** BURASI KRİTİK DÜZELTME ALANI ***
    // =================================================================
    // Tarih/Saat bilgisini al ("2025-07-03 22:00" formatında gelir)
    $full_datetime_str = trim($cols[0]->textContent);

    // Boşluk karakterine göre metni böl ve sadece ilk kısmı (tarihi) al.
    $date_only = explode(' ', $full_datetime_str)[0];
    // =================================================================

    $teamCell = $cols[1];
    $teams = [];
    foreach ($teamCell->childNodes as $node) {
        if ($node->nodeType === XML_TEXT_NODE) {
            $teams[] = trim($node->textContent);
        }
    }
    $home = $teams[0] ?? '';
    $away = $teams[1] ?? '';

    $score = trim($cols[2]->textContent);

    // Satıra `data-date` özelliği olarak SADECE TARİH kısmını ekle
    echo '<tr data-date="'.$date_only.'">
        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">'.$full_datetime_str.'</td>
        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">'.$home.'</td>
        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">'.$score.'</td>
        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">'.$away.'</td>
    </tr>';
}
echo '
    </tbody>
</table>
</div>';


// --- JAVASCRIPT KODU (DEĞİŞİKLİK YOK, ARTIK DOĞRU ÇALIŞACAK) ---
echo '
<script>
document.addEventListener("DOMContentLoaded", function() {

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return year + "-" + month + "-" + day;
    }

    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);
    const nextDay = new Date();
    nextDay.setDate(today.getDate() + 2);

    const todayStr = formatDate(today);
    const tomorrowStr = formatDate(tomorrow);
    const nextDayStr = formatDate(nextDay);
    
    const buttons = {
        today: document.getElementById("btnToday"),
        tomorrow: document.getElementById("btnTomorrow"),
        nextDay: document.getElementById("btnNextDay"),
        all: document.getElementById("btnAll")
    };
    
    const rows = document.getElementById("matchTable").getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    function updateActiveButton(activeKey) {
        const activeClass = "bg-blue-600";
        const inactiveClass = "bg-gray-500";
        
        for (const key in buttons) {
            const button = buttons[key];
            if (key === activeKey) {
                button.classList.remove(inactiveClass);
                button.classList.add(activeClass);
            } else {
                button.classList.remove(activeClass);
                button.classList.add(inactiveClass);
            }
        }
    }

    function filterMatches(filterDate, activeKey) {
        let matchFound = false;
        for (const row of rows) {
            if (filterDate === "all" || row.dataset.date === filterDate) {
                row.style.display = "";
                matchFound = true;
            } else {
                row.style.display = "none";
            }
        }
        updateActiveButton(activeKey);
    }
    
    buttons.today.addEventListener("click", () => filterMatches(todayStr, "today"));
    buttons.tomorrow.addEventListener("click", () => filterMatches(tomorrowStr, "tomorrow"));
    buttons.nextDay.addEventListener("click", () => filterMatches(nextDayStr, "nextDay"));
    buttons.all.addEventListener("click", () => filterMatches("all", "all"));

    filterMatches(todayStr, "today");
});
</script>';

?>