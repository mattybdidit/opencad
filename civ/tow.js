var href = window.location.href;
var hdir = href.substring(0, href.lastIndexOf('/')) + "/";

console.log(href)
console.log(hdir)

function searchPlate() {
    $plate = $("input[name=licensePlate]").val()
    $.ajax({
        type: "POST",
        url: hdir + "../actions/new.php",
        data: {
            action: 'searchPlate',
            licensePlate: $plate
        },
        success: function (response) {
            $('div.plateSearchReturn').html(response);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            M.toast({ html: 'An error occured: ' + errorThrown, classes: 'rounded red darken-3' })
        }

    });
    $("input[name=licensePlate]").val('')
}