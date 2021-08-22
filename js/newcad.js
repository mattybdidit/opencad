var href = window.location.href;
var hdir = href.substring(0, href.lastIndexOf('/')) + "/";

getCallsNew();

function getCallsNew() {
    $.ajax({
        type: "GET",
        url: hdir + "actions/new.php",
        data: {
            getCalls: 'yes'
        },
        success: function (response) {
            $('ul.calls').html(response);
            setTimeout(getCallsNew, 5000);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            M.toast({ html: 'An error has occured while getting calls.', classes: 'rounded red darken-3' })
        }

    });
}