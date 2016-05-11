<?php

class Games extends CI_Controller {
    // Shows game log
    // FixMe: link to games to edit them
    // FixMe: hardcoded year
    function index() {

        $this->load->model('games_model');
        $data = array(
            'all_games' => $this->games_model->get_games(2016)
        );

        // move to view??
        $this->load->library('table');
        $tmpl = array ( 'table_open'  => '<table border="1" cellpadding="3" cellspacing="2" class="mytable">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Game#', 'Game Date','Score', 'Winning Team', 'Losing Team');

        foreach($data['all_games'] as $doc) {
           $this->table->add_row($doc["_id"],$doc["date"], $doc["winScore"]. "-" . $doc["lossScore"],
               implode("<BR>", $doc["winners"]),implode("<BR>",$doc["losers"]));
        }

        $data['table'] = $this->table->generate();
        $data['title'] = "Game Log";
        $this->load->view('home', $data);
    }

    function form() {

        if($_POST) {
            $data['table'] = 'Game Created';
            $this->load->model('games_model');
            $this->games_model->insert_game($_POST); // FixMe: pass $_POST?
        }
        else {
            $data['table'] = $this->load->view('game_form', '', true);
        }

        $data['title'] = "Add Game";
        $this->load->view('home',$data);
    }


    // FixMe
    // Create game
    /*function create() {
	    $this->load->helper(array('form', 'url'));

	    $this->load->library('form_validation');

	    if ($this->form_validation->run() == FALSE) {
	        $this->load->model('players_model');
         //   $data['all_players'] = $this->players_model->get_all_players();
            $data['table'] = [];
            $this->load->view('home',$data);
	    }
	    else {
	      //  $this->load->view('entergamesuccess');
	    }
    }*/
}
?>