<?php

require_once(dirname(__FILE__) ."/../config.php");

class db{

    public $conn;
    public $table;

    function __construct($table = "") {
        try {

            $this->conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME."", DB_USER, DB_PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->table = $table;
            
        } catch(PDOException $e) {

            echo "Connection failed: " . $e->getMessage();

        }
    }

    public function insert($datas){

        $keys = array();
        $values = array();

        foreach (array_keys($datas) as $key) {
            $keys[] =":{$key}";
            $values [":{$key}"] = $datas[$key];
        }

        $key = implode(',', array_keys($datas));
        $value = implode( ",", array_values($keys));

        $query = "INSERT INTO $this->table ($key) VALUES ($value)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($values);

        return $this->conn->lastInsertId();
      
    }

    public function update($datas){

        $data_set = array();
        $values = array();

        foreach (array_keys($datas) as $key) {

            if($key != "id"){
                $data_set[] = $key . " = :{$key}";
                $values [":{$key}"] = $datas[$key];
            }
        }

        $value = rtrim(implode( ",", array_values($data_set)), ',');

        $query = "UPDATE $this->table SET $value WHERE id = :id";
        $values[':id'] = $datas['id'];
        $stmt = $this->conn->prepare($query);
        $stmt->execute($values);

        return $datas['id'];
    }

    public function select($select = array(), $params = array()){

        $query = "SELECT ";

        if(empty($select)){

            $query .= " * "; 

        }else{

            $query .= rtrim(implode( ",", array_values($select)), ',');

        }

        $query .= " FROM $this->table  ";

        if(!empty($params)){
            $query .= " WHERE ";

            $data_set = array();
            $values = array();

            foreach (array_keys($params) as $key) {
                $data_set[] = $key . " = :{$key}";
                $values [":{$key}"] = $params[$key];
            }

            $value = rtrim(implode( " AND ", array_values($data_set)), ' AND ');

            $query .= " $value ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($values); 
            return $stmt->fetch();

        }else{
            return false;
        }


    }

    public function fetchAll($select = array(), $params = array(), $orderBy= array(), $limit = 0, $skip = 0){
        $query = "SELECT ";

        if(empty($select)){

            $query .= " * "; 

        }else{

            $query .= rtrim(implode( ",", array_values($select)), ',');

        }

        $query .= " FROM $this->table  ";

        if(!empty($params)){
            $query .= " WHERE ";

            $data_set = array();
            $values = array();

            foreach (array_keys($params) as $key) {
                $data_set[] = $key . " = :{$key}";
                $values [":{$key}"] = $params[$key];
            }

            $value = rtrim(implode( " AND ", array_values($data_set)), ' AND ');

            $query .= " $value ";

        }

        if(!empty($orderBy)){
            $query .= " ORDER BY ". key($orderBy). " ".$orderBy[key($orderBy)] ;
        }

        if($limit !== 0){

            if($skip > 0){
                $query .=  " LIMIT $skip, $limit";
            }else{
                $query .=  " LIMIT $limit";
            }

        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($values); 
        return $stmt->fetchAll();
    }

}