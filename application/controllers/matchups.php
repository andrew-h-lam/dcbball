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

        $data['table'] = year_dropdown($year);
        $data['table'] .= $this->table->generate();
        $data['title'] = "Matchups";
        $this->load->view('home', $data);
    }

}

?>