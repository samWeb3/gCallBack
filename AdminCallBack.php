<?php
//Include the PS_Pagination class
require_once 'class/gfAdminCallBack.class.php';
require_once 'class/gfCallBackStats.class.php';
require_once 'FirePHP/firePHP.php';
require_once 'class/gfDatePicker.class.php';
require_once 'class/gfDebug.class.php';

//Set the Debugging mode to True
Debug::setDebug(false);

$crud = new CRUD();
?>  
<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />
	<link href="css/dr/demos.css" rel="stylesheet" />
	<link href="css/callback.css" rel="stylesheet" />
	<link href="css/hacks.css" rel="stylesheet" />
	<link href="css/bootstrap.css" rel="stylesheet" />	
	<link href="css/easypaginate.css" rel="stylesheet" />
	    
	<script>
	    //need this until php sets the value, Therefore need here
	    var dayRange = new Array();
	    var totalRec = new Array();//To hold all request for Callback
	    var ansRec = new Array();//To hold answered callback records
	</script>
    </head>
    <body>
	<?php
	$instanceId = 151; //instance of partner
	$numLink = 10; //number of link
	
	DatePicker::setNoOfDays(31);
	$datePicker = new DatePicker($fromDate, $toDate, $dateRangeSet);

	$cbStats = new CallBackStats($crud, $instanceId, $datePicker);
	try {

	    //Get the From and To Date Range
	    if (isset($_GET['dateRangeSet'])) {
		
		if (isset($_GET['fromDate']) && isset($_GET['toDate'])) {
		    $fromDate = $_GET['fromDate'];
		    $toDate = $_GET['toDate'];

		    $ukFromDate = date("d M Y h:i:s A", strtotime($fromDate));
		    /*
		     * We are adding 86399sec (1day - 1sec) so unixToDate returns 11:59 PM(End of Day)
		     * instead of 12:00 AM(Begining of day)
		     */
		    $ukToDate = date("d M Y h:i:s A", strtotime($toDate) + (86399));
		}

		$dateRangeSet = $_GET['dateRangeSet'];

		if ($fromDate != "" && $toDate != "") {		    
		    $infoMessage = $datePicker->displayDateRangeMsg($ukFromDate, $ukToDate);
		    $cbStats->customStats($_GET['fromDate'], $_GET['toDate']);
		} else {		    		    
		    $infoMessage = $datePicker->displayDateRangeMsg($datePicker->getUkFromDate(), $datePicker->getUkToDate());
		    $cbStats->monthStats();
		}
	    } else {				
		$infoMessage = $datePicker->displayDateRangeMsg($datePicker->getUkFromDate(), $datePicker->getUkToDate());
		$cbStats->monthStats();
	    }

	    $adminCallBack = new AdminCallBack($crud, $instanceId, $datePicker);

	    //Check if Callback link has been clicked
	    if ((isset($_GET['enq_id']))) {
		$adminCallBack->updateCallBackStatus($_GET['enq_id']);
		if ($fromDate != "" && $toDate != "") {		    
		    $infoMessage = $datePicker->displayDateRangeMsg($ukFromDate, $ukToDate);
		    $cbStats->customStats($_GET['fromDate'], $_GET['toDate']);
		} else {		    		    
		    $infoMessage = $datePicker->displayDateRangeMsg($datePicker->getUkFromDate(), $datePicker->getUkToDate());
		    $cbStats->monthStats();
		}
	    }
	    if ((isset($_GET['row_pp']))) {
		if (empty($_GET['row_pp'])) {
		    $errorMessage = "Please enter the number of records to be displayed";
		    $inputNum = 10;
		} else if (!is_numeric($_GET['row_pp'])) {
		    $errorMessage = "Please enter numeric values!";
		    $inputNum = 10;
		} else if ($_GET['row_pp'] <= 0) {
		    $errorMessage = "Record number should be greater than or equal to 1";
		    $inputNum = 10;
		} else {
		    $inputNum = $_GET['row_pp'];
		}
	    } else {
		$inputNum = 10;
	    }

	    $TotalCB = $adminCallBack->countTotCB();
	    $AnsCB = $adminCallBack->countAnsCB();
	    $UnAnsCB = $adminCallBack->countUnAnsCB();

	    if ((isset($_GET['cbStatus']))) {
		$cbStatus = $_GET['cbStatus'];
		if ($cbStatus == 0) {//Display UnAnswered CallBacks	   
		    $resultSet = $adminCallBack->viewPaginateCallBacks($inputNum, $numLink, '0');
		} else if ($cbStatus == 1) {//Display Answered CallBacks	   
		    $resultSet = $adminCallBack->viewPaginateCallBacks($inputNum, $numLink, '1');
		} else if ($cbStatus == 2) { // Total CallBacks
		    $resultSet = $adminCallBack->viewPaginateCallBacks($inputNum, $numLink, '2');
		}
	    } else { //Display Total Callbacks
		$resultSet = $adminCallBack->viewPaginateCallBacks($inputNum, $numLink, '2');
	    }
	} catch (Exception $ex) {
	    $errorMessage = $ex->getMessage();
	}
	?>	
	<div id="container">
	    <div id="datePickerHolder" class="group">
		<div id="switchDisplay">
		    <button id="viewStatBtn" class="btn default pull-left">View Statistics</button>		    
		    <button id="viewDashboardBtn" class="btn default pull-left">View Dashboard</button>
		</div>

		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" class="pull-right">
		    <!--Used by JavaScript-->
		    <input class="medium" type="text" id="from" name="from" placeholder="Date From" />
		    <input class="medium" type="text" id="to" name="to" placeholder="Date To" />

		    <!--Used by PHP-->
		    <input type="hidden" id="fromDate" name="fromDate"/>
		    <input type="hidden" id="toDate" name="toDate"/>

		    <input type="submit" id="date" name="dateRangeSet" value="Display" class="btn default"/>
		</form>
	    </div>

	    <?php if (isset($infoMessage)) { ?>
		<div class='alert-message info fade in clear' id="dateRangeMsg" data-alert='alert'><a class='close' href='#'>&times;</a>
		    <?php echo "$infoMessage"; ?>
		</div>
	    <?php } ?>
	    
	    <div id="viewStatPnl">
		<div id="statPlaceholder"></div>
	    </div>	    

	    <p id="tooltipContainer"><input id="enableTooltip" type="checkbox" checked>Enable Tooltip</p>

	    <div id="viewDashboardPnl">
		<ul id="items">
		    <li>
			<h3>Total Callbacks</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?cbStatus=2&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"'; ?>" class="dashboardLink" id="totCB">
				    <?php echo $TotalCB ?>
				</a>
			    </span>
			</p>
		    </li>

		    <li>
			<h3>Answered Callbacks</h3>    	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?cbStatus=1&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"'; ?>" class="dashboardLink" id="ansCB">
				<?php echo $AnsCB ?>
				</a>
			    </span>
			</p>
		    </li>

		    <li>
			<h3>Unanswered Callbacks</h3>   <!-- {_Unanswered Callbacks} Language Variable--> 	
			<p class="dashboard">
			    <span class="data">
				<a href="<?php echo $_SERVER['PHP_SELF'] . "?cbStatus=0&fromDate=".$datePicker->getFromDate()."&toDate=".$datePicker->getToDate()."&dateRangeSet=".$datePicker->getDateRangeSet().'"' ?>" class="dashboardLink" id="unAnsCB">
				    <?php echo $UnAnsCB ?>
				</a>
			    </span>
			</p>		  
		    </li>
		</ul>
	    </div> 
	    
	    <?php if (isset($errorMessage)) { ?>
		<div class='alert-message warning fade in' data-alert='alert'><a class='close' href='#'>&times;</a>
		    <?php echo "$errorMessage"; ?><!-- {$errorMessage}-->
		</div>
	    <?php } ?>
	    
	    <div id="middle">
		<div id="search" class="group">
		    <div class="pull-left">
			<label for="filter">Filter Record: </label> 
			<input type="text" name="filter" value="" id="filter">			
		    </div>
		    <span id="dateFilter" class="pull-right">
			    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" class="pull-right">
				<input type="hidden" name="cbStatus" id="cbStatus" value="<?php echo $adminCallBack->getCbStatus();?>">				
				<input type="hidden" name="fromDate" value="<?php echo $datePicker->getFromDate();?>">
				<input type="hidden" name="toDate" value="<?php echo $datePicker->getToDate();?>">
				<input type="hidden" name="dateRangeSet" value="<?php echo $datePicker->getDateRangeSet();?>">	
				<label id="disRecHelpText" class="help-inline">Enter valid number and press &#60;ENTER&#62; !&nbsp;</label>
				<input id="disRecord" class="input-small span2" type="text" size="15" placeholder="Display Record" name="row_pp">				
			    </form>
			</span>
		</div>	    
		
		<table class="zebra-striped tablesorter" id="CallBackTable">
		    <thead>
		    <tr>			
			<th>Date </th>
			<th>Name </th>
			<th>Email </th>
			<th>Phone No </th>
			<th>Enquiry</th>
			<th>Status</th>
		    </tr>
		    </thead>
		    <tbody>			
			<?php if ($resultSet) { ?>
			    <?php foreach ($resultSet as $r) { ?>
				<tr>
				    <td>
					<!--use UnixTimeStamp to sort date properly, then hide it using css-->
					<span class="unixDate"><?php echo $r[callBackDate]; ?></span>
					<span class="qDate"><?php echo $datePicker->convertUnixToDMY($r[callBackDate]); ?></span>
					<br />
					<span class="small unHighlight qtime"><?php echo $datePicker->convertUnixToTime($r[callBackDate]); ?></span>
				    </td>
				    <td><?php echo $r[name]; ?></td>
				    <td><?php echo $r[email]; ?></td>
				    <td><?php echo $r[telephone]; ?></td>
				    <td><?php echo $r[enquiry]; ?></td>
				    <td>
					<?php if ($r[cb_status] == 0) { ?>
					    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?
						enq_id=<?php echo $r[enq_id]; ?>
						&page=<?php echo $adminCallBack->getPageNo();?>
						&row_pp=<?php echo $adminCallBack->getRecordsPerPage(); ?>
						&cbStatus=<?php echo $adminCallBack->getCbStatus(); ?>
						&param1=value1
						&param2=value2
						&fromDate=<?php echo $datePicker->getFromDate(); ?>
						&toDate=<?php echo $datePicker->getToDate(); ?>
						&dateRangeSet=<?php echo $datePicker->getDateRangeSet() ?>"
					        class="btn danger">Callback</a>
					<?php } else { ?>
						<a href="#" class="btn success disabled">Answered</a>
					<?php } ?>				    
				    </td>
				</tr>			
			    
			<?php } } else { ?>
				<tr>
				    <td colspan="6">
					<div class='alert-message block-message error' data-alert='alert'>
					    <div class="block-message-header">Oops! Records not available!</div>
					    <div class="block-message-body">Please enter valid date range and try again...</div>					    
					</div>
				    </td>
				</tr>			    
			<?php } ?>
		    </tbody>
		</table>
		
		<div class="cPaginator">
		    <?php echo $adminCallBack->getPaginatorNav(); ?>
		</div>
	    </div>
	    
	</div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script><!--For Date Range Picker-->
	<script src="js/jquery.tablesorter.min.js"></script>	
	<script type="text/javascript" src="js/jquery.cookies.2.2.0.js"></script>
	<script type="text/javascript" src="js/easypaginate.js"></script>		
	<script type="text/javascript" src="js/bootstrap-alerts.js"></script>		
	<script type="text/javascript" src="js/recordFilter.js"></script>	
	
	<!--for Date Range Picker-->
	<script src="js/dr/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.datepicker.js" type="text/javascript" charset="utf-8"></script>

	<!--For Generating stats-->
	<!--for the support on IE7 and IE8-->
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/stat/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.symbol.js"></script>
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.stack.js"></script>
	<script language="javascript" type="text/javascript" src="js/callbackStats.js"></script>
	<script language="javascript" type="text/javascript" src="js/adminCallBack.js"></script>	
    </body>
</html>
