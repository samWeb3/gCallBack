<?php

require_once 'gfCRUD.class.php';
require_once 'gfPagination.class.php';
require_once 'gfDatePicker.class.php';

class CallBackStats {

    private $_crud;
    private $_datePicker;
    private $_instanceId;        

    /**
     *
     * @param int $instanceId	Instance Id of a partner website
     */
    public function __construct(CRUD $crud, $instanceId, DatePicker $datePicker) {	
	if (empty($instanceId)){
	    throw new Exception("Partner ID Not provided");
	}
	if (empty($datePicker)) {
	    throw new Exception("Date Object Not provided");
	}
	$this->_datePicker = $datePicker;
	$this->_instanceId = $instanceId;
	$this->_crud = $crud;
    }

    /**
     * Get Total & Answered callback records between Dates
     * Add the records into the javascript array constructed earlier
     * 
     * @param type $toDate	End Date
     * @param type $fromDate    Start Date
     * @param type $noOfDays    Days between Start and End Date
     */
    function getRecords($toDate, $fromDate, $noOfDays) {

	//Doing this inorder to add php value inside javascript array dayRange, callBackRec, $countAnsRec
	echo "<script type='text/javascript' language='JavaScript'>";

	//Iterate through each Days to get records
	for ($i = 0; $i < $noOfDays; $i++) {
	    
	    /****************************************************************
	     * printing the php var in a javascript array we declared earlier
	     ****************************************************************/
	    echo "dayRange.push($fromDate * 1000);"; //need to multiply unix timestamp by 1000 to get javascript timestamp
	    
	    //add 86400sec to get the end of the day
	    $fromDateEnd = $fromDate + 86400;

		/*****************************************************
		 * FOR TOTAL CALLBACKS 
		 *****************************************************/
		//Strip the record number from resultset Array
		$countCBRec = $this->countRecord($this->statResultSet($fromDate, $fromDateEnd));

		/******************************************************
		 * FOR ANSWERED CALLBACKS 
		 ******************************************************/		
		$countAnsRec = $this->countRecord($this->statResultSet($fromDate, $fromDateEnd, '1'));

		//Add one more day (86400sec) to the first day 
		$fromDate = $fromDate + 86400;

	    /****************************************************************
	     * printing the php var in a javascript array we declared earlier
	     * **************************************************************/	    
	    echo "totalRec.push($countCBRec);";
	    echo "ansRec.push($countAnsRec);";
	}

	echo "</script>";
    }   
    
    /**
     * Computes Prev one month date from the current day
     */
    public function monthStats(){	
	$this->resetRecords();
	//$this->_dpStatistics->setNoOfDays(31);
	$this->getRecords($this->getUnixToDate(), $this->getUnixFromDate(), $this->getNoOfDays());
    }    
    
    /**
     * Computes a Date Range received from Date Picker
     * 
     * @param type $fromDate	Stats to display from the start date
     * @param type $toDate      Stats to display until the end date
     */
    public function customStats($fromDate, $toDate){
	$this->resetRecords();	
	/*
	 * If From Date and To Date not set with the custom value received from parameter
	 * it always return the 30 days or noOFDays set in above monthStats() function 
	 */
	$this->setFromDate($fromDate);
	$this->setToDate($toDate);	
	$this->getRecords($this->getUnixToDate(), $this->getUnixFromDate(), $this->getNoOfDays());
    }
    
     /**
     * Strip number from Array [Result set from sql query]
     * 
     * @param type $countRecArr Pass a counted result in Array 
     * @return type		Integer
     */
    private function countRecord($countRecArr) {
	foreach ($countRecArr as $num) {
	    foreach ($num as $k => $v) {
		$countRecNum = $v;
	    }
	}
	return $countRecNum;
    }
    
    /**
     * Need to reset the callBackRec and ansCBRec js array before
     * fetching the correct reocord set
     * 
     * Else record set will be populated by old set of data
     */
    private function resetRecords(){
	echo "<script type='text/javascript' language='JavaScript'>";
	echo "totalRec = [];";
	echo "ansRec = [];";
	echo "</script>";
    }
    
    /**
     * Returns the resultSet required for generating stats
     * 
     * @param type $fromDate	    Stats to display from the start date 00:00:00
     * @param type $fromDateEnd	    Stats to display from the start date 23:59:59    
     * @param type $cbStatus	    '1' if Answered
     * @return type		    Array [resultSet]
     */
    private function statResultSet($fromDate, $fromDateEnd, $cbStatus=""){
	$sql =  "select count(*) from callbackuserenquiry where callBackDate > 
				:fromDate and callBackDate < :fromDateEnd AND callbackuserenquiry.instanceId = :insId";
	if ($cbStatus == '1'){
	    $sql .= " AND cb_status = $cbStatus";
	} 			
		
	$stmt = $this->_crud->getDbConn()->prepare($sql);
	$stmt->bindParam(':insId', $this->_instanceId, PDO::PARAM_STR);
	$stmt->bindParam(':fromDate', $fromDate, PDO::PARAM_STR);
	$stmt->bindParam(':fromDateEnd', $fromDateEnd, PDO::PARAM_STR);
	$stmt->execute();
	
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /********************************************************
     * Delegating Methods
     ********************************************************/
    private function setFromDate($fromDate){
	$this->_datePicker->setFromDate($fromDate);
    }
    
    private function setToDate($toDate){
	$this->_datePicker->setToDate($toDate);
    }
    
    private function getUnixToDate(){
	return $this->_datePicker->getUnixToDate();
    }
    
    private function getUnixFromDate(){
	return $this->_datePicker->getUnixFromDate();
    }
    
    private function getNoOfDays(){
	return $this->_datePicker->getNoOfDays();
    }
    
    public function getFromDate(){
	return $this->_datePicker->getUnixFromDate();
    }
    
    public function getToDate(){
	return $this->_datePicker->getUnixToDate();
    }
}

?>
