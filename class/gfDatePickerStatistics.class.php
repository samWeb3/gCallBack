<?php
require_once 'gfDatePickerSuper.class.php';

class DatePickerStatistics extends DatePickerSuper{
    private $_noOfDays;  

    public function __construct($fromDate = "", $toDate = "") {
	parent::__construct($fromDate, $toDate);	
    }
    
    public function getNoOfDays() {
	if (parent::getFromDate() == "" && parent::getToDate() == ""){	    
	    return $this->_noOfDays;
	} else {	   
	    return round($this->getRange() / 86400);
	}	
    }
    
    public function setNoOfDays($noOfDays){
	$this->_noOfDays = $noOfDays;
    }
    
    public function getRange(){	
	if (parent::getFromDate() == "" && parent::getToDate() == ""){
	    if (isset($this->_noOfDays)){
		return 86400 * $this->getNoOfDays();
	    } else {
		$this->_noOfDays = 30;
		return 86400 * $this->getNoOfDays();
	    }
	} else {
	    return $this->getUnixToDate() - $this->getUnixFromDate();
	}
    }
    
    public function getUnixFromDate(){
	if (parent::getFromDate() != ""){
	    return strtotime(parent::getFromDate());
	} else {
	    return $this->getUnixToDate() - $this->getRange();
	}	
    }
    
    public function getUnixToDate(){	
	if (parent::getToDate() != ""){	    
	    /*
	     * We are adding 86399sec (1day - 1sec) so unixToDate returns 11:59 PM(End of Day)
	     * instead of 12:00 AM(Begining of day)
	     */
	    return strtotime(parent::getToDate()) + (86399);
	} else {
	    return strtotime('today') + 86400;
	}	
    }
}

?>
