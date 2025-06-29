<?php
require_once 'header.php';
?>

<section class="px-4 sm:px-0">
    <div class="container mx-auto">
        <div class="flex flex-row items-center justify-center">
            <div class="w-[540px]">
                <div class="border p-4 rounded shadow bg-white">
                    <h1 class="mb-4 font-medium text-xl text-teal-900">Giriş Yap</h1>
                    <form action="" method="POST">
                        <label class="text-gray-800 my-2 block" for="email">E-Posta Adresi</label>
                        <input type="text" name="email" class="w-full rounded border px-2 py-1 outline-none mb-4" id="email" placeholder="E-Mail">
                        <label class="text-gray-800 my-2 block" for="password">Şifre</label>
                        <input type="password" name="password" class="w-full rounded border px-2 py-1 outline-none mb-4" id="password" placeholder="*******">
                        <button type="submit" class="inline-block bg-blue-600 hover:bg-blue-500 text-white transition px-4 py-2 rounded-lg">Giriş Yap</button>
                    </form>
                </div>
            </div>
        </div>


    </div>
</section>


<?php
require_once 'footer.php';
?>
