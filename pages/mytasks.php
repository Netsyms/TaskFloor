<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div id="tasksdispdiv" class="row<?php
if ($pageid != "mytasks") {
    echo ' home-list-container"';
}
?>">
    <?php
    include __DIR__ . '/../lib/gettasks.php';
    ?>
</div>
<br />
<script nonce="<?php echo $SECURE_NONCE; ?>">
    function refreshTasks() {
        $.get('lib/gettasks.php<?php if ($pageid == "mytasks") {
        echo "?alone=1";
    } ?>', function (data) {
            $('#tasksdispdiv').html(data);
        });
    }
    
    function refreshTasksSoon() {
        setTimeout(refreshTasks, 100);
    }
    setInterval(refreshTasks, 10 * 1000);
</script>