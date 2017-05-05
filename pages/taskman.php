<h1 class="page-header"><?php lang("task manager") ?></h1>
<div class="well well-sm">
    <a href="app.php?page=edittask" class="btn btn-primary"><i class="fa fa-plus"></i> <?php lang("new task") ?></a>
</div>
<div id="tasksdispdiv" style="<?php
if ($pageid != "taskman") {
    echo "max-height: 600px; overflow-y: auto; padding: 5px;";
}
?>" class="row">
    <?php
    include __DIR__ . '/../lib/gettaskman.php';
    ?>
</div>
<br />
<script>
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
