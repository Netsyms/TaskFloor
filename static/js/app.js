function setupTooltips() {
    $('[data-toggle="tooltip"]').tooltip({
        trigger: "click hover focus"
    });
}

$(document).ready(function () {
    /* Fade out alerts */
    $(".alert .close").click(function (e) {
        $(this).parent().fadeOut('slow');
    });

    /* Activate tooltips */
    setupTooltips();
});