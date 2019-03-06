<?php

//script default settings
$userFilePath = "../users.csv";

//database default settings
$host = "localhost";
$username = "root";
$password = "root_password";
$dbName = "php-task";
$tableName = "users";

$con = mysqli_connect($host, $username, $password, $dbName);

/*
//test connection
if(!$con) {
	echo "Connection failed: ".mysqli_connect_error();
} else {
	echo "Connected";
}
*/

$file = fopen($userFilePath, "r");

if($file) {
	fgetcsv($file); //read the first line but do nothing with it

	while(!feof($file)) {
		$user = fgetcsv($file);
		if($user != array(null)) {
			print_r($user);
		}
	}
	
	fclose($file);
}

mysqli_close($con);