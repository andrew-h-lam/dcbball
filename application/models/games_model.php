<?php

class Games_model extends CI_Model {
    public $id;
    public $gameDate;
    public $winScore;
    public $lossScore;

    // FixMe: create function to generate Mongo where clauses
    public function __construct() {
        parent::__construct();
        $this->load->database();
        #$this->db = new MongoClient();
        #$this->collection = $this->db->dcbball->games;

    }

    // get all games for a given year for game log; returns assoc array with game id as index
    public function get_games($year = null, $playerid = null) {

        if($year == null) {
            $year = date("Y");
        }

        $playerid_clause = '';
        if($playerid != null) {
            $playerid_clause = " and players.id = $playerid";
        }

        $this->db->select("lastName, firstName, games.id, gameDate, winScore, lossScore, result");
        $this->db->from("games join playerGame on gameID = games.id join players on players.id = playerID");
        $this->db->where("gameDate like '" . $year . "%' $playerid_clause");
        $this->db->order_by('games.id, result', 'DESC');

        $res = $this->db->get()->result_array();

        $res_size = sizeof($res);
        $results_arr = array();
        for($i=0; $i<$res_size; $i++) {

            $game_id = $res[$i]['id'];
            $results_arr[$game_id]['id'] = $game_id;
            $results_arr[$game_id]['gameDate'] = $res[$i]['gameDate'];
            $results_arr[$game_id]['winScore'] = $res[$i]['winScore'];
            $results_arr[$game_id]['lossScore'] = $res[$i]['lossScore'];
            $fullname =  $res[$i]['firstName'] . " " . $res[$i]['lastName'];
            if($res[$i]['result'] == "W") $results_arr[$game_id]["winners"][] = $fullname;
            else $results_arr[$game_id]["losers"][] = $fullname;

        }

        return $results_arr;
    }

    // get all games for a given year for game log; returns assoc array with game id as index
    public function get_player_games($year = null, $playerid = null) {

        if($year == null) {
            $year = date("Y");
        }

        $playerid_clause = '';
        if($playerid != null) {
            $playerid_clause = " and players.id = $playerid";
        }

        $this->db->select("lastName, firstName, games.id, gameDate, winScore, lossScore, result");
        $this->db->from("games join playerGame on gameID = games.id join players on players.id = playerID");
        $this->db->where("gameDate like '" . $year . "%' $playerid_clause");
        $this->db->order_by('games.id, result', 'DESC');

        $res = $this->db->get()->result_array();
        $res_size = sizeof($res);
        $results_arr = array();
        for($i=0; $i<$res_size; $i++) {

            $game_id = $res[$i]['id'];
            $results_arr[$game_id]['id'] = $game_id;
            $results_arr[$game_id]['gameDate'] = $res[$i]['gameDate'];
            $results_arr[$game_id]['winScore'] = $res[$i]['winScore'];
            $results_arr[$game_id]['lossScore'] = $res[$i]['lossScore'];
            $results_arr[$game_id]['result'] =  $res[$i]['result'];
        }

        return $results_arr;
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

        $filter = array(
                '$and' =>
                [
                $names1, $names2, $names3
                ],
                'date' => $date_regex
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

    public function insert_game($data) {

        if($data['gameDate'] && $data['winScore'] && $data['lossScore']) {
            $sql = "INSERT INTO games (gameDate, winScore, lossScore)
                    VALUES ('" . $data['gameDate'] . "','" . $data['winScore'] . "','" . $data['lossScore'] . "')";

            $this->db->query($sql);
            $game_id = $this->db->insert_id();

            foreach($data['winners'] as $i => $v) {
                $sql = "INSERT INTO playerGame (playerID, gameID, result)
                    VALUES ($v, $game_id, 'W')";
                $this->db->query($sql);
            }

            foreach($data['losers'] as $i => $v) {
                $sql = "INSERT INTO playerGame (playerID, gameID, result)
                    VALUES ($v, $game_id, 'L')";
                $this->db->query($sql);
            }
        }
    }


}

?>