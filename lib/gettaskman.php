<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

require_once __DIR__ . "/userinfo.php";

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
            echo "<div class=\"col-12 col-md-6\">";
        }
        $colorclass = "default";
        switch ($task['statusid']) {
            case 2:
                $colorclass = "success";
                break;
            case 4:
                $colorclass = "warning";
                break;
        }
        ?>
        <div class='card border-<?php echo $colorclass ?> my-2'>
            <div class='card-body'>
                <h5 class='card-title'><?php
                    switch ($task['statusid']) {
                        case 0:
                            echo "<i class='fas fa-ellipsis-h fa-fw'></i>";
                            break;
                        case 1:
                            echo "<i class='fas fa-play fa-fw'></i>";
                            break;
                        case 2:
                            echo "<i class='fas fa-check fa-fw'></i>";
                            break;
                        case 3:
                            echo "<i class='fas fa-pause fa-fw'></i>";
                            break;
                        case 4:
                            echo "<i class='fas fa-stop fa-fw'></i>";
                            break;
                    }
                    echo " " . $task['title'];
                    // Check if the task is assigned to someone and show status if it is
                    if (!is_null($task['userid'])) {
                        echo "<span class='float-right text-muted small ml-1 mt-1'>";
                        if (!isset($usercache[$task['userid']])) {
                            $usercache[$task['userid']] = getUserByID($task['userid']);
                        }
                        echo "<i class='fas fa-user fa-fw'></i> " . $usercache[$task['userid']]['name'];
                        echo "</span>";
                    }
                    ?>
                </h5>
                <?php echo $task['desc'] ?>
            </div>
            <div class='card-footer'>
                <div class='row'>
                    <div class='col-12 col-sm-8 list-group'>
                        <div class="list-group-item">
                            <i class='fas fa-hourglass-start fa-fw'></i> <?php lang2("assigned on", ["date" => ($task['assigned'] > 0 ? date("M j, g:i a", strtotime($task['assigned'])) : lang("no assigned date", false))]) ?>
                        </div>
                        <div class="list-group-item">
                            <i class='fas fa-hourglass-end fa-fw'></i> <?php lang2("due by", ["date" => ($task['due'] > 0 ? date("M j, g:i a", strtotime($task['due'])) : lang("no due date", false))]) ?>
                        </div>
                        <?php
                        if ($task['statusid'] > 0) {
                            ?>
                            <div class="list-group-item">
                                <i class='fas fa-play fa-fw'></i> <?php lang2("started on", ["date" => date("M j, g:i a", strtotime($task['starttime']))]) ?>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                        if ($task['statusid'] == 2) {
                            ?>
                            <div class="list-group-item">
                                <i class='fas fa-stop fa-fw'></i> <?php lang2("finished on", ["date" => date("M j, g:i a", strtotime($task['endtime']))]) ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class='col-12 col-sm-4'>
                        <div class='float-right btn-bin'>
                            <form action='app.php?page=edittask' method='GET' class='bin-btn'>
                                <input type='hidden' name='page' value='edittask' />
                                <input type='hidden' name='taskid' value='<?php echo $task['taskid'] ?>' />
                                <button type='submit' class='btn btn-sm btn-primary' data-toggle="tooltip" data-placement="auto left" title="<?php lang("edit task") ?>"><i class='fas fa-edit fa-fw'></i> <?php lang("edit"); ?></button>
                            </form>
                            <form
                                action='action.php'
                                method='POST'
                                data-taskid="<?php echo $task['taskid'] ?>"
                                class='bin-btn'>
                                <input type='hidden' name='taskid' value='<?php echo $task['taskid'] ?>' />
                                <input type='hidden' name='action' value='deltask' />
                                <?php
                                if (!is_null($task['userid'])) {
                                    ?>
                                    <input type='hidden' name='assigned' value='1' />
                                    <?php
                                }
                                ?>
                                <button type='submit' id='deltaskbtn<?php echo $task['taskid'] ?>' class='btn btn-sm btn-danger' data-toggle="tooltip" data-placement="auto left" title="<?php lang("delete task") ?>"><i class='fas fa-trash fa-fw'></i> <?php lang("delete"); ?></button>
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
        echo "<div class=\"col-12 col-md-6\">";
    }
    echo "<div class='alert alert-info'><i class='fas fa-info-circle'></i> " . lang("no tasks", false) . "</div>";
    if (isset($_GET['alone']) || (isset($pageid) && $pageid != "home")) {
        echo "</div>";
    }
}
//var_dump($tasks);
?>