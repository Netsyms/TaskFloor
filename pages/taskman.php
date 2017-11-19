<?php
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
