<?php

class Players_model extends CI_Model {

    public $id;
    public $firstName;
    public $lastName;
    public $active;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
        echo "PLAYERS INDEX";
    }

    public function get_players($active=1) {

        $this->db->select('*');
        $this->db->from('players');
        $this->db->where('active',$active);
        #$this->db->order_by('id', 'DESC');
        return $this->db->get()->result_array();
    }
}

?>