<?php
require_once 'class/gfDatePickerDashboard.class.php';
require_once 'class/gfDatePickerStatistics.class.php';

$dpd = new DatePickerDashboard("01/12/2012", "01/12/2012", "display");

echo "To Date: ".$dpd->getToDate()."<br />";
echo "From Date".$dpd->getFromDate()."<br />";
echo "Range: ".$dpd->getDateRange()."<br />";
echo "Unix To Date: ".$dpd->getUnixToDate()."<br />";
echo "Unix From Date: ".$dpd->getUnixFromDate()."<br />";

echo "<br /><br />";

/*$dps = new DatePickerStatistics(20);
echo $dps->customStats("01/12/2012", "01/12/2012");

echo "To Date: ".$dps->getToDate()."<br />";
echo "From Date".$dps->getFromDate()."<br />";

echo "No of Days: ".$dps->getNoOfDays()."<br />";
echo "Range: ".$dps->getRange()."<br />";
echo "Unix To Date: ".$dps->getUnixToDate()."<br />";
echo "Unix From Date: ".$dps->getUnixFromDate()."<br />";
 * */

$dps = new DatePickerStatistics();

$dps->setNoOfDays(31);
echo "No of Days:".$dps->getNoOfDays()."<br />";

echo "Unix To Date: ".$dps->getUnixToDate()."<br />";
echo "Range: ".$dps->getRange()."<br />";
echo "Unix From Date: ".$dps->getUnixFromDate()."<br />";
echo "No of Days:".$dps->getNoOfDays()."<br /><br />";
$dps->setFromDate("01/01/2012");
$dps->setToDate("01/18/2012");
echo "Set From Date: ".$dps->getFromDate()."<br >";
echo "Set to Date: ".$dps->getToDate()."<br />";



echo "Range: ".$dps->getRange()."<br />";
echo "No of Days:".$dps->getNoOfDays()."<br /><br />";
	
	
/*$date = new DateRange("01/12/2012", "01/12/2012", "display");
$fromDate = $date->getFromDate();
$ukFromDate = strtotime(21/10/2012);
echo $ukFromDate;
echo "From Date: ".$fromDate."<br />";
echo "To Date: ".$date->getToDate()."<br />";

echo "Unix From Date: ".date(strtotime($fromDate))."<br />";
echo "To Date: ".$date->getUnixToDate()."<br />";
echo "Range: ".$date->getDateRange()."<br />";
*/



?>
