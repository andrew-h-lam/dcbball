<?php //echo validation_errors(); ?>
<form action="gameform" method="post">

<h4>Game Date</h4>
<input type="text" name="gameDate" value="" size="20" />

<h4>Winner Score</h4>
<input type="text" name="winScore" value="" size="20" />

<h4>Loser Score</h4>
<input type="text" name="lossScore" value="" size="20" />


<h4>Winning Team</h4>

    <?php
    // FixMe: move to function
    $len = sizeof($players);
    echo "<div id=winners_form>";
    echo "<table border=1><tr>";
    for($i=0; $i<$len; $i++) {
        echo "<td><label for='players'></label><input type=checkbox name=winners[] id=winners_$i value=" . $players[$i]['id'] . ">" . $players[$i]['firstName'] . " " . $players[$i]['lastName'] . "</td>";
        if(($i+2) % 4 == 1) echo "</tr><tr>";

    }
    echo "</tr></table>";
    echo "</div>";
    ?>

<h4>Losing Team</h4>
    <?php
    $len = sizeof($players);
    echo "<div id=losers_form>";
    echo "<table border=1><tr>";
    for($i=0; $i<$len; $i++) {
        echo "<td><label for='players'></label><input type=checkbox name=losers[] id=losers_$i value=" . $players[$i]['id'] . ">" . $players[$i]['firstName'] . " " . $players[$i]['lastName'] . "</td>";
        if(($i+2) % 4 == 1) echo "</tr><tr>";
    }
    echo "</tr></table>";
    echo "</div>";
    ?>

<div><br><input type="submit" value="Submit" /></div>
</form>
