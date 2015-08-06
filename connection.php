<?php
/*--------------------BEGINNING OF THE CONNECTION PROCESS------------------*/
//define constants for db_host, db_user, db_pass, and db_database
//adjust the values below to match your database settings
define('DB_HOST', 'aar85g818k10po.corkzmip7kuo.us-west-2.rds.amazonaws.com');
define('DB_USER', 'matthewis');
define('DB_PASS', 'awesomesauce'); //set DB_PASS as 'root' if you're using mac
define('DB_DATABASE', 'ebdb'); //make sure to set your database
define('DB_PORT', '3306');
//connect to database host
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE ,DB_PORT);
/*-------------------------END OF CONNECTION PROCESS!---------------------*/
if ($connection->connect_errno) 
{
	die("Failed to connect to MySQL: (" . $connection->connect_errno . ") " . $connection->connect_error);
}
 
?>