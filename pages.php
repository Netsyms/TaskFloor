<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "home",
        "navbar" => true,
        "icon" => "home",
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/messages.js",
            "static/js/tasks.js"
        ]
    ],
    "mytasks" => [
        "title" => "my tasks",
        "navbar" => true,
        "icon" => "th-list",
        "scripts" => [
            "static/js/tasks.js"
        ]
    ],
    "taskman" => [
        "title" => "task manager",
        "navbar" => true,
        "icon" => "tasks",
        "scripts" => [
            "static/js/taskman.js"
        ]
    ],
    "messages" => [
        "title" => "messages",
        "navbar" => true,
        "icon" => "comments",
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/messages.js"
        ]
    ],
    "edittask" => [
        "title" => "edit task",
        "navbar" => false,
        "icon" => "pencil",
        "styles" => [
            "static/css/bootstrap-datetimepicker.min.css",
            "static/css/easy-autocomplete.min.css",
            "static/css/summernote.css"
        ],
        "scripts" => [
            "static/js/summernote.min.js",
            "static/js/moment.min.js",
            "static/js/bootstrap-datetimepicker.min.js",
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/edittask.js"
        ]
    ],
    "404" => [
        "title" => "404 error",
        "navbar" => false,
        "icon" => "exclamation-triangle"
    ]
]);
