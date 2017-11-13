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