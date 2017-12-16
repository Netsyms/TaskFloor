/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

$('textarea').summernote({
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['misc', ['undo', 'redo', 'fullscreeen']],
        ['para', ['ul', 'ol', 'paragraph']],
    ]
});

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

$(function () {
    $('#assigned-on-box').datetimepicker({
        format: "ddd MMMM D YYYY h:mm a"
    });
    $('#due-by-box').datetimepicker({
        format: "ddd MMMM D YYYY h:mm a"/*"YYYY-M-DTH:m"*/,
        useCurrent: false
    });
});