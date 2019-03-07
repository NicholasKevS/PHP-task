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
$makeTableQuery = "CREATE TABLE `$tableName` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(50) NOT NULL , `surname` VARCHAR(50) NOT NULL , `email` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`), UNIQUE `email_unique` (`email`))";

$con = mysqli_connect($host, $username, $password, $dbName);

//test connection
if(!$con) {
	echo "Connection failed: ".mysqli_connect_error();
} else {
	create_table(); //create table if not exists
	
	if(file_exists($userFilePath)) {
		$file = fopen($userFilePath, "r");
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
					
					$insertQuery = "INSERT INTO `$tableName` (`name`, `surname`, `email`) VALUES ('{$user[0]}', '{$user[1]}', '{$user[2]}')";
					if(!mysqli_query($con, $insertQuery)) {
						echo "Error message: ".mysqli_error($con)."\n";
					}
				} else {
					echo "Error message: Your email {$user[2]} is not valid\n";
				}
			}
		}
		
		echo "Finished inputting user data into database\n";
		fclose($file);
	} else {
		echo "Error message: File not found\n";
	}
}

mysqli_close($con);

function create_table() {
	//get global variable
	$con = $GLOBALS['con'];
	$tableName = $GLOBALS['tableName'];
	$makeTableQuery = $GLOBALS['makeTableQuery'];
	
	$checkTableQuery = "DESCRIBE `$tableName`";
	if(!mysqli_query($con, $checkTableQuery)) {
		echo "There is no users table\n";
		echo "Users table will be created\n";
	
		if(!mysqli_query($con, $makeTableQuery)) {
			echo "Error message: ".mysqli_error($con)."\n";
		} else {
			echo "Users table created\n";
		}
	} else {
		echo "Found users table in the database\n";
	}
}