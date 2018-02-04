<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$home = false;

if ($pageid == "home") {
    $home = true;
}
?>
<div id="tasksdispdiv" class="<?php
     if ($home) {
         echo "list-group";
     } else {
         echo "row justify-content-center";
     }
     ?>">
         <?php
         include __DIR__ . '/../lib/gettasks.php';
         ?>
</div>
<br />
<script nonce="<?php echo $SECURE_NONCE; ?>">
    function refreshTasks() {
        $.get('lib/gettasks.php?home=<?php
         echo ($home ? "1" : "0");
         ?>', function (data) {
            $('#tasksdispdiv').html(data);
        });
    }

    function refreshTasksSoon() {
        setTimeout(refreshTasks, 100);
    }
    setInterval(refreshTasks, 10 * 1000);
</script>