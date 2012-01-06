<?php

require_once 'gfCRUD.class.php';
require_once 'gfPagination.php';

class CallBackStats {

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

		//Get the record for a specified day
		$resCB = $this->_crud->rawSelect("select count(*) from callbackuserenquiry where callBackDate > 
				$fromDate and callBackDate < $fromDateEnd");

		//Strip the record number from resultset Array
		$countCBRec = $this->countRecord($resCB);

		/******************************************************
		 * FOR ANSWERED CALLBACKS 
		 ******************************************************/

		$resAnsCB = $this->_crud->rawSelect("select count(*) from callbackuserenquiry where callBackDate > 
				$fromDate and callBackDate < $fromDateEnd and cb_status = 1");

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
     * @return type 
     */
    private function countRecord($countRecArr) {
	foreach ($countRecArr as $num) {
	    foreach ($num as $k => $v) {
		$countRecNum = $v;
	    }
	}
	return $countRecNum;
    }
}

?>
