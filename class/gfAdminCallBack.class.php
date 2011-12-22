<?php

require_once 'gfCRUD.class.php';
require_once 'gfPagination.php';

class AdminCallBack {
    private $_crud;
    private $_instanceId;
    private $_dateRange;
    private $_fromDate;
    private $_toDate;
    private $_unixFromDate;
    private $_unixToDate;
    
    /**
     *
     * @param int    $instanceId    Instance Id of a partner website	   
     * @param string $fromDate	    Date From
     * @param string $toDate	    Date To
     * @param string $dateRange	    Name of the Date Range Form
     */
    public function __construct($instanceId, $fromDate = "", $toDate = "", $dateRange="") {	
	if (empty($instanceId)){
	    throw new Exception("Partner ID Not provided");
	}	
	if ($fromDate != "" && $toDate !="" && $dateRange != ""){	    
	    $this->_fromDate = $fromDate;	    
	    $this->_toDate = $toDate;	    
	    $this->_unixFromDate = strtotime($fromDate);
	    
	    /*
	     * We are adding 86399sec (1day - 1sec) so unixToDate returns 11:59 PM(End of Day)
	     * instead of 12:00 AM(Begining of day)
	     */
	    $this->_unixToDate = strtotime($toDate) + (86399);	
	   
	    $this->_dateRange = $dateRange;
	}
	$this->_instanceId = $instanceId;
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
     * Populates table with Callback records and paginates
     * 
     * @param int    $rowNum	 Number of rows per page
     * @param int    $numLink	 Number of links     
     * @param string $cbStatus   Status of callback [Answered, Unanswered]
     */
    public function viewPaginateCallBacks($rowNum, $numLink, $cbStatus=""){		
	if ($cbStatus == "" || $cbStatus == '2'){	    
	    $sql = "SELECT callbackuser.user_id, enq_id, instanceId, name, email, telephone, enquiry, callBackDate, cb_status
		FROM callbackuserenquiry, callbackuser		
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		AND callbackuserenquiry.instanceId = $this->_instanceId";
		if ($this->_unixFromDate != "" && $this->_unixToDate != ""){		    
		    $sql .= " AND callBackDate > $this->_unixFromDate AND callBackDate < $this->_unixToDate"; 		
		}
		$sql .= " ORDER BY callbackuserenquiry.callBackDate DESC";	    
	    if (Debug::getDebug()){
		    fb($sql, "SQL No Status: ", FirePHP::INFO);		    
	    }
	} else if ($cbStatus == '0' || $cbStatus == '1') {	    
	    $sql = "SELECT callbackuser.user_id, enq_id, name, email, telephone, enquiry, callBackDate, cb_status
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		AND callbackuserenquiry.instanceId = $this->_instanceId";
		if ($this->_unixFromDate != "" && $this->_unixToDate != ""){		    
		    $sql .= " AND callBackDate > $this->_unixFromDate AND callBackDate < $this->_unixToDate"; 		
		}		
		$sql .= " AND cb_status = '$cbStatus' ORDER BY callbackuserenquiry.callBackDate DESC";
	    if (Debug::getDebug()){
		    fb($sql, "SQL Status Set: ", FirePHP::INFO);		    
	    }
	}	
	
	$pager = new PS_Pagination($this->_crud, $sql, $rowNum, $numLink, "&cbStatus=$cbStatus&param1=valu1&param2=value2&fromDate=$this->_fromDate&toDate=$this->_toDate&dateRange=$this->_dateRange");
		
	//returns resultset or false
	$reqResultSet = $pager->paginate();
		
	//if(!$reqResultSet) die(mysql_error());
	if (!$reqResultSet){
	    return false;
	} else {
	
	//Updates the Answered and Unanswered callbacks
	$this->countAnsCB();
	$this->countUnAnsCB();
	
	$callBackTableSet =  '
	    <div id="middle">
		<div id="search">
			<label for="filter">Filter Record: </label> <input type="text" name="filter" value="" id="filter" />
			<span id="displayRecord">
			    <form action="'.$_SERVER['PHP_SELF'].'" method="get" class="pull-right">
				<input type="hidden" name="cbStatus" value="'.$cbStatus.'">				
				<input type="hidden" name="fromDate" value="'.$this->_fromDate.'">
				<input type="hidden" name="toDate" value="'.$this->_toDate.'">
				<input type="hidden" name="dateRange" value="'.$this->_dateRange.'">	
				<label id="disRecHelpText" class="help-inline">Enter valid number and press &#60;ENTER&#62; !&nbsp;</label>
				<input id="disRecord" class="input-small span2" type="text" size="15" placeholder="Display Record" name="row_pp">				
			    </form>
			</span>
		</div>
		<table class="zebra-striped tablesorter" id="CallBackTable">
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
			    $status = "<a href='".$_SERVER['PHP_SELF']."?enq_id=".$r[enq_id]."&page=".$pager->getPage()."&row_pp=".$pager->getRowsPerPage()."&cbStatus=".$cbStatus."&param1=valu1&param2=value2&fromDate=$this->_fromDate&toDate=$this->_toDate&dateRange=$this->_dateRange'><button class='btn danger'>Callback</button></a>";
			} else {
			    $status = "<button class='btn success disabled'>Answered</button>";
			}
			$callBackTableSet .= "<tr><td>".$date."</td>
				<td>".$r[name]."</td>
				<td>".$r[email]."</td>
				<td>".$r[telephone]."</td>
				<td>".$r[enquiry]."</td>
				<td>".$status."</td>
			    </tr>";		    
		    }
		    
		    $callBackTableSet .= "</tbody></table>";

		    //Display the full navigation in one go		  
		    $callBackTableSet .= "<div class='cPaginator'>".$pager->renderFullNav()."</div>";	    
		    
		    return $callBackTableSet;
	}
    }
    
    /**
     * Updates the status of the callback table
     * 
     * @param string $enqId Enquiry Id of the callback table
     */
    public function updateCallBackStatus($enqId){	
	$this->_crud->dbUpdate('callbackuserenquiry', 'cb_status', 1, 'enq_id', $enqId);	
    }
    
    /**
     * Displays Total Number of Answered Call Back
     * 
     * @return int Number of answered Call Back 
     */
    public function countAnsCB(){
	Fb::info("Answered:");	
	$rs = $this->_crud->dbSelectFromTo('callbackuserenquiry', $this->_instanceId, 'cb_status', '1', 'callBackDate', $this->_unixFromDate, $this->_unixToDate);		
	return count($rs);	
    }
    
    /**
     * Displays Total Number of Unanswered Call Back
     * 
     * @return int Number of Unanswered Call Back
     */
    public function countUnAnsCB(){
	Fb::info("Un Answered:");
	$rs = $this->_crud->dbSelectFromTo('callbackuserenquiry', $this->_instanceId, 'cb_status', '0', 'callBackDate', $this->_unixFromDate, $this->_unixToDate);		
	return count($rs);	
    }
    
    /**
     * Displays Total Number of Call Back
     * 
     * @return int Number all Call Back
     */
    public function countTotCB(){
	Fb::info("Total Call Back");
		
	$rs = $this->_crud->dbSelectFromTo('callbackuserenquiry', $this->_instanceId, null, null, 'callBackDate', $this->_unixFromDate, $this->_unixToDate);	
	return count($rs);	
    }
}

?>
