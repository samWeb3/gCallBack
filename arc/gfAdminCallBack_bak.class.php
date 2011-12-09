<?php

require_once '../class/gfCRUD.class.php';
require_once 'gfPagination_bak.php';

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
	$this->_crud->conn();
    }
    
    /**
     * Display all CallBack Records without pagination them
     * 
     * @return type Result set of rows
     */
     public function viewAllCallBacks(){
	$sql = "SELECT callbackuser.user_id, name, email, telephone, enquiry, callBackDate
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		ORDER BY callbackuserenquiry.callBackDate DESC";
	
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
    public function viewPaginateCallBacks($rowNum, $numLink, $cbStatus=""){	
	if ($cbStatus == ""){	    
	    $sql = "SELECT callbackuser.user_id, enq_id, name, email, telephone, enquiry, callBackDate, cb_status
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id		
		ORDER BY callbackuserenquiry.callBackDate DESC";	    
	} else if ($cbStatus == '0' || $cbStatus == '1') {	    
	    $sql = "SELECT callbackuser.user_id, enq_id, name, email, telephone, enquiry, callBackDate, cb_status
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		AND cb_status = '$cbStatus'
		ORDER BY callbackuserenquiry.callBackDate DESC";
	}	
	
	$pager = new PS_Pagination($this->_crud, $sql, $rowNum, $numLink, "param1=valu1&param2=value2");
	
	/*
	 * The paginate() function returns a mysql result set
	 * or false if no rows are returned by the query
	*/
	$reqResultSet = $pager->paginate();
		
	if(!$reqResultSet) die(mysql_error());
	
	$this->countAnsCB();
	$this->countUnAnsCB();
	
	$callBackTableSet =  '
	    <div id="middle">
		<div id="search">
			<label for="filter">Filter Record: </label> <input type="text" name="filter" value="" id="filter" />
		</div>
		<table class="zebra-striped tablesorter" id="resultTable">
		    <thead>
		    <tr>
			<th>Date: </th>
			<th>Name: </th>
			<th>Email: </th>
			<th>Phone No: </th>
			<th>Enquiry</th>
			<th>Status</th>
		    </tr>
		    </thead><tbody>';	
	
		    foreach ($reqResultSet as $r){
			$date = date('d.M.Y', $r[callBackDate]);
			$status = "";
			if ($r[cb_status] == 0){
			    //need to pass a pager number to ensure when callback is called from page it doesn't go back to first page
			    $status = "<a href='".$_SERVER['PHP_SELF']."?enq_id=".$r[enq_id]."&page=".$pager->getPage()."&param1=valu1&param2=value2'>Callback</a>";
			} else {
			    $status = "Answered";
			}
			$callBackTableSet .= "<tr><td>".$date."</td>
				<td>".$r[name]."</td>
				<td>".$r[email]."</td>
				<td>".$r[telephone]."</td>
				<td>".$r[enquiry]."</td>
				<td>".$status."</td>
			    </tr>";		    
		    }

		    //Display the full navigation in one go
		    $callBackTableSet .= "</tbody><tfoot><tr><td colspan='6'>".$pager->renderFullNav()."<tr><td></tfoot></table>";
		    
		    return $callBackTableSet;
		    
		//echo "</table>";
    }
    
    /**
     * Updates the status of the callback table
     * 
     * @param string $enqId Enquiry Id of the callback table
     */
    public function updateCallBackStatus($enqId){	
	$this->_crud->dbUpdate('callbackuserenquiry', 'cb_status', 1, 'enq_id', $enqId);
	//Need to update the count result
	//$this->countAnsCB();
	//$this->countUnAnsCB();
    }
    
    /**
     * Displays Total Number of Answered Call Back
     * 
     * @return int Number of answered Call Back 
     */
    public function countAnsCB(/*$pageNo="1"*/){
	$rs = $this->_crud->dbSelect('callbackuserenquiry', 'cb_status', '1');
	$num = count($rs);
	//echo "Answered Call Back:  <a href='".$_SERVER['PHP_SELF']."?ans_CB=ans_CB&page=$pageNo'>$num</a><br />";
	return count($rs);	
    }
    
    /**
     * Displays Total Number of Unanswered Call Back
     * 
     * @return int Number of Unanswered Call Back
     */
    public function countUnAnsCB(){
	$rs = $this->_crud->dbSelect('callbackuserenquiry', 'cb_status', '0');
	$num = count($rs);
	//echo "UnAnswered Call Back:  <a href='".$_SERVER['PHP_SELF']."?unAns_CB=unAns_CB'>$num</a><br />";
	return count($rs);
	
    }
    
    /**
     * Displays Total Number of Call Back
     * 
     * @return int Number all Call Back
     */
    public function countTotCB(){
	$rs = $this->_crud->dbSelect('callbackuserenquiry', 'cb_status');
	return count($rs);	
    }
}

?>
