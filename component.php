<?php
function inputCompontent ($type, $name, $placeholder) {
    return <<<EOT
<div class="mb-4">
    <label class="text-gray-800 my-2 block" for="$name">$placeholder</label>
    <input type="$type" name="$name" class="w-full rounded border px-2 py-1 outline-none" id="$name" placeholder="$placeholder">
</div>
EOT;
}

?>