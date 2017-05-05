<h2 class="page-header">
    <?php lang("messages") ?>
</h2>
<form action="action.php" method="POST" onsubmit="setTimeout(function () {
            $('#msgsendbox').val('');
            $('#msgtobox').val('');
            refreshMsgs();
        }, 100);" class="form-horizontal">
    <input type="hidden" name="action" value="sendmsg" />
    <div class="form-group">
        <div class="col-xs-12 col-sm-6" style="margin-bottom: 5px;">
            <input type="text" id="msgsendbox" name="msg" class="form-control" placeholder="<?php lang("send message") ?>" autocomplete="off" />
        </div>
        <div class="col-xs-9 col-sm-4" style="padding-right: 0px;">
            <input type="text" id="msgtobox" name="to" class="form-control" placeholder="<?php lang("to") ?>" autocomplete="off" />
        </div>
        <button id="msgsendbtn" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px; margin-left: 0px;" class="btn btn-primary col-xs-3 col-sm-2" type="submit"><i class="fa fa-paper-plane"></i> <?php lang("send") ?></button>
    </div>
</form>
<div style="<?php
if ($pageid != "messages") {
    echo "max-height: 600px; overflow-y: auto; padding: 5px;";
}
?>">
    <div id="messagedispdiv">
        <?php
        include __DIR__ . '/../lib/getmsgs.php';
        ?>
    </div>
    <script>
        function refreshMsgs() {
            $.get('lib/getmsgs.php', function (data) {
                $('#messagedispdiv').html(data);
                setupTooltips();
            });
        }
        setInterval(function () {
            refreshMsgs();
        }, 10 * 1000);
    </script>
</div>