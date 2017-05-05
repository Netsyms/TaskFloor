<h2 class="page-header"><?php lang("my tasks") ?></h2>
<div id="tasksdispdiv" style="<?php if ($pageid != "mytasks") {
        echo "max-height: 600px; overflow-y: auto; padding: 5px;";
    } ?>" class="row">
    <?php
    include __DIR__ . '/../lib/gettasks.php';
    ?>
</div>
<br />
<script>
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