<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<h2 class="page-header"><?php lang("my tasks") ?></h2>
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