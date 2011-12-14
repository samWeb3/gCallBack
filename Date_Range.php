<?php
    //Include the PS_Pagination class
    require_once 'class/gfAdminCallBack.class.php';
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
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.datepicker.js" type="text/javascript" charset="utf-8"></script>
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
	    console.log("From " + $('#from').val() + " To: " + $('#to').val());
	    $('#fromDate').val($('#from').val());
	    $('#toDate').val($('#to').val());
	});
    });
	
	</script>
</head>
<body>
<?php
    if (isset($_POST['dateRange'])){
	$adminCallBack = new AdminCallBack();
	
	$fromDate = strtotime($_POST['fromDate']);
	$toDate = strtotime($_POST['toDate']);
	
	echo "From: ".$fromDate."<br />";
	echo "To: ".$toDate."<br />";
	
	echo "Unix Time Stamp <br />";
	echo "From: ".strtotime($fromDate)."<br>";
	echo "To: ".strtotime($toDate)."<br>";
	
	$inputNum = 10;
	
	$callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '1', $fromDate, $toDate);
	
	echo $callBackTableSet;
	
    }
?>

<form action="Date_Range.php" method="post">
    <input type="text" id="from" name="from"/>
    <input type="text" id="to" name="to"/>
    <input type="hidden" id="fromDate" name="fromDate" />
    <input type="hidden" id="toDate" name="toDate" />
    <input type="submit" id="date" name="dateRange" value="Display" />
</form>

</body>
</html>
