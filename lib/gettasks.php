<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$home = false;

if ((isset($_GET['home']) && $_GET['home'] == "1") || (isset($pageid) && $pageid == "home")) {
    $home = true;
}

//$tasks = $database->debug()->select('assigned_tasks', ['[>]tasks' => ['taskid' => 'taskid']], '*', ["AND" => ['assigned_tasks.userid' => $_SESSION['uid'], 'assigned_tasks.statusid' => [0, 1, 3, 4], '#taskassignedon[<=]' => 'NOW()'], "ORDER" => ['0 - taskdueby' => "DESC"]]);

$tasks = $database->query("SELECT * FROM assigned_tasks LEFT JOIN tasks ON assigned_tasks.taskid = tasks.taskid WHERE assigned_tasks.userid = '" . $_SESSION['uid'] . "' AND assigned_tasks.statusid IN (0,1,3,4) AND taskassignedon <= NOW() AND tasks.deleted = 0 ORDER BY 0 - taskdueby DESC")->fetchAll();
if (count($tasks) > 0) {
    foreach ($tasks as $task) {
        $colorclass = 'border-blue-grey';
        if ($task['taskdueby'] == null) {
            // This bit is just here to skip the rest of the branches if we need to
        } else if (strtotime($task['taskdueby']) - time() < 0) { // deadline overdue
            $colorclass = 'border-red';
        } else if (strtotime($task['taskdueby']) - time() < 60 * 60 * 3) { // less than three hours
            $colorclass = 'border-orange';
        } else if (strtotime($task['taskdueby']) - time() < 60 * 60 * 8) { // less than eight hours
            $colorclass = 'border-blue';
        }

        $statusicon = "fas fa-ellipsis-h";
        if ($task['statusid'] == 1) {
            $statusicon = 'fas fa-play';
        } else if ($task['statusid'] == 3) {
            $statusicon = 'fas fa-play';
        } else if ($task['statusid'] == 4) {
            $statusicon = 'fas fa-exclamation';
        }

        $btns = "";
        if ($task['statusid'] == 0) {
            $btns = "<form
                action='action.php'
                method='POST'
                data-taskid=\"" . $task['taskid'] . "\"
                data-action=\"start\"
                class='bin-btn'>
                <input type='hidden' name='taskid' value='" . $task['taskid'] . "' />
                <input type='hidden' name='action' value='start' />
                <button type='submit' id='starttaskbtn" . $task['taskid'] . "' class='btn btn-primary'><i class='fas fa-fw fa-play'></i> " . lang("start", false) . "</button>
            </form>";
        } else if ($task['statusid'] == 1) {
            $btns = "<form
                action='action.php'
                method='POST'
                data-taskid=\"" . $task['taskid'] . "\"
                data-action=\"finish\"
                class='bin-btn'>
                <input type='hidden' name='taskid' value='" . $task['taskid'] . "' />
                <input type='hidden' name='action' value='finish' />
                <button type='submit' id='finishtaskbtn" . $task['taskid'] . "' class='btn btn-success'><i class='fas fa-fw fa-stop'></i> " . lang("finish", false) . "</button>
            </form>";
            $btns .= "<form
                action='action.php'
                method='POST'
                data-taskid=\"" . $task['taskid'] . "\"
                data-action=\"pause\"
                class='bin-btn'>
                <input type='hidden' name='taskid' value='" . $task['taskid'] . "' />
                <input type='hidden' name='action' value='pause' />
                <button type='submit' id='pausetaskbtn" . $task['taskid'] . "' class='btn btn-warning'><i class='fas fa-fw fa-pause'></i> " . lang("pause", false) . "</button>
            </form>";
            $btns .= "<form
                action='action.php'
                method='POST'
                data-taskid=\"" . $task['taskid'] . "\"
                data-action=\"problem\"
                class='bin-btn'>
                <input type='hidden' name='taskid' value='" . $task['taskid'] . "' />
                <input type='hidden' name='action' value='problem' />
                <button type='submit' id='problemtaskbtn" . $task['taskid'] . "' class='btn btn-danger'><i class='fas fa-fw fa-exclamation'></i> " . lang("problem", false) . "</button>
            </form>";
        } else if ($task['statusid'] == 3 || $task['statusid'] == 4) {
            $btns = "<form
                action='action.php'
                method='POST'
                data-taskid=\"" . $task['taskid'] . "\"
                data-action=\"resume\"
                class='bin-btn'>
                <input type='hidden' name='taskid' value='" . $task['taskid'] . "' />
                <input type='hidden' name='action' value='resume' />
                <button type='submit' id='resumetaskbtn" . $task['taskid'] . "' class='btn btn-primary'><i class='fas fa-fw fa-play'></i> " . lang("resume", false) . "</button>
            </form>";
        }

        $assignedon = "<i class='fas fa-hourglass-start fa-fw'></i> " . lang2("assigned on", ["date" => date("F j, Y, g:i a", strtotime($task['taskassignedon']))], false);
        $dueby = "<i class='fas fa-hourglass-end fa-fw'></i> " . lang2("due by", ["date" => ($task['taskdueby'] > 0 ? date("F j, Y, g:i a", strtotime($task['taskdueby'])) : lang("no due date", false))], false);
        $startedon = "";
        if ($task['statusid'] > 0) {
            $startedon = "<i class='fas fa-play fa-fw'></i> " . lang2("started on", ["date" => date("F j, Y, g:i a", strtotime($task['starttime']))], false);
        }
        $finishedon = "";
        if ($task['statusid'] == 2) {
            $finishedon = "<i class='fas fa-stop fa-fw'></i> " . lang2("finished on", ["date" => date("F j, Y, g:i a", strtotime($task['endtime']))], false);
        }
        if ($home) {
            ?>
            <div class="list-group-item <?php echo $colorclass
            ?>">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><i class="<?php echo $statusicon ?> fa-fw"></i> <?php echo $task['tasktitle'] ?></h5>
                </div>
                <p class="task-description"><?php echo $task['taskdesc'] ?></p>
                <div class="row">
                    <div class="col-12 col-md-6 list-group">
                        <div class="list-group-item">
                            <?php echo $assignedon ?>
                        </div>
                        <div class="list-group-item">
                            <?php echo $dueby ?>
                        </div>
                        <?php if ($startedon != "") { ?>
                            <div class="list-group-item">
                                <?php echo $startedon ?>
                            </div>
                        <?php } ?>
                        <?php if ($finishedon != "") { ?>
                            <div class="list-group-item">
                                <?php echo $finishedon ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-12 col-md-6 btn-bin">
                        <?php echo $btns; ?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class='card <?php echo $colorclass ?>'>
                <h4 class='card-header'>
                    <i class="<?php echo $statusicon ?> fa-fw"></i> <?php echo $task['tasktitle'] ?>
                </h4>
                <div class='card-body task-description'>
                    <?php echo $task['taskdesc'] ?>
                </div>
                <div class='card-footer'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 list-group'>
                            <div class="list-group-item">
                                <?php echo $assignedon ?>
                            </div>
                            <div class="list-group-item">
                                <?php echo $dueby ?>
                            </div>
                            <?php if ($startedon != "") { ?>
                                <div class="list-group-item">
                                    <?php echo $startedon ?>
                                </div>
                            <?php } ?>
                            <?php if ($finishedon != "") { ?>
                                <div class="list-group-item">
                                    <?php echo $finishedon ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class='col-12 col-sm-6 btn-bin'>
                            <?php echo $btns; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
} else {
    if (!$home) {
        echo "<div class=\"col-12 col-md-6\">";
    }
    echo "<div class='alert alert-success'><i class='fas fa-check'></i> " . lang("all caught up", false) . "</div>";
    if (!$home) {
        echo "</div>";
    }
}
?>
<?php
if (DEBUG) {
    var_dump($tasks);
}
?>