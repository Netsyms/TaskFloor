/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

$("#tasksdispdiv").on("click", ".task-btn", function () {
    var taskid = $(this).data("taskid");
    var action = $(this).data("action");
    switch (action) {
        case "start":
            break;
        case "finish":
            break;
        case "pause":
            break;
        case "problem":
            break;
        case "resume":
            break;
        default:
            // Not a valid action code
            return;
    }
    $("#" + action + "taskbtn" + taskid).prop("disabled", true);
    refreshTasksSoon();
});