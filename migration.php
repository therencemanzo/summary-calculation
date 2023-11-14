<?php
require_once(dirname(__FILE__) ."../config.php");


// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


$sql = "CREATE DATABASE ".DB_NAME;

if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
    echo "<br>";
}

$conn->select_db(DB_NAME);

$sql = "CREATE TABLE users (
        id int NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        password varchar(255) NOT NULL,
        salt varchar(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";

if ($conn->query($sql) === TRUE) {
    echo "table user created successfully";
    echo "<br>";
}

$sql = "CREATE TABLE csvfile (
    id int NOT NULL AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    original_filename varchar(255) NOT NULL,
    filename varchar(255) NOT NULL,
    summary varchar(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);";

if ($conn->query($sql) === TRUE) {
    echo "table csvfile created successfully";
    echo "<br>";
}

$sql = "CREATE TABLE csvfiledata (
    id int NOT NULL AUTO_INCREMENT,
    file_id int(11) NOT NULL,
    description varchar(255) NOT NULL,
    quantity int(11) NOT NULL,
    amount float NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);";

if ($conn->query($sql) === TRUE) {
    echo "table csvfiledata created successfully";
    echo "<br>";
}
