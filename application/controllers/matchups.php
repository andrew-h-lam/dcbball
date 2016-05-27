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
            $year = data("Y");
        }

        $this->session->set_userdata('year', $year);

        $games = $this->games_model->get_games($year);

        // get list of teams that played together
        $teams_set1 = array();
        $teams_set2 = array();
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
                    }
                    if ($teams_set1[$t1] == $v['loser_ids'] && $teams_set2[$t2] == $v['winner_ids']) {
                        $team2 = $v['winners'];
                        $losses++;
                    }
                }
                if($wins != 0 && $losses != 0 ) {
                    echo "START SERIES<BR>";
                    echoPre($team1);
                    echoPre($team2);
                    echo $wins . " - " . $losses . "<br>";
                    echo "END SERIES<BR>";

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