<?php

require_once 'gfCRUD.class.php';
require_once 'gfPagination.php';
require_once 'class/gfDatePicker.class.php';
require_once 'gfDatePickerDashboard.class.php';

class AdminCallBack {

    private $_crud;
    private $_dPDashboard;
    private $_instanceId;
    private $_cbStatus;    
    private $_pager;   

    /**
     *
     * @param int    $instanceId    Instance Id of a partner website	   
     * @param string $fromDate	    Date From
     * @param string $toDate	    Date To
     * @param string $dateRange	    Name of the Date Range Form
     */
    public function __construct($instanceId, DatePickerDashboard $dPDashboard) {
	if (empty($instanceId)) {
	    throw new Exception("Partner ID Not provided");
	}
	if (empty($dPDashboard)) {
	    throw new Exception("Date Object Not provided");
	}
	$this->_dPDashboard = $dPDashboard;	
	$this->_instanceId = $instanceId;
	$this->_crud = new CRUD();
    }

    /**
     * Display all CallBack Records without pagination them
     * 
     * @return type Result set of rows
     */
    public function viewAllCallBacks() {
	$sql = "SELECT callbackuser.user_id, name, email, telephone, enquiry, callBackDate
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		ORDER BY callbackuserenquiry.callBackDate DESC";

	$stmt = $this->_crud->getDbConn()->prepare($sql);
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
    public function viewPaginateCallBacks($rowNum, $numLink, $cbStatus="") {
	
	$this->_cbStatus = $cbStatus;

	if ($cbStatus == "" || $cbStatus == '2') {//Total CallBack	    
	    $sql = $this->callBackQuery($cbStatus);
	} else if ($cbStatus == '0' || $cbStatus == '1') {//Answered or Unanswered CB	    
	    $sql = $this->callBackQuery($cbStatus);
	}	
	$this->_pager = new PS_Pagination($this->_crud, $sql, $rowNum, $numLink, "&cbStatus=$cbStatus&param1=valu1&param2=value2&fromDate=".$this->getFromDate()."&toDate=".$this->getToDate()."&dateRange=".$this->getDateRange().'"');
	
	//returns resultset or false
	$reqResultSet = $this->_pager->paginate();
	
	if (!$reqResultSet) {
	    return false;
	} else {
	    //Updates the Answered and Unanswered callbacks
	    $this->countAnsCB();
	    $this->countUnAnsCB();
	    
	    return $reqResultSet;	    
	}
    }
    
    public function getPaginatorNav(){
	return $this->_pager->renderFullNav();
    }

    /**
     * Construct the Query for retrieving Callback result
     * 
     * @param type $cbStatus	Total CallBacks [" " || 2]; Answered [1], Unanswered [0]
     * @return string		SQL query
     */
    private function callBackQuery($cbStatus="") {
	$sql = "SELECT callbackuser.user_id, enq_id, instanceId, name, email, telephone, enquiry, callBackDate, cb_status
		FROM callbackuserenquiry, callbackuser		
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		AND callbackuserenquiry.instanceId = $this->_instanceId";
	if ($this->getUnixFromDate() != "" && $this->getUnixToDate() != "") {	    
	    $sql .= " AND callBackDate > ".$this->getUnixFromDate()." AND callBackDate < ". $this->getUnixToDate();
	}
	if ($cbStatus == '0' || $cbStatus == '1') {
	    $sql .= " AND cb_status = '$cbStatus'";
	}
	$sql .= " ORDER BY callbackuserenquiry.callBackDate DESC";
	if (Debug::getDebug()) {
	    fb($sql, "SQL: ", FirePHP::WARN);
	}
	return $sql;
    }

    /**
     * Updates the status of the callback table
     * 
     * @param string $enqId Enquiry Id of the callback table
     */
    public function updateCallBackStatus($enqId) {
	$this->_crud->dbUpdate('callbackuserenquiry', 'cb_status', 1, 'enq_id', $enqId);
    }

    /**
     * Displays Total Number of Answered Call Back
     * 
     * @return int Number of answered Call Back 
     */
    public function countAnsCB() {
	Fb::info("Answered:");
	$rs = $this->_crud->dbSelectFromTo('callbackuserenquiry', $this->_instanceId, 'cb_status', '1', 'callBackDate', $this->getUnixFromDate(), $this->getUnixToDate());
	return count($rs);
    }

    /**
     * Displays Total Number of Unanswered Call Back
     * 
     * @return int Number of Unanswered Call Back
     */
    public function countUnAnsCB() {
	Fb::info("Un Answered:");
	$rs = $this->_crud->dbSelectFromTo('callbackuserenquiry', $this->_instanceId, 'cb_status', '0', 'callBackDate', $this->getUnixFromDate(), $this->getUnixToDate());
	return count($rs);
    }

    /**
     * Displays Total Number of Call Back
     * 
     * @return int Number all Call Back
     */
    public function countTotCB() {
	Fb::info("Total Call Back");

	$rs = $this->_crud->dbSelectFromTo('callbackuserenquiry', $this->_instanceId, null, null, 'callBackDate', $this->getUnixFromDate(), $this->getUnixToDate());
	return count($rs);
    }
    
    /***Getters and Setters***/
    public function getToDate(){
	return $this->_dPDashboard->getToDate();
    }
    
    public function getUnixToDate(){
	return $this->_dPDashboard->getUnixToDate();
    }
    
    public function getUnixFromDate(){
	return $this->_dPDashboard->getUnixFromDate();
    }
    
    public function getFromDate(){
	
	return $this->_dPDashboard->getFromDate();
    }
    
    public function getCbStatus(){
	return $this->_cbStatus;
    }
    
    public function getDateRange(){
	return $this->_dPDashboard->getDateRange();
    }
    
    public function getPageNo(){
	return $this->_pager->getPage();
    }
    
    public function getRecordsPerPage(){
	return $this->_pager->getRowsPerPage();
    }
}

?>
