<?php
function inputCompontent ($type, $name, $placeholder) {
    return <<<EOT
<div class="mb-4">
    <label class="text-gray-800 my-2 block" for="$name">$placeholder</label>
    <input type="$type" name="$name" class="w-full rounded border px-2 py-1 outline-none" id="$name" placeholder="$placeholder">
</div>
EOT;
}

function loginCard () {
    return <<<EOT
<div class="border rounded bg-white shadow">
                <div class="bg-amber-500 text-gray-900 font-bold px-4 py-2 rounded-t">
                    Hemen Üye Ol - <span class="font-bold text-gray-900">%50 İndirim Fırsatını Kaçırma</span>
                </div>
                <div class="bg-white px-4 py-2">
                    <ul class="flex flex-col gap-4">
                        <li><i class="bi bi-tags text-yellow-500"></i> Aylık sadece 350 TL</li>
                        <li><i class="bi bi-award text-yellow-500"></i> %80 başarı oranına sahip tahminlere erişim</li>
                        <li><i class="bi bi-star text-yellow-500"></i> 1 ay süre ile premium üyelik</li>
                    </ul>
                </div>
                <div class="bg-white px-4 py-4">
                    <a href="register.php" class="px-4 py-2 rounded bg-blue-600 transition hover:bg-blue-500 text-white font-bold">Hemen Üye Ol</a>
                </div>
            </div>
EOT;

}



?>



