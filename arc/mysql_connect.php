<?php # Script - mysql_connect.php

/*This file contains the database access information for the database. This file also
establishes a connection to MySQL and selects the database.*/

//connect to the db_access_ info.php to get access information
include_once('db_access_info_securetropos.inc');

//Make the connection and then select the database.
$dbc = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) OR die ('Could not connect to MYSQL: '. mysql_error());
mysql_select_db (DB_NAME) or die ('Could not select the database: '. mysql_error());

//Function for escaping and trimming from data.
function escape_data ($data) {
	global $dbc;
	if (ini_get('magic_quotes_gpc'))
	{
		$data = stripslashes($data);
	}
	return mysql_escape_string(trim ($data));

} //End of escape_data() function.

?>