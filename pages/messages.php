<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<form action="action.php" method="POST" class="form-horizontal mb-1" id="msgsendform">
    <input type="hidden" name="action" value="sendmsg" />
    <div class="input-group" id="msgsenddiv"> <!--col-12 col-md-5 col-lg-4-->
        <input type="text" id="msgsendbox" name="msg" class="form-control" placeholder="<?php lang("send message") ?>" autocomplete="off" />
        <input type="text" id="msgtobox" name="to" class="form-control" placeholder="<?php lang("to") ?>" autocomplete="off" />
        <div class="input-group-append">
            <button id="msgsendbtn" class="btn btn-primary btn-sm" type="submit"><i class="fas fa-paper-plane"></i> <?php lang("send") ?></button>
        </div>
    </div>
</form>
<div>
    <div id="messagedispdiv">
        <?php
        include __DIR__ . '/../lib/getmsgs.php';
        ?>
    </div>
</div>