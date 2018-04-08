<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";
require_once __DIR__ . "/lib/login.php";
require_once __DIR__ . "/lib/userinfo.php";


if ($VARS['action'] !== "signout") {
    dieifnotloggedin();
}

/**
 * Redirects back to the page ID in $_POST/$_GET['source'] with the given message ID.
 * The message will be displayed by the app.
 * @param string $msg message ID (see lang/messages.php)
 * @param string $arg If set, replaces "{arg}" in the message string when displayed to the user.
 */
function returnToSender($msg, $arg = "") {
    global $VARS;
    if ($arg == "") {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=" . $msg);
    } else {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=$msg&arg=$arg");
    }
    die();
}

if ($VARS['action'] != "signout" && !account_has_permission($_SESSION['username'], "TASKFLOOR")) {
    returnToSender("no_permission");
}

switch ($VARS['action']) {
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
    case "sendmsg":
        header("HTTP/1.1 204 No Content");
        $msg = strip_tags($VARS['msg']);
        if (is_empty($VARS['to'])) {
            $to = null;
            die(); // TODO: add some kind of permission thing to allow this
        } else if (user_exists($VARS['to'])) {
            $to = getUserByUsername($VARS['to'])['uid'];
        } else {
            die();
        }
        if (is_empty($msg)) {
            die();
        }
        $database->insert('messages', ['messagetext' => $msg, 'messagedate' => date("Y-m-d H:i:s"), 'from' => $_SESSION['uid'], 'to' => $to]);
        break;
    case "delmsg":
        header('HTTP/1.0 204 No Content');
        if (is_empty($VARS['msgid'])) {
            die();
        }
        if (!$database->has('messages', ['messageid' => $VARS['msgid']])) {
            die();
        }
        $msg = $database->select('messages', ['to', 'from'], ['messageid' => $VARS['msgid']])[0];
        if ($msg['to'] == $_SESSION['uid'] ||
                $msg['from'] == $_SESSION['uid'] ||
                isManagerOf($_SESSION['uid'], $msg['to']) ||
                isManagerOf($_SESSION['uid'], $msg['from'])) {
            $database->update('messages', ['deleted' => 1], ['messageid' => $VARS['msgid']]);
        }
        break;
    case "start":
        if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]])) {
            die('You are not assigned to this task!');
        }
        header('HTTP/1.0 204 No Content');
        $database->update('assigned_tasks', ['starttime' => date("Y-m-d H:i:s"), 'statusid' => 1], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]]);
        break;
    case "resume":
        if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid'], 'starttime[!]' => null]])) {
            die('Invalid operation.');
        }
        header('HTTP/1.0 204 No Content');
        $database->update('assigned_tasks', ['statusid' => 1], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]]);
        break;
    case "finish":
        header('HTTP/1.0 204 No Content');
        if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]])) {
            die('You are not assigned to this task!');
        }
        $database->update('assigned_tasks', ['endtime' => date("Y-m-d H:i:s"), 'statusid' => 2], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]]);
        break;
    case "pause":
        if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]])) {
            die('You are not assigned to this task!');
        }
        header('HTTP/1.0 204 No Content');
        $database->update('assigned_tasks', ['statusid' => 3], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]]);
        break;
    case "problem":
        if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]])) {
            die('You are not assigned to this task!');
        }
        header('HTTP/1.0 204 No Content');
        $database->update('assigned_tasks', ['statusid' => 4], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $_SESSION['uid']]]);
        break;
    case "edittask":
        if (is_empty($VARS['tasktitle'])) {
            header('HTTP/1.0 204 No Content');
            die();
        }

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $taskdesc = $purifier->purify($VARS['taskdesc']);
        //$taskdesc = $VARS['taskdesc'];

        if (is_empty($VARS['taskid'])) {
            $database->insert('tasks', ['tasktitle' => $VARS['tasktitle'], 'taskdesc' => $taskdesc, 'taskcreatoruid' => $_SESSION['uid']]);
            $VARS['taskid'] = $database->id();
            header('Location: app.php?page=edittask&taskid=' . $database->id() . '&msg=task_saved');
        } else {
            $database->update('tasks', ['tasktitle' => $VARS['tasktitle'], 'taskdesc' => $taskdesc], ['taskid' => $VARS['taskid']]);
            header('Location: app.php?page=edittask&taskid=' . $VARS['taskid'] . '&msg=task_saved');
        }

        if (checkIsAValidDate($VARS['taskassignedon'])) {
            $assigneddate = date('Y-m-d H:i:s', strtotime($VARS['taskassignedon']));
            $database->update('tasks', ['taskassignedon' => $assigneddate], ['taskid' => $VARS['taskid']]);
        }
        if (checkIsAValidDate($VARS['taskdueby'])) {
            $duedate = date('Y-m-d H:i:s', strtotime($VARS['taskdueby']));
            $database->update('tasks', ['taskdueby' => $duedate], ['taskid' => $VARS['taskid']]);
        } else if ($VARS['taskdueby'] == "") {
            $database->update('tasks', ['taskdueby' => null], ['taskid' => $VARS['taskid']]);
        }
        if (!is_empty($VARS['assignedto']) && user_exists($VARS['assignedto'])) {
            $uid = getUserByUsername($VARS['assignedto'])['uid'];
            $managed_uids = getManagedUIDs($_SESSION['uid']);
            // allow self-assignment
            if (!in_array($uid, $managed_uids) && $uid != $_SESSION['uid']) {
                header('Location: app.php?page=edittask&taskid=' . $VARS['taskid'] . '&msg=user_not_managed');
                die(lang("user not managed", false));
            }
            if ($database->has('assigned_tasks', ['taskid' => $VARS['taskid']])) {
                $database->update('assigned_tasks', ['userid' => $uid, 'starttime' => null, 'endtime' => null, 'statusid' => 0], ['taskid' => $VARS['taskid']]);
            } else {
                $database->insert('assigned_tasks', ['taskid' => $VARS['taskid'], 'userid' => $uid, 'starttime' => null, 'endtime' => null, 'statusid' => 0]);
            }
        } else if (is_empty($VARS['assignedto'])) {
            $database->delete('assigned_tasks', ['taskid' => $VARS['taskid']]);
        }
        break;
    case "deltask":
        if (is_empty($VARS['taskid'])) {
            die('Missing taskid.');
        }

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
                "tasks.taskid" => $VARS['taskid']
        ]]);

        if (!$allowed) {
            header("Location: app.php?page=taskman&msg=task_delete_not_allowed");
            die(lang("task delete not allowed", false));
        }

        if ($VARS['assigned']) {
            $database->delete('assigned_tasks', ['taskid' => $VARS['taskid']]);
        } else {
            $database->update('tasks', ['deleted' => 1], ['taskid' => $VARS['taskid']]);
        }
        header("Location: app.php?page=taskman&msg=task_deleted");
        break;
    case "autocomplete":
        header("Content-Type: application/json");
        $client = new GuzzleHttp\Client();

        $response = $client
                ->request('POST', PORTAL_API, [
            'form_params' => [
                'key' => PORTAL_KEY,
                'action' => "usersearch",
                'search' => $VARS['q']
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            exit("[]");
        }

        $resp = json_decode($response->getBody(), TRUE);
        if ($resp['status'] == "OK") {
            exit(json_encode($resp['result']));
        } else {
            exit("[]");
        }
        break;
    default:
        die("Invalid request.");
}