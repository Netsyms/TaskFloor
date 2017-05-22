<?php
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
            "to" => $_SESSION['uid'],
            "to" => null,
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
                    <div class="col-xs-12 col-sm-6 col-md-6" style="text-align: right;">
                        <i class='fa fa-clock-o'></i> <?php echo date("F j, Y, g:i a", strtotime($msg['date'])) ?> 
                        <form style="display: inline-block; margin-left: 5px;"
                              action="action.php" method="GET"
                              onsubmit="$('#delmsgbtn<?php echo $msg['id']; ?>').prop('disabled', true);setTimeout(refreshMsgs, 100);">
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