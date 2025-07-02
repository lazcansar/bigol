<?php
require_once 'header.php';

$url = 'https://www.predicd.com/en/football/match-schedule/'; // Hedef URL

// cURL oturumu başlat
$ch = curl_init();

// cURL seçeneklerini ayarla
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Çıktıyı string olarak döndür
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Yönlendirmeleri takip et (yönlendirme varsa)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL sertifikası doğrulamayı kapat (Güvenli değil! Sadece test veya bilinen siteler için kullanın.)
curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 saniye timeout süresi (uzun sürebilir)

// Gerçek bir tarayıcı User-Agent'ı ekle - bu çok önemli!
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept-Language: en-US,en;q=0.9', // Dil tercihi
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
]);


// İsteği çalıştır ve HTML içeriğini al
$html = curl_exec($ch);

// cURL hatalarını kontrol et
if (curl_errno($ch)) {
    echo "cURL Hatası: " . curl_error($ch) . "\n";
    die("Web sitesinden veri çekilemedi. Lütfen bağlantınızı, URL'yi veya sitenin durumunu kontrol edin.");
}

// cURL oturumunu kapat
curl_close($ch);

// --- HTML içeriği çekildi, şimdi ayrıştırma kısmına geçiyoruz ---

$dom = new DOMDocument();

// HTML'deki hataları görmezden gelmek için
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors(); // Önceki hataları temizle

$xpath = new DOMXPath($dom);

$all_matches_data = [];

// Tüm maç satırlarını seçelim. Bu satırlar 'tr-body' sınıfına sahip olanlar.
// Bu, accordion içeriği olan satırları atlayacaktır.
$rows = $xpath->query('//tbody/tr[contains(@class, "tr-body") and contains(@class, "accordion-toggle")]');

if ($rows->length > 0) {
    echo "<h2>Maç Tahminleri (predicd.com):</h2>\n";
    echo "<pre>";

    // For döngüsü ile her bir maç satırını işleyin
    for ($i = 0; $i < $rows->length; $i++) {
        $row = $rows->item($i);
        $match_data = [];

        // Maç ID'si (data-bs-target özelliğinden çekebiliriz)
        $data_bs_target = $row->getAttribute('data-bs-target');
        if (!empty($data_bs_target) && strpos($data_bs_target, '#demo_') === 0) {
            $match_data['match_id'] = str_replace('#demo_', '', $data_bs_target);
        } else {
            $match_data['match_id'] = 'N/A';
        }

        // Maç saati: 'matches-time-column' sınıfına sahip td içindeki span
        $matchTimeNode = $xpath->query('.//td[contains(@class, "matches-time-column")]/span[starts-with(@id, "matchTime-")]', $row);
        $match_data['match_time'] = $matchTimeNode->length > 0 ? trim($matchTimeNode->item(0)->nodeValue) : 'Yok';

        // Takım adları: 'inner-td' sınıfına sahip td içindeki 'teamname' ID'li divler
        $teamNamesNodes = $xpath->query('.//td[contains(@class, "inner-td")]//div[@id="teamname"]', $row);
        if ($teamNamesNodes->length >= 2) {
            $match_data['home_team'] = trim($teamNamesNodes->item(0)->nodeValue);
            $match_data['away_team'] = trim($teamNamesNodes->item(1)->nodeValue);
        } else {
            $match_data['home_team'] = 'Yok';
            $match_data['away_team'] = 'Yok';
        }

        // Tahmini skor: 'predResult_' ile başlayan ID'ye sahip span'den
        $predResultNode = $xpath->query('.//span[starts-with(@id, "predResult_")]', $row);
        if ($predResultNode->length > 0) {
            // Skoru içeren 'of-meta' span'lerini bulalım ve birleştirelim
            $predictedScoreParts = $xpath->query('.//span[@class="of-meta"]', $predResultNode->item(0));
            $score = [];
            foreach ($predictedScoreParts as $part) {
                $score[] = trim($part->nodeValue);
            }
            $match_data['predicted_score'] = implode(' - ', $score);

            // Eğer özel karakterler (, ) hala görünüyorsa veya boşsa, daha geniş bir temizlik yap
            if (empty(trim($match_data['predicted_score'])) || preg_match('/[^0-9-:]/', $match_data['predicted_score'])) {
                $raw_score_text = trim($predResultNode->item(0)->nodeValue);
                // Sadece sayıları, tireyi ve iki noktayı bırak
                $cleaned_score = preg_replace('/[^0-9-:]/', '', $raw_score_text);
                $match_data['predicted_score'] = !empty($cleaned_score) ? $cleaned_score : 'Yok';
            }
        } else {
            $match_data['predicted_score'] = 'Yok';
        }

        // Gerçek maç sonucu (şu an HTML'de boş, gelecekte dolabilir)
        $actualResultNode = $xpath->query('.//div[starts-with(@id, "matchResult-")]', $row);
        if ($actualResultNode->length > 0 && !empty(trim($actualResultNode->item(0)->nodeValue))) {
            $match_data['actual_result'] = trim($actualResultNode->item(0)->nodeValue);
        } else {
            $match_data['actual_result'] = 'Henüz yok / Bilinmiyor';
        }

        $all_matches_data[] = $match_data;
    }
// Skor Tablosu
    echo '
   
<div class="relative overflow-x-auto">
<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-400 dark:bg-gray-700 dark:text-gray-400">
    <tr>
        <th scope="col" class="px-6 py-3">
            Başlama Zamanı
        </th>
        <th scope="col" class="px-6 py-3">
            Ev Sahibi
        </th>
        <th scope="col" class="px-6 py-3">
            Skor
        </th>
        <th scope="col" class="px-6 py-3">
            Deplasman
        </th>
    </tr>
    </thead>
    <tbody>
    ';

    // Toplanan verileri ekrana yazdır
    foreach ($all_matches_data as $match) {
        echo '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">

        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
            '.$match['match_time'].'
        </td>
        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
            '.$match['home_team'].'
        </td>
        <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap dark:text-white text-center">
            '.$match['actual_result'].'
        </td>
        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
            '.$match['away_team'].'
        </td>
    </tr>';



    }
    echo "</pre>";

    echo '
    </tbody>
</table>
</div>
';

} else {
    echo "Web sayfasında belirtilen kriterlere uygun maç kaydı bulunamadı. Lütfen HTML yapısını veya XPath sorgusunu tekrar kontrol edin.\n";
}




require_once 'footer.php';
?>