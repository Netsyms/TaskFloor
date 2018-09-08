<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

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
            $usercache[$msg['from']] = new User($msg['from']);
        }
        if (is_null($msg['to'])) {
            $to['name'] = $Strings->get("all users", false);
            $to['username'] = $Strings->get("all users", false);
        } else {
            if (!isset($usercache[$msg['to']])) {
                $usercache[$msg['to']] = new User($msg['to']);
            }
            $to = $usercache[$msg['to']];
        }
        ?>
        <div class="list-group-item">
            <div class="text-muted">
                <i class="fas fa-user fa-fw"></i>
                <span data-toggle="tooltip" title="<?php $Strings->build("from user", ["user" => $usercache[$msg['from']]->getUsername()]) ?>">
                    <?php echo $usercache[$msg['from']]->getName(); ?>
                </span>
                <div class="float-right float-md-none d-inline">
                    <i class="fas fa-caret-right fa-fw"></i>
                    <span data-toggle="tooltip" title="<?php $Strings->build("to user", ["user" => $to->getUsername()]) ?>">
                        <?php echo $to->getName(); ?>
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
    echo "<div class='alert alert-info'><i class='far fa-comment-alt'></i> " . $Strings->get("no messages", false) . "</div>";
}
?>