<?php

require_once('db.php');

class FileData extends db{
    public function __construct(){
        parent::__construct('csvfiledata');
    }
}