<?php
require_once 'header.php';
require_once 'component.php';
?>

<section class="px-4 sm:px-0">
    <div class="container mx-auto">
        <div class="flex flex-row items-center justify-center">
            <div class="w-[540px]">
                <div class="border p-4 rounded shadow bg-white">
                    <h1 class="mb-4 font-medium text-xl text-teal-900">Giriş Ekranı</h1>
                    <form action="operations.php" method="POST">
                        <?php
                        echo inputCompontent('email', 'email', 'E-mail Adresiniz');
                        echo inputCompontent('password', 'password', 'Şifreniz');
                        ?>
                        <div class="flex flex-row flex-wrap items-center justify-between">
                            <button type="submit" name="login" class="inline-block bg-blue-600 hover:bg-blue-500 text-white transition px-4 py-2 rounded-lg">Giriş Yap</button>
                            <a href="register.php" class="inline-block bg-amber-600 hover:bg-amber-500 text-white transition px-4 py-2 rounded-lg">Hesabınız mı yok? Hemen kayıt ol!</a>
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
