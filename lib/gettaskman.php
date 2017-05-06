<?php
require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

require_once __DIR__ . "/userinfo.php";
require_once __DIR__ . "/manage.php";

$managed_uids = getManagedUIDs($_SESSION['uid']);

// There needs to be at least one entry otherwise the SQL query craps itself
if (count($managed_uids) < 1) {
    $managed_uids = [-1];
}

$tasks = $database->select('tasks', [
    '[>]assigned_tasks' => [
        'taskid' => 'taskid'
    ]
        ], [
    'tasks.taskid',
    'tasktitle (title)',
    'taskdesc (desc)',
    'taskassignedon (assigned)',
    'taskdueby (due)',
    'userid',
    'statusid',
    'starttime',
    'endtime'
        ], [
    "AND" =>
    [
        "OR" => [
            'tasks.taskcreatoruid' => $_SESSION['uid'],
            'assigned_tasks.userid' => $managed_uids
        ],
        "tasks.deleted" => 0
    ]
        ]
);

if (count($tasks) > 0) {
    $usercache = [];
    foreach ($tasks as $task) {
        if (isset($_GET['alone']) || (isset($pageid) && $pageid != "home")) {
            echo "<div class=\"col-xs-12 col-md-6\">";
        }
        $panelclass = "default";
        switch ($task['statusid']) {
            case 2:
                $panelclass = "success";
                break;
            case 4:
                $panelclass = "warning";
                break;
        }
        ?>
        <div class='panel panel-<?php echo $panelclass ?>'>
            <div class='panel-heading'>
                <h3 class='panel-title'><?php echo $task['title'] ?> 
                    <?php
                    // Check if the task is assigned to someone and show status if it is
                    if (!is_null($task['userid'])) {
                        echo "<span class='pull-right'>";
                        if (!isset($usercache[$task['userid']])) {
                            $usercache[$task['userid']] = getUserByID($task['userid']);
                        }
                        echo "<i class='fa fa-user fa-fw'></i> " . $usercache[$task['userid']]['name'] . " ";
                        switch ($task['statusid']) {
                            case 0:
                                echo "<i class='fa fa-ellipsis-h fa-fw'></i> " . lang("pending", false);
                                break;
                            case 1:
                                echo "<i class='fa fa-play fa-fw'></i> " . lang("started", false);
                                break;
                            case 2:
                                echo "<i class='fa fa-check fa-fw'></i> " . lang("finished", false);
                                break;
                            case 3:
                                echo "<i class='fa fa-pause fa-fw'></i> " . lang("paused", false);
                                break;
                            case 4:
                                echo "<i class='fa fa-stop fa-fw'></i> " . lang("problem", false);
                                break;
                        }
                        echo "</span>";
                    }
                    ?>
                </h3>
            </div>
            <div class='panel-body'>
                <?php echo $task['desc'] ?> 
            </div>
            <div class='panel-footer'>
                <div class='row'>
                    <div class='col-xs-12 col-sm-8 col-md-8'>
                        <i class='fa fa-hourglass-start fa-fw'></i> <?php lang2("assigned on", ["date" => ($task['assigned'] > 0 ? date("F j, Y, g:i a", strtotime($task['assigned'])) : lang("no assigned date", false))]) ?> 
                        <br />
                        <i class='fa fa-hourglass-end fa-fw'></i> <?php lang2("due by", ["date" => ($task['due'] > 0 ? date("F j, Y, g:i a", strtotime($task['due'])) : lang("no due date", false))]) ?> 
                        <?php
                        if ($task['statusid'] > 0) {
                            ?>
                            <br />
                            <i class='fa fa-play fa-fw'></i> <?php lang2("started on", ["date" => date("F j, Y, g:i a", strtotime($task['starttime']))]) ?> 
                            <?php
                        }
                        ?>
                        <?php
                        if ($task['statusid'] == 2) {
                            ?>
                            <br />
                            <i class='fa fa-stop fa-fw'></i> <?php lang2("finished on", ["date" => date("F j, Y, g:i a", strtotime($task['endtime']))]) ?> 
                            <?php
                        }
                        ?>
                    </div>
                    <div class='col-xs-12 col-sm-4 col-md-4'>
                        <div class='pull-right'>
                            <form action='app.php?page=edittask' method='GET' class='form-inline' style='display: inline-block;'>
                                <input type='hidden' name='page' value='edittask' />
                                <input type='hidden' name='taskid' value='<?php echo $task['taskid'] ?>' />
                                <button type='submit' class='btn btn-sm btn-primary' data-toggle="tooltip" data-placement="auto left" title="<?php lang("edit task") ?>"><i class='fa fa-pencil'></i></button>
                            </form>
                            <form action='action.php' onsubmit='$("#deltaskbtn<?php echo $task['taskid'] ?>").prop("disabled", true);' method='POST' class='form-inline' style='display: inline-block; padding-left: 5px;'>
                                <input type='hidden' name='taskid' value='<?php echo $task['taskid'] ?>' />
                                <input type='hidden' name='action' value='deltask' />
                                <?php
                                if (!is_null($task['userid'])) {
                                    ?>
                                    <input type='hidden' name='assigned' value='1' />
                                    <?php
                                }
                                ?>
                                <button type='submit' id='deltaskbtn<?php echo $task['taskid'] ?>' class='btn btn-sm btn-danger' data-toggle="tooltip" data-placement="auto left" title="<?php lang("delete task") ?>"><i class='fa fa-trash-o'></i></button>
                            </form>
                        </div>
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
    } else {
        echo "<div style=\"height: 52px;\"></div>";
    }
    echo "<div class='alert alert-info'><i class='fa fa-info-circle'></i> " . lang("no tasks", false) . "</div>";
    if (isset($_GET['alone']) || (isset($pageid) && $pageid != "home")) {
        echo "</div>";
    }
}
//var_dump($tasks);
?>