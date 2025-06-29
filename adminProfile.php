<?php
require_once 'header.php';
require_once 'component.php';

@$email = $_SESSION['email'];
@$role = $_SESSION['role'];
@$name = $_SESSION['name'];
@$id = $_SESSION['id'];
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

            <div class="overflow-x-auto mb-4">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Kullanıcı Adı</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">E-posta</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Telefon</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">IBAN Onayı</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700">
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4">Ahmet Yılmaz</td>
                        <td class="py-3 px-4">ahmet.yilmaz@example.com</td>
                        <td class="py-3 px-4">+90 5XX XXX XX XX</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Onaylı</span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4">Ayşe Demir</td>
                        <td class="py-3 px-4">ayse.demir@example.com</td>
                        <td class="py-3 px-4">+90 5XX XXX XX XX</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Beklemede</span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-4">Mehmet Kara</td>
                        <td class="py-3 px-4">mehmet.kara@example.com</td>
                        <td class="py-3 px-4">+90 5XX XXX XX XX</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Onaylı</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100">
                        <td class="py-3 px-4">Zeynep Can</td>
                        <td class="py-3 px-4">zeynep.can@example.com</td>
                        <td class="py-3 px-4">+90 5XX XXX XX XX</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Beklemede</span>
                        </td>
                    </tr>
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
