<?php

class DatePicker {

    private static $_noOfDays;
    
    private $_fromDate;
    private $_toDate;    
    private $_dateRangeSet;

    public function __construct($fromDate="", $toDate="", $dateRangeSet = "") {
	if ($fromDate != "" && $toDate != "") {
	    $this->_fromDate = $fromDate;
	    $this->_toDate = $toDate;	    
	}
	if ($dateRangeSet != ""){
	    $this->_dateRangeSet = $dateRangeSet;
	}
    }
    
    public function getDateRangeSet() {
	return $this->_dateRangeSet;
    }

    public function getFromDate() {
	return $this->_fromDate;
    }

    public function getToDate() {
	return $this->_toDate;
    }
    
    public function getUkFromDate(){
	return date("d M Y h:i:s A", $this->getUnixFromDate());
    }
    
    public function getUKToDate(){
	return date("d M Y h:i:s A", $this->getUnixToDate());
    }    

    public function setToDate($toDate) {
	$this->_toDate = $toDate;
    }

    public function setFromDate($fromDate) {
	$this->_fromDate = $fromDate;
    }

    public static function setNoOfDays($noOfDays) {
	self::$_noOfDays = $noOfDays;
    }

    public function getNoOfDays() {
	if ($this->getFromDate() == "" && $this->getToDate() == "") {
	    return self::$_noOfDays;
	} else {
	    return round($this->getRange() / 86400);
	}
    }

    public function getRange() {
	if ($this->getFromDate() == "" && $this->getToDate() == "") {
	    if (isset(self::$_noOfDays)) {
		return 86400 * $this->getNoOfDays();
	    } else {
		self::$_noOfDays = 30;
		return 86400 * $this->getNoOfDays();
	    }
	} else {
	    return $this->getUnixToDate() - $this->getUnixFromDate();
	}
    }

    public function getUnixFromDate() {
	if ($this->getFromDate() != "") {
	    return strtotime($this->getFromDate());
	} else {
	    return $this->getUnixToDate() - $this->getRange();
	}
    }

    public function getUnixToDate() {
	if ($this->getToDate() != "") {
	    /*
	     * We are adding 86399sec (1day - 1sec) so unixToDate returns 11:59 PM(End of Day)
	     * instead of 12:00 AM(Begining of day)
	     */
	    return strtotime($this->getToDate()) + (86399);
	} else {
	    return strtotime('today') + 86399;//86400;
	}
    }

}

?>
