<?php

require_once 'gfCRUD.class.php';
require_once 'ps_pagination.php';

class AdminCallBack {
    private $_crud;
    
    public function __construct() {
	$this->dbConnSetup();
    }

    private function dbConnSetup() {
	$this->_crud = new CRUD();
	$this->_crud->username = 'root';
	$this->_crud->password = 'root123';
	$this->_crud->dsn = "mysql:dbname=griff;host=localhost";
    }
    
     public function viewAllCallBacks(){
	$sql = "SELECT callbackuser.user_id, name, email, telephone, enquiry, callBackDate
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		ORDER BY callbackuserenquiry.callBackDate DESC";
	
	$this->_crud->conn();
	$stmt= $this->_crud->getDbConn()->prepare($sql);
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Display CallBack records and paginates
     * 
     * @param int $rowNum   Number of rows per page
     * @param int $numLink  Number of links
     */
    public function viewPaginateCallBacks($rowNum, $numLink){
	$sql = "SELECT callbackuser.user_id, name, email, telephone, enquiry, callBackDate
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		ORDER BY callbackuserenquiry.callBackDate DESC";
	
	
	/*
	 * Create a PS_Pagination object
	 * 
	 * $conn = MySQL connection object
	 * $sql = SQl Query to paginate
	 * $rowNum = Number of rows per page
	 * $numLink = Number of links
	 * "param1=valu1&param2=value2" = You can append your own parameters to paginations links
	 */
	$pager = new PS_Pagination($this->_crud, $sql, $rowNum, $numLink, "param1=valu1&param2=value2");
	
	/*
	 * Enable debugging if you want o view query errors
	*/
	$pager->setDebug(true);
	
	/*
	 * The paginate() function returns a mysql result set
	 * or false if no rows are returned by the query
	*/
	$reqResultSet = $pager->paginate();
	
	if(!$reqResultSet) die(mysql_error());
	
	echo '
	    <div id="middle">
		<div id="search">
			<label for="filter">Filter Record: </label> <input type="text" name="filter" value="" id="filter" />
		</div>
		<table cellpadding="1" cellspacing="1" id="resultTable">
		    <thead>
		    <tr>
			<th>Date: </th>
			<th>Name: </th>
			<th>Email: </th>
			<th>Phone No: </th>
			<th>Enquiry</th>
		    </tr>
		    </thead><tbody>';
	
	
	foreach ($reqResultSet as $r){
	    $date = date('d.M.Y', $r[callBackDate]);
	    echo "<tr><td>".$date."</td><td>".$r[name]."</td><td>".$r[email]."</td><td>".$r[telephone]."</td><td>".$r[enquiry]."</td></tr>";	
	    
	}
	
	//Display the full navigation in one go
	echo "</tbody><tfoot><tr><td colspan='6'>".$pager->renderFullNav()."<tr><td></tfoot>";
	
	echo "</table>";
    }
}

?>
