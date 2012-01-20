<?php
abstract class DatePickerSuper {
    private $_fromDate;
    private $_toDate;
    //private $_dateRange;   //This date range is very confusing change it! 
    
    public function __construct($fromDate="", $toDate=""/*, $dateRange=""*/) {
	if ($fromDate != "" && $toDate != "" /*&& $dateRange != ""*/) {
	    $this->_fromDate = $fromDate;
	    $this->_toDate = $toDate;	   
	    //$this->_dateRange = $dateRange;
	}
    }
    
    public function getFromDate(){
	return $this->_fromDate;
    }
    
    public function getToDate(){
	return $this->_toDate;
    }
    
    public function setToDate($toDate){
	$this->_toDate = $toDate;
    }
    
    public function setFromDate($fromDate){
	$this->_fromDate = $fromDate;
    }
    
    /*public function getDateRange(){
	return $this->_dateRange;
    }*/
    
    abstract public function getUnixFromDate();
    
    abstract public function getUnixToDate();
}
?>
