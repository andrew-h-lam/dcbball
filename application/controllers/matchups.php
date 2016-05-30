<?php

// generate series
class Matchups extends CI_Controller {

    function index() {

        $this->load->model('games_model');
        $this->load->model('players_model');
        $this->load->library('table');
        $this->load->helper("misc");
        $this->load->library('session');

        if(isset($_POST['year'])) {
            $year = $_POST['year'];
        }
        else if(isset($this->session->userdata['year'])) {
            $year = $this->session->userdata['year'];
        }
        else {
            $year = date("Y");
        }

        $this->session->set_userdata('year', $year);

        $tmpl = array ( 'table_open'  => '<table border="1" cellpadding="2" cellspacing="2" class="matchups">',
            'row_start'  => '<tr class="standings_player">',
            'row_end'    => '</tr>',
            'row_alt_start'       => '<tr class="matchups">',
            'row_alt_end'         => '</tr>'
        );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Team 1', 'Record', 'Team 2');

        $games = $this->games_model->get_games($year);

        // get list of teams that played together
        $teams_set1 = array();;
        foreach ($games as $i => $v) {
            sort($v['winner_ids']);
            sort($v['loser_ids']);
            if(!in_array($v['winner_ids'], $teams_set1)) $teams_set1[] = $v['winner_ids'];
            if(!in_array($v['loser_ids'], $teams_set1)) $teams_set1[] = $v['loser_ids'];
        }
        $teams_set2 = $teams_set1;

        $team1_size = sizeof($teams_set1);
        $team2_size = sizeof($teams_set2);
        for($t1=0; $t1<$team1_size; $t1++) {

            for($t2=$t1; $t2<$team2_size; $t2++) {

                $wins = 0;
                $losses = 0;
                $team1 = array();
                $team2 = array();
                foreach ($games as $i => $v) {
                    sort($v['winner_ids']);
                    sort($v['loser_ids']);

                    if ($teams_set1[$t1] == $v['winner_ids'] && $teams_set2[$t2] == $v['loser_ids']) {
                        $team1 = $v['winners'];
                        $wins++;
                        if($wins > 1) $team2 = $v['losers'];
                    }
                    if ($teams_set1[$t1] == $v['loser_ids'] && $teams_set2[$t2] == $v['winner_ids']) {
                        $team2 = $v['winners'];
                        $losses++;
                        if($losses > 1) $team1 = $v['winners'];
                    }
                }
                if($wins > 1 || $losses > 1 ) {
                    $this->table->add_row(implode("<BR>", $team1), $wins . " - " . $losses, implode("<BR>", $team2));
                }
            }
        }
        $data['table'] = year_dropdown($year);
        $data['table'] .= $this->table->generate();
        $data['title'] = "Matchups";
        $this->load->view('home', $data);
    }
}

?>