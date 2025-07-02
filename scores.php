<?php
require_once 'header.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'default';
?>


<div class="w-full">
    <div class="flex flex-row flex-wrap items-center justify-between">
        <a href="?page=skor1" class="border bg-white rounded px-4 py-2">Yapay Zeka Skor 1</a>
        <a href="?page=skor2" class="border bg-white rounded px-4 py-2">Yapay Zeka Skor 2</a>
        <a href="?page=skor3" class="border bg-white rounded px-4 py-2">Yapay Zeka Skor 3</a>
    </div>
</div>

<?php

switch ($page) {
    case 'skor2';
    include 'skor2.php';
    break;

    default;
    include 'skor1.php';
    break;
}

?>


<?php
require_once 'footer.php';
?>
