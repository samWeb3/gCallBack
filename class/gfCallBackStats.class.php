<?php

require_once 'gfCRUD.class.php';
require_once 'gfPagination.php';

class CallBackStats {

    private $_crud;
    private $_instanceId;    

    /**
     *
     * @param int $instanceId	Instance Id of a partner website
     */
    public function __construct($instanceId) {	
	if (empty($instanceId)){
	    throw new Exception("Partner ID Not provided");
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
     * Get Total & Answered callback records between Dates
     * Add the records into the javascript array constructed earlier
     * 
     * @param type $toDate	End Date
     * @param type $fromDate    Start Date
     * @param type $noOfDays    Days between Start and End Date
     */
    function getRecords($toDate, $fromDate, $noOfDays) {
	
	if (Debug::getDebug()){
	    fb(date('d.M.Y h:i:s A', $toDate), "To Date: ", FirePHP::INFO);		    
	    fb(date('d.M.Y h:i:s A', $fromDate), "From Date: ", FirePHP::INFO);		    
	    fb($noOfDays, "No of Days: ", FirePHP::INFO);		    
	}

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
	        $resCB = $this->statResultSet($fromDate, $fromDateEnd);
		//Strip the record number from resultset Array
		$countCBRec = $this->countRecord($resCB);

		/******************************************************
		 * FOR ANSWERED CALLBACKS 
		 ******************************************************/
		$resAnsCB = $this->statResultSet($fromDate, $fromDateEnd, '1');
		$countAnsRec = $this->countRecord($resAnsCB);

		//Add one more day (86400sec) to the first day 
		$fromDate = $fromDate + 86400;

	    /*****************************************************
	     * printing the php var in a javascript array we declared earlier
	     * *************************************************** */
	    
	    echo "callBackRec.push($countCBRec);";
	    echo "ansCBRec.push($countAnsRec);";
	}

	echo "</script>";
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
     * Computes Prev one month date from the current day
     */
    public function monthStats(){
	$this->resetRecords();
	//Get the today end day 
	$unixToDate = strtotime('today') + 86400;
	//calculate the range for 30 days
	$range = 86400 * 30;
	//Deduct $range from $toDate
	$unixFromDate = $unixToDate - $range;
	$noOfDays = round($range / 86400);

	$this->getRecords($unixToDate, $unixFromDate, $noOfDays);
    }
    
    /**
     * Computes a Date Range received from Date Picker
     * 
     * @param type $fromDate	Stats to display from the start date
     * @param type $toDate      Stats to display until the end date
     */
    public function customStats($fromDate, $toDate){
	$this->resetRecords();
	$unixFromDate = strtotime($fromDate);

	//we add one day (86400sec) to a toDate to get PM
	$unixToDate = strtotime($toDate) + 86399;

	//to calculate how many days we need range
	$range = $unixToDate - $unixFromDate;

	//get the number of days
	$noOfDays = round($range / 86400);

	$this->getRecords($unixToDate, $unixFromDate, $noOfDays);	
    }
    
    /**
     * Need to reset the callBackRec and ansCBRec js array before
     * fetching the correct reocord set
     * 
     * Else record set will be populated by old set of data
     */
    private function resetRecords(){
	echo "<script type='text/javascript' language='JavaScript'>";
	echo "callBackRec = [];";
	echo "ansCBRec = [];";
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
}

?>
