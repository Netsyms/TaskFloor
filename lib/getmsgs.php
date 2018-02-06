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
        <div class="list-group-item">
            <div class="text-muted">
                <i class="fas fa-user fa-fw"></i>
                <span data-toggle="tooltip" title="<?php lang2("from user", ["user" => $usercache[$msg['from']]['username']]) ?>">
                    <?php echo $usercache[$msg['from']]['name']; ?>
                </span>
                <div class="float-right float-md-none d-inline">
                    <i class="fas fa-caret-right fa-fw"></i>
                    <span data-toggle="tooltip" title="<?php lang2("to user", ["user" => $to['username']]) ?>">
                        <?php echo $to['name']; ?>
                    </span>
                </div>
            </div>
            <p class="m-2"><?php echo strip_tags($msg['text']); ?></p>
            <div class="text-muted">
                <i class='fas fa-clock'></i> <?php
                $msgdate = strtotime($msg['date']);
                $format = "M j, g:i a";
                if ($msgdate < strtotime("-12 months")) {
                    $format = "M j Y, g:i a";
                }
                echo date($format, $msgdate);
                ?>
            </div>
        </div>
        <?php
    }
} else {
    echo "<div class='alert alert-info'><i class='far fa-comment-alt'></i> " . lang("no messages", false) . "</div>";
}
?>