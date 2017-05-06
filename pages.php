<?php

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
            "static/js/messages.js"
        ]
    ],
    "mytasks" => [
        "title" => "my tasks",
        "navbar" => true,
        "icon" => "th-list"
    ],
    "taskman" => [
        "title" => "task manager",
        "navbar" => true,
        "icon" => "tasks"
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
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/tinymce/tinymce.min.js",
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
