<?php

class Players_model extends CI_Model {

    public $id;
    public $firstName;
    public $lastName;
    public $active;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        //$this->db = new MongoClient();
    }

    public function index() {
        echo "PLAYERS INDEX";
    }

    // get all active/inactive players; returns assoc array with player id as index
    /*public function get_players($active=1) {

        $collection = $this->db->dcbball->players;
        $filter = array(
            'active'=>$active
        );
        $cursor = $collection->find($filter);
        $results = array();

        foreach ( $cursor as $player ) {
            $results[$player['_id']] = $player['name'];
        }
        asort($results);
        return $results;
    }*/

    public function get_players($active=1) {

        $searchTerm = '';
        if(isset($_GET['term'])) {
            $searchTerm = $_GET['term'];
        }

        /*$query = $db->query("SELECT firstName, lastName
                              FROM players
                              WHERE (firstName LIKE '%".$searchTerm."%' OR lastName LIKE '%".$searchTerm."%') AND active=$active");
        while ($row = $query->fetch_assoc()) {
            $data[] = $row['firstName'] . " " . $row['lastName'];
        }*/
        $this->db->select('*');
        $this->db->from('players');
        $this->db->where('active',$active);
        #$this->db->order_by('id', 'DESC');
        return $this->db->get()->result_array();

//        return $data;
    }
}

?>