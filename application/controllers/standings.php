<?php

// determine player standings
class Standings extends CI_Controller {

    public function index() {

        $this->load->model('games_model');
        $this->load->model('players_model');
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

        $players = $this->players_model->get_players(1);
        $this->load->library('table');

        $tmpl = array ( 'table_open'  => '<table border="1" cellpadding="2" cellspacing="2" class="standings">',
                        'row_start'  => '<tr class="standings_player">',
                        'row_end'    => '</tr>',
                        'row_alt_start'       => '<tr class="standings_player">',
                        'row_alt_end'         => '</tr>'
        );
        $this->table->set_template($tmpl); 
        $this->table->set_heading('Name', 'Wins', 'Losses', 'GP', '%', "P/M", "P/M <br> Per Game",
            "Last <br> 10", "Current <br> Streak","Longest <br> Win Streak", "Longest <br> Lose Streak", "Last <br> Played");
        sort($players);
        // go through player list and determine stats
        foreach($players as $player) {

            $name = $player['firstName'] . " " . $player['lastName'];
            $games = $this->games_model->get_player_games($year, $player['id']);
            $this->load->helper('misc');
            // initialize variables
            $this->wins = 0;
            $this->losses = 0;
            $this->pm = 0;
            $this->prev_week_pm = 0;
            $this->l10 = "0-0";
            $this->process_game_info($games, $name);

            $wins = $this->wins;
            $losses = $this->losses;
            $pm = $this->pm;
            $last_played = $this->last_played;
            $l10 = $this->l10;

            $games_played = $wins + $losses;
            if($games_played == 0) continue;
            $percentage = $wins / $games_played;
            $pm_per_game = number_format(round($pm / $games_played,3),3);
            if($pm_per_game > 0) $color = 'green';
            else if($pm_per_game < 0) $color = 'red';
            else $color = 'black';
            $pm_per_game = "<font color='$color'>" . $pm_per_game . "</font>";

            $currentStreak= $this->currentStreak;
            $longestWinStreak = $this->longestWinStreak;
            $longestLoseStreak = $this->longestLoseStreak;

            //$playerInfoLink = "<a href='players/show/$id'>" . $name . "</a>";
            $this->table->add_row($name, $wins, $losses, $games_played, number_format(round($percentage,3),3), $pm,
                $pm_per_game, $l10, $currentStreak,$longestWinStreak, $longestLoseStreak, $last_played);
        }

        $data['table'] = year_dropdown($year);
        $data['table'] .= $this->table->generate();
        $data['title'] = "Standings";
        $this->load->view('home', $data);
        //$this->load->view('standings', $data);
    }

    // determines wins, losses, plus/minus, streaks, and last played
    private function process_game_info($games, $name) {
        $currentResult = "";
        $currentWinStreak = 0;
        $longestWinStreak = 0;
        $currentLoseStreak = 0;
        $longestLoseStreak = 0;
        $num_games = sizeof($games);
        $l10_w = 0;
        $l10_l = 0;
        // for each player's set of games, calculate their stats
        foreach ($games as $id => $game) {


            $this->last_played = $game['gameDate'];
            if($game['result' ] == "W") {

                $this->wins++;
                $this->pm += $game['winScore'] - $game['lossScore'];
                $currentWinStreak++;
                if($currentResult == "L" || $currentResult == "") $currentLoseStreak = 0;
                if($currentWinStreak > $longestWinStreak ) $longestWinStreak = $currentWinStreak;
                $currentResult = "W";
                if($num_games <= 10) $l10_w++;
                else $num_games--;

            }
            else if($game['result' ] == "L") {

                $this->losses++;
                $this->pm -= $game['winScore'] - $game['lossScore'];
                $currentLoseStreak++;
                if($currentResult == "W" || $currentResult == "") $currentWinStreak = 0;
                if($currentLoseStreak > $longestLoseStreak ) $longestLoseStreak = $currentLoseStreak;
                $currentResult = "L";
                if($num_games <= 10) $l10_l++;
                else $num_games--;
            }
        }

        $this->l10 = $l10_w . "-" . $l10_l;
        $this->longestWinStreak = $longestWinStreak;
        $this->longestLoseStreak = $longestLoseStreak;
        if($currentResult == "W") $this->currentStreak = $currentResult . $currentWinStreak;
        else if($currentResult == "L") $this->currentStreak = $currentResult . $currentLoseStreak;
    }
}

?>