$(document).ready(function () {
    //Dissappear the alter message
    $("#alert-message").fadeTo(1000, 500).slideUp(500, function () {
        $("#alert-message").alert('close');
    });
});