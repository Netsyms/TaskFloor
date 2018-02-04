<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "home",
        "navbar" => true,
        "icon" => "fas fa-home",
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
        "icon" => "fas fa-th-list",
        "scripts" => [
            "static/js/tasks.js"
        ]
    ],
    "taskman" => [
        "title" => "task manager",
        "navbar" => true,
        "icon" => "fas fa-tasks",
        "scripts" => [
            "static/js/taskman.js"
        ]
    ],
    "messages" => [
        "title" => "messages",
        "navbar" => true,
        "icon" => "fas fa-comments",
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
        "icon" => "fas fa-pencil",
        "styles" => [
            "static/css/easy-autocomplete.min.css",
            "static/css/tempusdominus-bootstrap-4.min.css",
            "static/css/summernote-lite.css"
        ],
        "scripts" => [
            "static/js/summernote-lite.js",
            "static/js/moment.min.js",
            "static/js/tempusdominus-bootstrap-4.min.js",
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
