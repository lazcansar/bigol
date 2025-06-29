<?php
require_once 'header.php';
require_once 'component.php';

@$email = $_SESSION['email'];
@$role = $_SESSION['role'];
@$name = $_SESSION['name'];
@$id = $_SESSION['id'];


// Status Change Request Method Get
if (@$_GET['userIdChangeIbanStatus']) {
    $userId = filter_var($_GET['userIdChangeIbanStatus'], FILTER_SANITIZE_NUMBER_INT);


    $stmt = $db->prepare("SELECT ibanConfirm FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $currentIbanStatus = $user['ibanConfirm'];
        $newIbanStatus = ($currentIbanStatus == 0) ? 1 : 0;

        $currentTime = date('Y-m-d H:i:s');

        $updateStmt = $db->prepare("UPDATE users SET ibanConfirm = ?, updated_at =? WHERE id = ?");
        $updateStmt->bind_param("isi", $newIbanStatus, $currentTime, $userId);

        if ($updateStmt->execute()) {
            echo '<script type="text/javascript">';
            echo 'window.location.href = "adminProfile.php?IbanStatusChange=success";';
            echo '</script>';
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'alert("IBAN onay durumu güncellenirken bir hata oluştu: ' . addslashes($updateStmt->error) . '");';
            echo 'window.location.href = "adminProfile.php?IbanStatusChange=error";';
            echo '</script>';
            exit();
        }
        $updateStmt->close();
    } else {
        echo "Belirtilen ID'ye sahip kullanıcı bulunamadı.";
    }
    $stmt->close();
}


?>
<section class="px-4 sm:px-0 bg-gray-100 w-full">
    <div class="flex flex-row items-center justify-between">
        <span class="px-4 py-2 bg-gray-200 text-gray-800"><?= $name ?> - <?= $email ?></span>
        <a href="logout.php" class="bg-amber-600 hover:bg-amber-500 transition text-white px-4 py-2 inline-block">Çıkış Yap</a>
    </div>
</section>


<section class="px-4 sm:px-0">
    <div class="container mx-auto">
        <h1 class="font-bold text-2xl text-amber-500 mb-2">Kullanıcı Profili</h1>
        <?php
        if (@$_GET['IbanStatusChange']) {
            if ($_GET['IbanStatusChange'] == 'success') {
                echo '<div class="px-4 py-2 bg-green-600 text-white rounded mb-4" role="alert"><i class="bi bi-check-circle"></i> IBAN durumu güncellendi.</div>';
            }
            if ($_GET['IbanStatusChange'] == 'error') {
                echo '<div class="px-4 py-2 bg-amber-700 text-white rounded mb-4" role="alert"><i class="bi bi-exclamation-diamond"></i> IBAN güncelleme sırasında hata meydana geldi!</div>';
            }
        }
        ?>


        <div class="flex flex-col">
            <?php
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if($id === $row['id']) {
                $iban = $row['ibanConfirm'];
            }
            if ($iban == true) {
                $ibanStatus = 'Aktif';
            } else {
                $ibanStatus = 'Deaktif';
            }
            ?>

            <div class="overflow-x-auto mb-4">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Kullanıcı Adı</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">E-posta</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">IBAN Onayı</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">IBAN Onay Tarihi</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php
                        $stmt = $db->prepare("SELECT * FROM users");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $allUsers = $result->fetch_all(MYSQLI_ASSOC);
                        foreach ($allUsers as $user) {
                            $userIban = $user['ibanConfirm'];
                            if ($userIban == true) {
                                $ibanConfirm = 'Aktif';
                            } else {
                                $ibanConfirm = 'Deaktif';
                            }
                            echo '<tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-4">'.$user['name'].'</td>
                                    <td class="py-3 px-4">'.$user['email'].'</td>
                                    <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">'.$ibanConfirm.'</span>
                                    <a href="?userIdChangeIbanStatus='.$user['id'].'" class="px-2 py-1 font-semibold leading-tight text-white bg-blue-400 rounded-full">Değiştir</a>
                                    </td>
                                    <td class="py-3 px-4 text-center">'.$user['updated_at'].'</td>
                                  </tr>';
                        }

                        ?>

                    </tbody>
                </table>
            </div>


            <p class="px-4 py-2 mb-4 bg-blue-600 text-white">Premium Üyelik: <?= $ibanStatus ?></p>
            <p class="px-4 py-2 bg-amber-500 text-white mb-4"><i class="bi bi-info-circle"></i> Tahminler sayfasını görüntüleyebilmek için <strong>premium üye</strong> olmanız gereklidir.</p>
            <p class="px-4 py-2 bg-amber-700 text-white mb-4"><i class="bi bi-info-circle"></i> 30 gün süre ile <strong>Premium üye</strong> olabilmek için aşağıda belirtilen IBAN numarasına 100 TL göndermeniz ve gönderi sonrasında <a href="mailto:" class="font-bold">test@harbigol.com</a> posta adresine ödeme bildirimine ilişkin mail göndermeniz gerekir.</p>
            <p class="px-4 py-2 bg-teal-900 text-white">TR12 0001 0000 1200 1300 0434 09</p>
        </div>
    </div>
</section>


<?php
require_once 'footer.php';
?>
