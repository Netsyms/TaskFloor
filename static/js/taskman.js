$('.deltaskform').on("submit", function () {
    $("#deltaskbtn" + $(this).data("taskid")).prop("disabled", true);
});