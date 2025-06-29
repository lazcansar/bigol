<?php
require_once 'header.php';
require_once 'component.php';
?>

<section class="px-4 sm:px-0">
    <div class="container mx-auto">
        <div class="flex flex-row items-center justify-center">
            <div class="w-[540px]">
                <?php
                if (@$_GET['register']) {
                    if ($_GET['register'] == 'success') {
                        echo '<div class="px-4 py-2 bg-green-600 text-white rounded mb-4" role="alert"><i class="bi bi-check-circle"></i> Kayıt Başarılı.</div>';
                    }
                    if ($_GET['register'] == 'fail') {
                        echo '<div class="px-4 py-2 bg-amber-700 text-white rounded mb-4" role="alert"><i class="bi bi-exclamation-diamond"></i> Kayıt sırasında hata meydana geldi!</div>';
                    }
                }
                ?>
                <div class="border p-4 rounded shadow bg-white">
                    <h1 class="mb-4 font-medium text-xl text-teal-900">Hesap Oluştur</h1>
                    <form action="operations.php" method="POST">
                        <?php
                        echo inputCompontent("text", "name", 'Ad Soyad');
                        echo inputCompontent("text", "email", 'E-Mail');
                        echo inputCompontent("tel", "phone", 'Telefon Numarası');
                        echo inputCompontent("password", "password", 'Şifre');
                        ?>
                        <div class="flex flex-row flex-wrap items-center justify-between">
                            <button type="submit" name="register" class="inline-block bg-green-600 hover:bg-green-500 text-white transition px-4 py-2 rounded-lg">Kayıt Ol</button>
                            <a href="login.php" class="inline-block bg-blue-600 hover:bg-blue-500 text-white transition px-4 py-2 rounded-lg">Hesabınız mı var, hemen giriş yapın</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
</section>


<?php
require_once 'footer.php';
?>
