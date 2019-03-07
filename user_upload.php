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

//command line option settings
$shortOpt = "u:";
$shortOpt .= "p:";
$shortOpt .= "h:";
$shortOpt .= "d:";

$longOpt = array(
			"file:",
			"create_table",
			"rebuild_table",
			"dry_run",
			"help");

//get command line option
$opt = getopt($shortOpt, $longOpt);

if(empty($opt)) {
	echo "No options found or wrong use of options, use --help to see the option list\n";
} elseif(isset($opt['create_table'])) {
	$con = connect_db($opt);
	
	if($con) {
		create_table();
		mysqli_close($con);
	}
} elseif(isset($opt['rebuild_table'])) {
	$con = connect_db($opt);
	
	if($con) {
		rebuild_table();
		mysqli_close($con);
	}
} elseif(isset($opt['help'])) {
	print_help();
} elseif(isset($opt['file'])) {
	$userFilePath = $opt['file'];
	
	if(isset($opt['dry_run'])) {
		echo "Dry run option selected\n";
		echo "Database will not be altered\n";
	} else {
		$con = connect_db($opt);
	}
	
	if(file_exists($userFilePath)) {
		if(isset($con)) {
			create_table(); //create table if not exists
		}
		
		$file = fopen($userFilePath, "r");
		fgetcsv($file); //read the first line and do nothing with it

		while(!feof($file)) {
			$user = fgetcsv($file);
			if($user != array(null)) {
				//print_r($user);
				
				$user[2] = strtolower(trim($user[2]));
				
				if(filter_var($user[2], FILTER_VALIDATE_EMAIL)) { //validate email
					//make first letter uppercase and add blackslashes to special character and trim
					$user[0] = addslashes(ucfirst(strtolower(trim($user[0]))));
					$user[1] = addslashes(ucfirst(strtolower(trim($user[1]))));
					$user[2] = addslashes($user[2]);
					
					if(isset($con)) {
						$insertQuery = "INSERT INTO `$tableName` (`name`, `surname`, `email`) VALUES ('{$user[0]}', '{$user[1]}', '{$user[2]}')";
						if(!mysqli_query($con, $insertQuery)) {
							echo "Error message: ".mysqli_error($con)."\n";
						}
					}
				} else {
					echo "Error message: Your email {$user[2]} is not valid\n";
				}
			}
		}
		
		if(isset($con)) {
			echo "Finished inputting user data into database\n";
		}
		fclose($file);
	} else {
		echo "Error message: File $userFilePath not found\n";
	}
	
	if(isset($con)) {
		mysqli_close($con);
	}

} else {
	echo "Wrong use of options, use --help to see the option list\n";
}

//functions
function connect_db($opt) {
	//get global or user input variable
	$host = isset($opt['h'])?$opt['h']:$GLOBALS['host'];
	$username = isset($opt['u'])?$opt['u']:$GLOBALS['username'];
	$password = isset($opt['p'])?$opt['p']:$GLOBALS['password'];
	$dbName = isset($opt['d'])?$opt['d']:$GLOBALS['dbName'];
	
	$con = mysqli_connect($host, $username, $password, $dbName);
	
	if(!$con) {
		echo "Connection failed: ".mysqli_connect_error();
		return false;
	} else {
		return $con;
	}
}

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
		echo "No table created\n";
	}
}

function rebuild_table() {
	//get global variable
	$con = $GLOBALS['con'];
	$tableName = $GLOBALS['tableName'];
	$makeTableQuery = $GLOBALS['makeTableQuery'];
	
	//Drop the database
	if(!mysqli_query($con, "DROP TABLE `users`")) {
		echo "Error message: ".mysqli_error($con)."\n";
	} else {
		echo "Users table deleted\n";
	}
	
	//Build the database
	if(!mysqli_query($con, $makeTableQuery)) {
		echo "Error message: ".mysqli_error($con)."\n";
	} else {
		echo "Users table created\n";
	}
}

function print_help() {
	echo "--file [csv file name] - this is the name of the CSV to be parsed\n";
	echo "--create_table - this will cause the MySQL users table to be built (and no furtheraction will be taken\n";
	echo "-- rebuild_table - this will delete and build new MySQL users table\n";
	echo "--dry_run - this will be used with the --file directive in the instance that we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered\n";
	echo "-u - MySQL username\n";
	echo "-p - MySQL password\n";
	echo "-h - MySQL host\n";
	echo "-d - MySQL database\n";
	echo "--help - which will output the above list of directives with details\n";
}