<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=edittask" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("new task") ?></a>
</div>
<div id="tasksdispdiv" class="row<?php
if ($pageid != "taskman") {
    echo ' home-list-container"';
}
?>">
    <?php
    include __DIR__ . '/../lib/gettaskman.php';
    ?>
</div>
<br />
<script nonce="<?php echo $SECURE_NONCE; ?>">
    function refreshTasks() {
        $.get('lib/gettaskman.php<?php
    if ($pageid == "taskman") {
        echo "?alone=1";
    }
    ?>', function (data) {
            $('#tasksdispdiv').html(data);
            setupTooltips();
        });
    }
    setInterval(function () {
        refreshTasks();
    }, 10 * 1000);
</script>
