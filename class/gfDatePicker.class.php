<?php
class DatePicker {
    private $_fromDate;
    private $_toDate;
    private $_dateRange;   
    
    public function __construct($fromDate="", $toDate="", $dateRange="") {
	if ($fromDate != "" && $toDate != "" && $dateRange != "") {
	    $this->_fromDate = $fromDate;
	    $this->_toDate = $toDate;	   
	    $this->_dateRange = $dateRange;
	}
    }
    
    public function getFromDate(){
	return $this->_fromDate;
    }
    
    public function getToDate(){
	return $this->_toDate;
    }
    
    public function getDateRange(){
	return $this->_dateRange;
    }
    
    public function getUnixFromDate(){
	return strtotime($this->_fromDate);
    }
    
    public function getUnixToDate(){
	if ($this->_toDate != ""){
	    /*
	     * We are adding 86399sec (1day - 1sec) so unixToDate returns 11:59 PM(End of Day)
	     * instead of 12:00 AM(Begining of day)
	     */
	    return strtotime($this->_toDate) + (86399);
	} else {
	    return "";
	}	
    }
}
?>
