<?php

// FixMe: loop; dynamic
function year_dropdown($year) {
    $selected_2014 ='';
    $selected_2015 = '';
    $selected_2016 = '';

    if($year == 2014) $selected_2014 ='selected';
    else if($year == 2015) $selected_2015 ='selected';
    else if($year == 2016) $selected_2016 ='selected';

    return "<form method=POST id='yearform'>
                            <select name='year' id='year'>
                                    <option value=2014 $selected_2014>2014</option>
                                    <option value=2015 $selected_2015>2015</option>
                                    <option value=2016 $selected_2016>2016</option>
                            </select></form>";
}

?>