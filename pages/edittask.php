<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="card border-blue-grey">
    <h3 class="card-header text-blue-grey">
        <i class="fas fa-edit"></i> <?php
        if (is_empty($VARS['taskid'])) {
            $Strings->get("add task");
        } else {
            $Strings->get("edit task");
        }
        ?>
    </h3>

    <?php
    if (!is_empty($VARS['taskid'])) {
        $taskid = $VARS['taskid'];

        $managed_users = (new User($_SESSION['uid']))->getManagedUsers();
        $managed_uids = [];
        foreach ($managed_users as $u) {
            $managed_uids[] = $u->getUID();
        }
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
            die($Strings->get("task edit not allowed", false));
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
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <?php $Strings->get("task title") ?>: <input type="text" name="tasktitle" placeholder="<?php $Strings->get("task title") ?>" required="required" class="form-control" value="<?php echo $task['tasktitle']; ?>"/>
                </div>
                <div class="col-12 mb-3">
                    <?php $Strings->get("task description") ?>:<br />
                    <textarea name="taskdesc" id="taskdesc" class="form-control"><?php echo $task['taskdesc']; ?></textarea>
                </div>
                <div class="col-12 col-lg-4 mb-3">
                    <?php $Strings->get("assigned to") ?>:
                    <input type="text" id="assigned-to-box" name="assignedto" class="form-control" autocomplete="off" value="<?php echo (is_null($tass['userid']) ? "" : (new User($tass['userid']))->getUsername() ); ?>" placeholder="<?php $Strings->get("nobody") ?>" />
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <?php $Strings->get("assigned on 2") ?>: <input type="text" class="form-control" id="assigned-on-box" name="taskassignedon" data-toggle="datetimepicker" data-target="#assigned-on-box" value="<?php echo (is_empty($task['taskassignedon']) ? "" : date("D F j, Y g:i a"/* 'Y-m-d\TH:i' */, strtotime($task['taskassignedon']))); ?>" />
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <?php $Strings->get("due by 2") ?>: <input type="text" class="form-control" id="due-by-box" name="taskdueby" data-toggle="datetimepicker" data-target="#due-by-box" value="<?php echo (is_empty($task['taskdueby']) ? "" : date("D F j, Y g:i a", strtotime($task['taskdueby']))); ?>"/>
                </div>
            </div>
        </div>
        <input type="hidden" name="action" value="edittask" />
        <?php if (!is_empty($taskid)) { ?>
            <input type="hidden" name="taskid" value="<?php echo $taskid; ?>" />
        <?php } ?>
        <div class="card-footer d-flex">
            <button id="savebtn" type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php $Strings->get("save task") ?></button>
            <a class="btn btn-warning" href="app.php?page=taskman"><i class="fas fa-times"></i> <?php $Strings->get("exit") ?></a>
        </div>
    </form>
</div>
