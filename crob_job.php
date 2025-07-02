<?php
require_once 'dbCon.php';

$thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));

// Süresi dolmuş premium üyeleri bul ve güncelle
$updateStmt = $db->prepare("UPDATE users SET ibanConfirm = 0, updated_at = NOW() WHERE ibanConfirm = 1 AND updated_at <= ?");
$updateStmt->bind_param("s", $thirtyDaysAgo);

if ($updateStmt->execute()) {
    echo "Süresi dolmuş " . $updateStmt->affected_rows . " kullanıcının IBAN onay durumu güncellendi.";
} else {
    error_log("IBAN onay durumu güncellenirken hata oluştu: " . $updateStmt->error);
    echo "Hata oluştu: " . $updateStmt->error;
}

$updateStmt->close();
$db->close();
?>