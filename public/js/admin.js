
$(document).ready(function() {
    $('#nav ul.tab').hover(
        function() {
            active= $('#nav ul.tab.active');
            $('#nav ul.tab').removeClass('active');
            $(this).addClass('active');
            $(this).find('.sub').addClass('active');

        }, function() {
            $('#nav ul.tab').removeClass('active');
            $(active).addClass('active');
        }
    );
});