<?php
//require_once 'class/gfCRUD.class.php';
require_once 'class/gfCallBackStats.class.php';
require_once 'FirePHP/firePHP.php';
//Set the Debugging mode to True
Debug::setDebug(true);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
	<meta charset="utf-8">
	<title>jQuery UI Datepicker - Select a Date Range</title>
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />	
	<link href="css/dr/demos.css" rel="stylesheet" />	
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/callback.css" rel="stylesheet" />
	
	
	

	<script>
	    //need this until php sets the value
	    var dayRange = new Array();
	    var callBackRec = new Array();//To hold all request for Callback
	    var ansCBRec = new Array();//To hold answered callback records
	</script>

    </head>
    <body>
	<?php
	$cbStats = new CallBackStats();
	if (isset($_POST['dateRange'])) {
	    
	    $fromDate = strtotime($_POST['fromDate']);	    

	    //we add one day (86400sec) to a toDate to get PM
	    $toDate = strtotime($_POST['toDate']) + 86399;

	   //to calculate how many days we need range
	    $range = $toDate - $fromDate;

	    //get the number of days
	    $noOfDays = round($range/86400);	   

	    $cbStats->getRecords($toDate, $fromDate, $noOfDays);
	    //getRecords($toDate, $fromDate, $noOfDays);
	     
	} else {
	    //Get the today end day 
	    $toDate = strtotime('today') + 86400;
	    //calculate the range for 30 days
	    $range = 86400 * 30;
	    //Deduct $range from $toDate
	    $fromDate = $toDate - $range;
	    $noOfDays = round($range / 86400);	 	           
	    
	    $cbStats->getRecords($toDate, $fromDate, $noOfDays);	    
	}	
	?>

	<form action="Date_Range_stats_3.php" method="post">
	    <input type="text" id="from" name="from"/>
	    <input type="text" id="to" name="to"/>
	    <input type="hidden" id="fromDate" name="fromDate" />
	    <input type="hidden" id="toDate" name="toDate" />
	    <input type="submit" id="date" name="dateRange" value="Display" />
	</form>

	<div id="statPlaceholder" style="width:818px;height:300px"></div>

	<p id="tooltip"><input id="enableTooltip" type="checkbox" checked>Enable tooltip</p>
	
	
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" 
	    type="text/javascript" charset="utf-8"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" 
	    type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.datepicker.js" type="text/javascript" charset="utf-8"></script>

	<!--For Generating stats-->
	<!--for the support on IE7 and IE8-->
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/stat/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.symbol.js"></script>
	<script language="javascript" type="text/javascript" src="js/stat/jquery.flot.stack.js"></script>

	<script>
	    $(function() {
		var dates = $( "#from, #to" ).datepicker({
		    defaultDate: "+1w",
		    changeMonth: true,
		    numberOfMonths: 1,
		    onSelect: function( selectedDate ) {
			var option = this.id == "from" ? "minDate" : "maxDate",
			instance = $( this ).data( "datepicker" ),
			date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			    $.datepicker._defaults.dateFormat,
			selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		    }
		});
	    });
		
	    $(document).ready(function(){
		$('#date').click(function() {	    
		    $('#fromDate').val($('#from').val());
		    $('#toDate').val($('#to').val());
		});
	    });
	
	</script>
	
	
	
	<script language="javascript" type="text/javascript" src="js/callbackStats.js"></script>
    </body>
</html>
