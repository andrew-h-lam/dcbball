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

    // FixMe: Make dynamic
    /*$("#winners_1").on("click", function() {
        var selectedValue = this.value;
        if (!this.checked) {
            $("#losers_1").removeAttr("disabled");
        } else {
            $("#losers_1").attr("disabled", true);
        }
    });*/

});


