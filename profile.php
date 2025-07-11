<?php
require_once 'header.php';
require_once 'component.php';

@$email = $_SESSION['email'];
@$role = $_SESSION['role'];
@$name = $_SESSION['name'];
@$id = $_SESSION['id'];

if (empty($email)) {
    echo '<script>';
    echo 'window.location.href = "login.php";';
    echo '</script>';
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
            <p class="px-4 py-2 mb-4 bg-blue-600 text-white">Premium Üyelik: <?= $ibanStatus ?></p>
            <p class="px-4 py-2 bg-amber-500 text-white mb-4"><i class="bi bi-info-circle"></i> Tahminler sayfasını görüntüleyebilmek için <strong>premium üye</strong> olmanız gereklidir.</p>
            <p class="px-4 py-2 bg-amber-700 text-white mb-4"><i class="bi bi-info-circle"></i> 30 gün süreyle Premium Üye olabilmek için, aşağıda belirtilen IBAN numarasına <strong>350 TL</strong> göndermeniz gerekmektedir.</p>
            <p class="px-4 py-2 bg-amber-700 text-white mb-4"><i class="bi bi-envelope"></i> <a href="mailto:fatihgolmak@gmail.com" class="font-bold">fatihgolmak@gmail.com</a> adresine e-posta göndermeniz zorunludur.</p>
            <p class="px-4 py-2 bg-amber-700 text-white mb-4"><i class="bi bi-files"></i> Ödeme sırasında açıklama kısmına sadece mail adresinizi yazınız.
                Başka herhangi bir açıklama girilmesi durumunda üyelikler onaylanmayacaktır.</p>
            <p class="px-4 py-2 bg-teal-900 text-white">IBAN Bilgisi:
                <strong>TR48 0001 2009 6900 0099 0000 07</strong><br>
                Alıcı Adı: Fatih Birinci<br>
                Açıklama: mail adresiniz (örnek: deneme@test.com)</p>
        </div>
    </div>
</section>


<?php
require_once 'footer.php';
?>
