<?php

// generate 3-man lineups;
class Lineups extends CI_Controller {

    function index() {

        if(isset($_POST['year'])) $year = $_POST['year'];
        else $year = date("Y");

        $this->load->helper("misc");
        $this->load->model('games_model');
        $this->load->model('players_model');
        $this->load->library('table');

        $tmpl = array ( 'table_open'  => '<table border="1" cellpadding="2" cellspacing="2" id="lineups" class="lineups">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Team', 'Wins', 'Losses', 'GP', '%', "P/M", "P/M <br> Per Game");

        $players = $this->players_model->get_players(1);
        sort($players);
        $len = sizeof($players);
        for($a=0;$a<$len;$a++) {
            for($b=$a+1; $b<$len; $b++) {
                for($c=$b+1; $c<$len; $c++) {

                    $record = $this->games_model->get_lineup_wins_losses(array($players[$a],$players[$b],$players[$c]), $year);
                    $games_played = $record['wins'] + $record['losses'];

                    if($games_played >=5) {
                        $percentage = $record['wins'] / $games_played;
                        $pm = $this->games_model->get_plus_minus(array($players[$a], $players[$b], $players[$c]));
                        $pm_per_game = number_format(round($pm / $games_played,3),3);
                        if($pm_per_game > 0) $color = 'green';
                        else if($pm_per_game < 0) $color = 'red';
                        else $color = 'black';
                        $pm_per_game = "<font color='$color'>" . $pm_per_game . "</font>";
                        $arr[$players[$a] . "-" . $players[$b] . "-" . $players[$c]] = $pm . " " . $games_played . " ";
                        $this->table->add_row($players[$a] . "-" . $players[$b] . "-" . $players[$c], $record['wins'], $record['losses'], 
                            $games_played, number_format(round($percentage,3),3), $pm, $pm_per_game);
                    }
                }
            }
        }

        $data['table'] = year_dropdown($year);
        $data['table'] .= $this->table->generate();
        $data['title'] = "3-Man Lineups";
        $this->load->view('home', $data);
        //$this->load->view('lineups', $data);
    }

}

?>