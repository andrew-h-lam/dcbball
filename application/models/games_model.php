<?php

class Games_model extends CI_Model {
    public $id;
    public $gameDate;
    public $winScore;
    public $lossScore;

    // FixMe: create function to generate Mongo where clauses
    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $this->db = new MongoClient();
        $this->collection = $this->db->dcbball->games;

    }

    // get all games for a given year for game log; returns assoc array with game id as index
    public function get_games($year = null) {

        if($year == null) {
            $year = date("Y");
        }
        $date_regex = new MongoRegex("/^$year/i");

        $cursor = $this->collection->find(array('date' => $date_regex));
        $cursor->sort(array('_id'=>1));
        $results = array();
        foreach ( $cursor as $id => $value ) {
            $results[$id] = $value;
        }

        return $results;
    }

    // get all game info for a particular player; returns assoc array with game id as index
    public function get_player_games($name, $year = null) {

        if($year == null) {
            $year = date("Y");
        }

        $date_regex = new MongoRegex("/^$year/i");

        $filter = $this->filter_by_name_and_year($name, $date_regex);

        $cursor = $this->collection->find($filter);
        $cursor->sort(array('_id'=>1));
        $results = array();
        foreach ( $cursor as $id => $value ) {
            $results[$id] = $value;
        }
        return $results;
    }

    // FixMe: change to handle 3, 4, or 5 names
    // calculate win/loss record for lineups
    public function get_lineup_wins_losses($names, $year = null) {

        if($year == null) {
            $year = date("Y");
        }
        $date_regex = new MongoRegex("/^$year/i");

        $names1 = array('winners' => $names[0]);
        $names2 = array('winners' => $names[1]);
        $names3 = array('winners' => $names[2]);

        $filter = array('$and' =>
            [
                $names1, $names2, $names3
            ]
        );
        $wins = $this->collection->count($filter);

        $names1 = array('losers' => $names[0]);
        $names2 = array('losers' => $names[1]);
        $names3 = array('losers' => $names[2]);

        $filter = array('$and' =>
            [
                $names1, $names2, $names3
            ]
        );
        $losses = $this->collection->count($filter);

        $arr["wins"] = $wins;
        $arr["losses"] = $losses;
        return $arr;
    }

    // FixMe: change to handle 3 or 4 names
    // calculate plus/minus for lineups
    public function get_plus_minus($names) {

        $names1 = array('winners' => $names[0]);
        $names2 = array('winners' => $names[1]);
        $names3 = array('winners' => $names[2]);

        $pipeline = array(
            array(
                '$match' => array('$and' =>
                    [
                        $names1, $names2, $names3
                    ]
                )
            ),
            array(
                '$group' => array(
                    '_id' => null,
                    'plus' => array('$sum' => '$winScore'),
                    'minus' => array('$sum' => '$lossScore'),
                ),
            ),
        );

        $wins = $this->collection->aggregate($pipeline);

        $names1 = array('losers' => $names[0]);
        $names2 = array('losers' => $names[1]);
        $names3 = array('losers' => $names[2]);

        $pipeline = array(
            array(
                '$match' => array('$and' =>
                    [
                        $names1, $names2, $names3
                    ]
                )
            ),
            array(
                '$group' => array(
                    '_id' => null,
                    'plus' => array('$sum' => '$winScore'),
                    'minus' => array('$sum' => '$lossScore'),
                ),
            ),
        );

        $losses = $this->collection->aggregate($pipeline);
        //FixMe?
        if(!isset($wins['result'][0]['plus'])) $wins['result'][0]['plus'] = 0;
        if(!isset($wins['result'][0]['minus'])) $wins['result'][0]['minus'] = 0;
        if(!isset($losses['result'][0]['plus'])) $losses['result'][0]['plus'] = 0;
        if(!isset($losses['result'][0]['minus'])) $losses['result'][0]['minus'] = 0;

        $plus_minus = ($wins['result'][0]['plus'] - $wins['result'][0]['minus']) +
                      ($losses['result'][0]['minus'] - $losses['result'][0]['plus']);

        return $plus_minus;
    }

    // get record for last ten games
    public function get_last_ten($name) {

        $year = date("Y");
        $date_regex = new MongoRegex("/^$year/i");

        $filter = $this->filter_by_name_and_year($name,$date_regex);

        $cursor = $this->collection->find($filter);
        $cursor->limit(10);
        $cursor->sort(array('_id'=>-1));
        $wins = 0;
        $losses = 0;
        foreach ($cursor as $id => $value ) {
            if(in_array($name, $value['winners'])) $wins++;
            else if(in_array($name, $value['losers'])) $losses++;
        }
        return $wins . "-" . $losses;
    }

    private function insert_game($data) {

        $collection = $this->db->mytest->test_collection;

        $game = array();

        $cursor = $this->collection->find();
        $cursor->limit(1);
        $cursor->sort(array('_id'=>-1));

        foreach ($cursor as $id => $value) {
            $max_game_id = $id;
        }

        foreach($data as $i => $v) {
            $game[$i] = $v;
        }
        $game["_id"] =$max_game_id + 1;
        //echo "<pre>" . print_r($game,true) . "</pre>";

        //$collection->insert($game);
        // insert into mysql as well for sanity check
    }

    private function filter_by_name_and_year($name, $date) {

        $filter =
            array(
                '$or' =>
                    array(
                        array('winners' => $name),
                        array('losers' => $name)
                    ),
                '$and' =>
                    array(
                        array('date' => $date)
                    )
            );

        return $filter;
    }
}

?>