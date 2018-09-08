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
header("Content-Type: application/json");

/**
 * Checks if the given AccountHub API key is valid by attempting to
 * access the API with it.
 * @param String $key The API key to check
 * @return boolean TRUE if the key is valid, FALSE if invalid or something went wrong
 */
function checkAPIKey($key) {
    try {
        $client = new GuzzleHttp\Client();

        $response = $client
                ->request('POST', PORTAL_API, [
            'form_params' => [
                'key' => $key,
                'action' => "ping"
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

$username = $VARS['username'];
$password = $VARS['password'];
$user = User::byUsername($username);
if ($user->exists() !== true || ((Login::auth($username, $password) !== Login::LOGIN_OK) && !checkAPIKey($password))) {
    header("HTTP/1.1 403 Unauthorized");
    die("\"403 Unauthorized\"");
}

if (!$user->hasPermission("TASKFLOOR")) {
    header("HTTP/1.1 403 Unauthorized");
    die("\"403 Unauthorized\"");
}

// query max results
$max = 20;
if (isset($VARS['max']) && preg_match("/^[0-9]+$/", $VARS['max']) === 1 && $VARS['max'] <= 1000) {
    $max = (int) $VARS['max'];
}

switch ($VARS['action']) {
    case "gettasks":
        $tasks = $database->query("SELECT * FROM assigned_tasks LEFT JOIN tasks ON assigned_tasks.taskid = tasks.taskid WHERE assigned_tasks.userid = '" . $user->getUID() . "' AND assigned_tasks.statusid IN (0,1,3,4) AND taskassignedon <= NOW() AND tasks.deleted = 0 ORDER BY 0 - taskdueby DESC LIMIT $max")->fetchAll();
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
                    "to" => $user->getUID(),
                    "to #null" => null,
                    "from" => $user->getUID()
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
                $usercache[$msg['from']] = new User($msg['from']);
            }
            if (is_null($msg['to'])) {
                $to['name'] = lang("all users", false);
                $to['username'] = lang("all users", false);
            } else {
                if (!isset($usercache[$msg['to']])) {
                    $usercache[$msg['to']] = new User($msg['to']);
                }
                $to = $usercache[$msg['to']];
            }

            $out['messages'][$msg['id']] = [
                "text" => $msg['text'],
                "from" => [
                    "username" => $usercache[$msg['from']]->getUsername(),
                    "name" => $usercache[$msg['from']]->getName()
                ],
                "to" => [
                    "username" => $to->getUsername(),
                    "name" => $to->getName()
                ],
                "sent" => date("F j, Y, g:i a", strtotime($msg['date']))
            ];
        }
        exit(json_encode($out));
    case "updatetask":
        if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $user->getUID()]])) {
            die('{"status": "ERROR", "msg": "You are not assigned to this task!"}');
        }

        switch ($VARS['status']) {
            case "start":
                $database->update('assigned_tasks', ['starttime' => date("Y-m-d H:i:s"), 'statusid' => 1], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $user->getUID()]]);
                break;
            case "resume":
                if (!$database->has('assigned_tasks', ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $user->getUID(), 'starttime[!]' => null]])) {
                    die('{"status": "ERROR", "msg": "Cannot resume non-started task."}');
                }
                $database->update('assigned_tasks', ['statusid' => 1], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $user->getUID()]]);
                break;
            case "finish":
                $database->update('assigned_tasks', ['endtime' => date("Y-m-d H:i:s"), 'statusid' => 2], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $user->getUID()]]);
                break;
            case "pause":
                $database->update('assigned_tasks', ['statusid' => 3], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $user->getUID()]]);
                break;
            case "problem":
                $database->update('assigned_tasks', ['statusid' => 4], ["AND" => ['taskid' => $VARS['taskid'], 'userid' => $user->getUID()]]);
                break;
            default:
                die('{"status": "ERROR", "msg": "Invalid status requested."}');
        }
        die('{"status": "OK", "msg": "Task updated."}');
    case "sendmsg":
        $msg = strip_tags($VARS['msg']);
        if (user_exists($VARS['to'])) {
            $to = User::byUsername($VARS['to'])->getUID();
        } else {
            die('{"status": "ERROR", "msg": "Invalid user."}');
        }
        if (is_empty($msg)) {
            die('{"status": "ERROR", "msg": "Missing message."}');
        }
        $database->insert('messages', ['messagetext' => $msg, 'messagedate' => date("Y-m-d H:i:s"), 'from' => $user->getUID(), 'to' => $to]);
        die('{"status": "OK"}');
    default:
        header("HTTP/1.1 400 Bad Request");
        die("\"400 Bad Request\"");
}
