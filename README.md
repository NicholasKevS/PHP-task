# PHP-task
Programming Evaluation for: PHP

## PHP version
Using php 7.0.33 for windows

Downloaded from [https://windows.php.net/download/]

## MySQL version
Using MySQL 10.1.38-MariaDB from XAMPP

Downloaded from [https://www.apachefriends.org/download.html]

## Dependencies
- MySQLi

## Additional Assumptions
- If there is no users table then the script will built one even without create_table option
- Users table only have 4 colums which are id, name, surname, and email.
- create_table option will only create table if there is no users table
- Add --rebuild_table option to drop and create users table
- Add -d option to specify database name