<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<form action="action.php" method="POST" class="form-horizontal" id="msgsendform">
    <input type="hidden" name="action" value="sendmsg" />
    <div class="form-group">
        <div class="col-xs-12 col-sm-6 mgn-btm-5px">
            <input type="text" id="msgsendbox" name="msg" class="form-control" placeholder="<?php lang("send message") ?>" autocomplete="off" />
        </div>
        <div class="col-xs-9 col-sm-4 padright-0px">
            <input type="text" id="msgtobox" name="to" class="form-control" placeholder="<?php lang("to") ?>" autocomplete="off" />
        </div>
        <button id="msgsendbtn" class="btn btn-primary col-xs-3 col-sm-2" type="submit"><i class="fa fa-paper-plane"></i> <?php lang("send") ?></button>
    </div>
</form>
<div<?php
if ($pageid != "messages") {
    echo ' class="home-list-container"';
}
?>>
    <div id="messagedispdiv">
        <?php
        include __DIR__ . '/../lib/getmsgs.php';
        ?>
    </div>
</div>