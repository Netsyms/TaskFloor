<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

//$tasks = $database->debug()->select('assigned_tasks', ['[>]tasks' => ['taskid' => 'taskid']], '*', ["AND" => ['assigned_tasks.userid' => $_SESSION['uid'], 'assigned_tasks.statusid' => [0, 1, 3, 4], '#taskassignedon[<=]' => 'NOW()'], "ORDER" => ['0 - taskdueby' => "DESC"]]);

$tasks = $database->query("SELECT * FROM assigned_tasks LEFT JOIN tasks ON assigned_tasks.taskid = tasks.taskid WHERE assigned_tasks.userid = '" . $_SESSION['uid'] . "' AND assigned_tasks.statusid IN (0,1,3,4) AND taskassignedon <= NOW() AND tasks.deleted = 0 ORDER BY 0 - taskdueby DESC")->fetchAll();
if (count($tasks) > 0) {
    foreach ($tasks as $task) {
        if (isset($_GET['alone']) || (isset($pageid) && $pageid != "home")) {
            echo "<div class=\"col-xs-12 col-md-6\">";
        }
        $panelclass = 'panel-default';
        if ($task['taskdueby'] == null) {
            // This bit is just here to skip the rest of the branches if we need to
        } else if (strtotime($task['taskdueby']) - time() < 0) { // deadline overdue
            $panelclass = 'panel-danger';
        } else if (strtotime($task['taskdueby']) - time() < 60 * 60 * 3) { // less than three hours
            $panelclass = 'panel-warning';
        } else if (strtotime($task['taskdueby']) - time() < 60 * 60 * 8) { // less than eight hours
            $panelclass = 'panel-primary';
        }
        ?>
        <div class='panel <?php echo $panelclass ?>'>
            <div class='panel-heading'>
                <h3 class='panel-title'><?php echo $task['tasktitle'] ?>
                    <?php
                    if ($task['statusid'] == 1) {
                        ?>
                        <span class='pull-right'><i class='fa fa-play'></i> <?php lang("started") ?></span>
                        <?php
                    }
                    ?>
                </h3>
            </div>
            <div class='panel-body'>
                <?php echo $task['taskdesc'] ?>
            </div>
            <div class='panel-footer'>
                <div class='row'>
                    <div class='col-xs-12 col-sm-6 col-md-6'>
                        <i class='fa fa-clock-o'></i> <?php lang2("assigned on", ["date" => date("F j, Y, g:i a", strtotime($task['taskassignedon']))]) ?>
                        <br />
                        <i class='fa fa-clock-o'></i> <?php lang2("due by", ["date" => ($task['taskdueby'] > 0 ? date("F j, Y, g:i a", strtotime($task['taskdueby'])) : "No due date")]) ?>
                    </div>
                    <div class='col-xs-12 col-sm-6 col-md-6'>
                        <?php lang("actions") ?>:<br />
                        <?php
                        if ($task['statusid'] == 0) {
                            ?>
                            <form action='action.php' method='POST' onsubmit='$("#starttaskbtn<?php echo $task['taskid'] ?>").prop("disabled", true); refreshTasksSoon();' class='form-inline' style='display: inline-block;'>
                                <input type='hidden' name='taskid' value='<?php echo $task['taskid'] ?>' />
                                <input type='hidden' name='action' value='start' />
                                <button type='submit' id='starttaskbtn<?php echo $task['taskid'] ?>' class='btn btn-primary'><i class='fa fa-play'></i> <?php lang("start") ?></button>
                            </form>
                            <?php
                        } else if ($task['statusid'] == 1) {
                            ?>
                            <form action='action.php' method='POST' onsubmit='$("#finishtaskbtn<?php echo $task['taskid'] ?>").prop("disabled", true); refreshTasksSoon();' class='form-inline' style='display: inline-block; padding-left: 5px;'>
                                <input type='hidden' name='taskid' value='<?php echo $task['taskid'] ?>' />
                                <input type='hidden' name='action' value='finish' />
                                <button type='submit' id='finishtaskbtn<?php echo $task['taskid'] ?>' class='btn btn-success'><i class='fa fa-stop'></i> <?php lang("finish") ?></button>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (isset($_GET['alone']) || (isset($pageid) && $pageid != "home")) {
            echo "</div>";
        }
    }
} else {
    if (isset($_GET['alone']) || (isset($pageid) && $pageid != "home")) {
        echo "<div class=\"col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4\">";
    }
    echo "<div class='alert alert-success'><i class='fa fa-check'></i> " . lang("all caught up", false) . "</div>";
    if (isset($_GET['alone']) || (isset($pageid) && $pageid != "home")) {
        echo "</div>";
    }
}
?>
<?php
if (DEBUG) {
    var_dump($tasks);
}
?>