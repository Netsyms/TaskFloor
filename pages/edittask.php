<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="panel panel-blue">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-pencil-square-o"></i> <?php
            if (is_empty($VARS['taskid'])) {
                lang("add task");
            } else {
                lang("edit task");
            }
            ?>
        </h3>
    </div>

    <?php
    include_once __DIR__ . "/../lib/userinfo.php";
    include_once __DIR__ . "/../lib/manage.php";

    if (!is_empty($VARS['taskid'])) {
        $taskid = $VARS['taskid'];

        $managed_uids = getManagedUIDs($_SESSION['uid']);
        // There needs to be at least one entry otherwise the SQL query craps itself
        if (count($managed_uids) < 1) {
            $managed_uids = [-1];
        }
        $allowed = $database->has('tasks', [
            '[>]assigned_tasks' => [
                'taskid' => 'taskid'
            ]
                ], [
            "AND" => [
                "OR" => [
                    'tasks.taskcreatoruid' => $_SESSION['uid'],
                    'assigned_tasks.userid' => $managed_uids
                ],
                "tasks.taskid" => $taskid
        ]]);

        if (!$allowed) {
            header("Location: app.php?page=edittask&msg=task_edit_not_allowed");
            die(lang("task edit not allowed", false));
        }
    }

    if (!is_empty($taskid)) {
        $task = $database->select('tasks', '*', ['taskid' => $taskid])[0];
    }
    if (!is_empty($taskid) && $database->has('assigned_tasks', ['taskid' => $taskid])) {
        $tass = $database->select('assigned_tasks', '*', ['taskid' => $taskid])[0];
    } else {
        $tass['userid'] = null;
    }
    ?>
    <form action="action.php" method="GET" onsubmit="prettysave();">
        <div class="panel-body">
            <input type="hidden" name="action" value="edittask" />
            <?php if (!is_empty($taskid)) { ?>
                <input type="hidden" name="taskid" value="<?php echo $taskid; ?>" />
            <?php } ?>
            <?php lang("task title") ?>: <input type="text" name="tasktitle" placeholder="<?php lang("task title") ?>" required="required" class="form-control" value="<?php echo $task['tasktitle']; ?>"/>
            <br />
            <?php lang("task description") ?>:<br />
            <textarea name="taskdesc" id="taskdesc" class="form-control"><?php echo $task['taskdesc']; ?></textarea>
            <br />
            <?php lang("assigned to") ?>: 
            <input type="text" id="assigned-to-box" name="assignedto" class="form-control" autocomplete="off" value="<?php echo (is_null($tass['userid']) ? "" : getUserByID($tass['userid'])['username'] ); ?>" placeholder="<?php lang("nobody") ?>" />
            <br />
            <?php lang("assigned on 2") ?>: <input type="datetime-local" class="form-control" id="assigned-on-box" name="taskassignedon" value="<?php echo (is_empty($task['taskassignedon']) ? "" : date('o-m-d\TH:i:s', strtotime($task['taskassignedon']))); ?>" />
            <!--<p><i class="fa fa-info-circle"></i> <?php lang("use now tip") ?></p>-->
            <br />
            <?php lang("due by 2") ?>: <input type="datetime-local" class="form-control" id="due-by-box" name="taskdueby" value="<?php echo (is_empty($task['taskdueby']) ? "" : date('o-m-d\TH:i:s', strtotime($task['taskdueby']))); ?>"/>
        </div>
        <div class="panel-footer">
            <button id="savebtn" type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save task") ?></button>
            <a class="btn btn-warning pull-right" href="app.php?page=taskman"><i class="fa fa-times"></i> <?php lang("exit") ?></a>
        </div>
    </form>
</div>
