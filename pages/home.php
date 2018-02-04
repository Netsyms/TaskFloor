<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-12 col-md-6 p-2">
        <div class="card">
            <div class="card-body p-1">
                <h4 class="card-title p-3">
                    <i class="fas fa-th-list"></i> <?php lang("my tasks") ?>
                </h4>
                <?php
                include __DIR__ . '/mytasks.php';
                ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 p-2">
        <div class="card">
            <div class="card-body p-1">
                <h4 class="card-title p-3">
                    <i class="fas fa-comments"></i> <?php lang("messages") ?>
                </h4>
                <?php
                include __DIR__ . '/messages.php';
                ?>
            </div>
        </div>
    </div>
</div>