var options = {
    url: "action.php",
    ajaxSettings: {
        dataType: "json",
        method: "GET",
        data: {
            action: "autocomplete"
        }
    },
    preparePostData: function (data) {
        data.q = $("#msgtobox").val();
        return data;
    },
    getValue: function (element) {
        return element.username;
    },
    template: {
        type: "custom",
        method: function (value, item) {
            return item.name + " <i class=\"small\">" + item.username + "</i>";
        }
    }
};

$("#msgtobox").easyAutocomplete(options);

function refreshMsgs() {
    $.get('lib/getmsgs.php', function (data) {
        $('#messagedispdiv').html(data);
        setupTooltips();
    });
}

setInterval(function () {
    refreshMsgs();
}, 10 * 1000);

$(".msgdelform").on("submit", function () {
    var msgid = $(this).data("msgid");
    $('#delmsgbtn' + msgid).prop('disabled', true);
    setTimeout(function () {
        refreshMsgs();
    }, 100);
});

$("#msgsendform").on("submit", function () {
    setTimeout(function () {
        $('#msgsendbox').val('');
        $('#msgtobox').val('');
        refreshMsgs();
    }, 100);
});