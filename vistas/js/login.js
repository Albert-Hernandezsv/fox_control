$('.miDiv').hide();  // Muestra el div
$(document).ready(function() {
    $('#toggleBtn').click(function() {
        $('.miDiv').fadeIn();  // Muestra el div
        $(this).hide();
    });
});