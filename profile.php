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
        <div class="flex flex-row items-center justify-center">

        </div>
    </div>
</section>


<?php
require_once 'footer.php';
?>
