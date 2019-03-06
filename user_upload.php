#!/usr/bin/env php
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
	fgetcsv($file); //read the first line and do nothing with it

	while(!feof($file)) {
		$user = fgetcsv($file);
		if($user != array(null)) {
			//print_r($user);
			
			$user[2] = trim($user[2]);
			
			if(filter_var($user[2], FILTER_VALIDATE_EMAIL)) { //validate email
				//make first letter uppercase and add blackslashes to special character and trim
				$user[0] = addslashes(ucfirst(strtolower(trim($user[0]))));
				$user[1] = addslashes(ucfirst(strtolower(trim($user[1]))));
				$user[2] = addslashes($user[2]);
				
				$insertQuery = "INSERT INTO $tableName (name, surname, email) VALUES ('{$user[0]}', '{$user[1]}', '{$user[2]}')";
				if(!mysqli_query($con, $insertQuery)) {
					echo "Error message: ".mysqli_error($con)."\n";
				}
			} else {
				echo "Error message: Your email {$user[2]} is not valid\n";
			}
		}
	}
	
	fclose($file);
}

mysqli_close($con);