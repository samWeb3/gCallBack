<?php
require_once 'gfDatePickerSuper.class.php';

class DatePickerDashboard extends DatePickerSuper{
    private $_dateRangeSet; //<-date Range

    public function __construct($fromDate = "", $toDate = "", $dateRangeSet = "") {
	parent::__construct($fromDate, $toDate);
	if ($dateRangeSet != ""){
	    $this->_dateRangeSet = $dateRangeSet;
	}
    }
    
    public function getDateRange() {
	return $this->_dateRangeSet;
    }
    
    public function getUnixFromDate(){
	return strtotime(parent::getFromDate());
    }
    
    public function getUnixToDate(){
	if (parent::getToDate() != ""){
	    /*
	     * We are adding 86399sec (1day - 1sec) so unixToDate returns 11:59 PM(End of Day)
	     * instead of 12:00 AM(Begining of day)
	     */
	    return strtotime(parent::getToDate()) + (86399);
	} else {
	    return "";
	}	
    }
    
}

?>
