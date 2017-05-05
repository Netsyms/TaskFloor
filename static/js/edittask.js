tinymce.init({selector: 'textarea'});

function clearpretty() {
    setTimeout(function () {
        $('#savebtn').prop('disabled', false);
        $('#savebtn').html('<i class="fa fa-floppy-o"></i> Save Task');
    }, 2000);
}

function prettysave() {
    $('#savebtn').prop('disabled', true);
    $('#savebtn').html('<i class="fa fa-check"></i> Task Saved!');
    clearpretty();
}

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
        data.q = $("#assigned-to-box").val();
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

$("#assigned-to-box").easyAutocomplete(options);