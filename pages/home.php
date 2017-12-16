<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-th-list"></i> <?php lang("my tasks") ?>
                </h3>
            </div>
            <div class="panel-body">
                <?php
                include __DIR__ . '/mytasks.php';
                ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-comments"></i> <?php lang("messages") ?>
                </h3>
            </div>
            <div class="panel-body">
                <?php
                include __DIR__ . '/messages.php';
                ?>
            </div>
        </div>
    </div>
</div>