$(document).ready(function() {

    var $myplayer = $(".standings_player").click(function(e) {
        e.preventDefault();
        $myplayer.removeClass("highlight");

        $(this).addClass("highlight");

    });

    $("#year").on("change", function() {
        var selectedValue = this.value;
        $("#yearform").submit();
    });

});


