<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


/**
 * Simple JSON API to allow other apps to access data from this app.
 * 
 * Requests can be sent via either GET or POST requests.  POST is recommended
 * as it has a lower chance of being logged on the server, exposing unencrypted
 * user passwords.
 */
require __DIR__ . '/required.php';
require_once __DIR__ . '/lib/login.php';
require_once __DIR__ . '/lib/userinfo.php';
header("Content-Type: application/json");

$username = $VARS['username'];
$password = $VARS['password'];
if (user_exists($username) !== true || (authenticate_user($username, $password, $errmsg) !== true && checkAPIKey($password) !== true)) {
    header("HTTP/1.1 403 Unauthorized");
    die("\"403 Unauthorized\"");
}

if (!account_has_permission($username, "TASKFLOOR")) {
    header("HTTP/1.1 403 Unauthorized");
    die("\"403 Unauthorized\"");
}

$userinfo = getUserByUsername($username);

// query max results
$max = 20;
if (preg_match("/^[0-9]+$/", $VARS['max']) === 1 && $VARS['max'] <= 1000) {
    $max = (int) $VARS['max'];
}

switch ($VARS['action']) {
    case "gettasks":
        $tasks = $database->query("SELECT * FROM assigned_tasks LEFT JOIN tasks ON assigned_tasks.taskid = tasks.taskid WHERE assigned_tasks.userid = '" . $userinfo['uid'] . "' AND assigned_tasks.statusid IN (0,1,3,4) AND taskassignedon <= NOW() AND tasks.deleted = 0 ORDER BY 0 - taskdueby DESC LIMIT $max")->fetchAll();
        $out = ["status" => "OK", "maxresults" => $max, "tasks" => []];
        foreach ($tasks as $task) {
            $icon = "ellipsis-h";
            switch ($task['statusid']) {
                case 1:
                    $icon = "play";
                    break;
                case 2:
                    $icon = "check";
                    break;
                case 3:
                    $icon = "pause";
                    break;
                case 4:
                    $icon = "exclamation";
                    break;
            }
            $out['tasks'][] = [
                "id" => $task['taskid'],
                "title" => $task['tasktitle'],
                "description" => $task['taskdesc'],
                "assigned" => date("F j, Y, g:i a", strtotime($task['taskassignedon'])),
                "due" => ($task['taskdueby'] > 0 ? date("F j, Y, g:i a", strtotime($task['taskdueby'])) : null),
                "status" => $task['statusid'],
                "icon" => $icon
            ];
        }
        exit(json_encode($out));
    case "getmsgs":
        $messages = $database->select(
                'messages', [
            'messageid (id)',
            'messagetext (text)',
            'messagedate (date)',
            'to',
            'from'
                ], [
            "AND" => [
                "OR" => [
                    "to" => $userinfo['uid'],
                    "to #null" => null,
                    "from" => $userinfo['uid']
                ],
                "deleted" => 0
            ],
            "ORDER" => [
                "messagedate" => "DESC"
            ],
            "LIMIT" => $max]
        );

        $out = ["status" => "OK", "maxresults" => $max, "messages" => []];
        $usercache = [];
        foreach ($messages as $msg) {
            $to = null;
            if (!isset($usercache[$msg['from']])) {
                $usercache[$msg['from']] = getUserByID($msg['from']);
            }
            if (is_null($msg['to'])) {
                $to['name'] = lang("all users", false);
                $to['username'] = lang("all users", false);
            } else {
                if (!isset($usercache[$msg['to']])) {
                    $usercache[$msg['to']] = getUserByID($msg['to']);
                }
                $to = $usercache[$msg['to']];
            }

            $out['messages'][$msg['id']] = [
                "text" => $msg['text'],
                "from" => [
                    "username" => $usercache[$msg['from']]['username'],
                    "name" => $usercache[$msg['from']]['name']
                ],
                "to" => [
                    "username" => $to['username'],
                    "name" => $to['name']
                ],
                "sent" => date("F j, Y, g:i a", strtotime($msg['date']))
            ];
        }
        exit(json_encode($out));
    case "updatetask":
        if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $userinfo['uid']]])) {
            die('{"status": "ERROR", "msg": "You are not assigned to this task!"}');
        }

        switch ($VARS['status']) {
            case "start":
                $database->update('assigned_tasks', ['starttime' => date("Y-m-d H:i:s"), 'statusid' => 1], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $userinfo['uid']]]);
                break;
            case "resume":
                if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $userinfo['uid'], 'starttime[!]' => null]])) {
                    die('{"status": "ERROR", "msg": "Cannot resume non-started task."}');
                }
                $database->update('assigned_tasks', ['statusid' => 1], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $userinfo['uid']]]);
                break;
            case "finish":
                $database->update('assigned_tasks', ['endtime' => date("Y-m-d H:i:s"), 'statusid' => 2], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $userinfo['uid']]]);
                break;
            case "pause":
                $database->update('assigned_tasks', ['statusid' => 3], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $userinfo['uid']]]);
                break;
            case "problem":
                $database->update('assigned_tasks', ['statusid' => 4], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $userinfo['uid']]]);
                break;
            default:
                die('{"status": "ERROR", "msg": "Invalid status requested."}');
        }
        die('{"status": "OK", "msg": "Task updated."}');
    case "sendmsg":
        $msg = strip_tags($VARS['msg']);
        if (user_exists($VARS['to'])) {
            $to = getUserByUsername($VARS['to'])['uid'];
        } else {
            die('{"status": "ERROR", "msg": "Invalid user."}');
        }
        if (is_empty($msg)) {
            die('{"status": "ERROR", "msg": "Missing message."}');
        }
        $database->insert('messages', ['messagetext' => $msg, 'messagedate' => date("Y-m-d H:i:s"), 'from' => $userinfo['uid'], 'to' => $to]);
        die('{"status": "OK"}');
    default:
        header("HTTP/1.1 400 Bad Request");
        die("\"400 Bad Request\"");
}