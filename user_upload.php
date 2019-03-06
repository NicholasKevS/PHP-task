<?php

//database default settings
$host = "localhost";
$username = "root";
$password = "root_password";
$dbName = "php-task";
$tableName = "users";

$con = mysqli_connect($host, $username, $password, $dbName);

if(!$con) {
	die("Connection failed: ".mysqli_connect_error());
} else {
	echo "Connected";
}