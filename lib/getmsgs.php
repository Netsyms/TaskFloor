<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

require_once __DIR__ . "/userinfo.php";

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
            // TODO: fix to => null
            "to" => $_SESSION['uid'],
            "from" => $_SESSION['uid']
        ],
        "deleted" => 0
    ],
    "ORDER" => [
        "messagedate" => "DESC"
    ],
    "LIMIT" => "50"]
);

$usercache = [];

if (count($messages) > 0) {
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
        ?>
        <div class="panel panel-default">
            <div class="panel-body message-content">
                <?php echo strip_tags($msg['text']); ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <i class="fa fa-user fa-fw"></i> <span data-toggle="tooltip" title="<?php lang2("from user", ["user" => $usercache[$msg['from']]['username']]) ?>"><?php echo $usercache[$msg['from']]['name']; ?></span> &nbsp;<i class="fa fa-caret-right fa-fw"></i> <span data-toggle="tooltip" title="<?php lang2("to user", ["user" => $to['username']]) ?>"><?php echo $to['name']; ?></span> 
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 text-right">
                        <i class='fa fa-clock-o'></i> <?php echo date("F j, Y, g:i a", strtotime($msg['date'])) ?> 
                        <form class="msgdelform"
                              data-msgid="<?php echo $msg['id']; ?>"
                              action="action.php" method="GET">
                            <input type="hidden" name="msgid" value="<?php echo $msg['id']; ?>" />
                            <input type="hidden" name="action" value="delmsg" />
                            <button type="submit" id="delmsgbtn<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="auto left" title="<?php lang("delete message") ?>"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo "<div class='alert alert-info'><i class='fa fa-commenting-o'></i> " . lang("no messages", false) . "</div>";
}
?>