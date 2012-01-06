<?php
require_once 'class/gfCRUD.class.php';
require_once 'FirePHP/firePHP.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
	<meta charset="utf-8">
	<title>jQuery UI Datepicker - Select a Date Range</title>
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />	
	<link href="css/dr/demos.css" rel="stylesheet" />	
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

	<script>
	    //need this until php sets the value
	    var dayRange = new Array();
	    var callBackRec = new Array();//To hold all request for Callback
	    var ansCBRec = new Array();//To hold answered callback records
	</script>

    </head>
    <body>
	<?php
	if (isset($_POST['dateRange'])) {
	    
	    $cb = new CRUD();
	    $cb->username = 'root';
	    $cb->password = 'root123';
	    $cb->dsn = "mysql:dbname=griff;host=localhost";

	    $fromDate = strtotime($_POST['fromDate']);	    

	    //we add one day (86400sec) to a toDate to get PM
	    $toDate = strtotime($_POST['toDate']) + 86400;

	   //to calculate how many days we need range
	    $range = $toDate - $fromDate;

	    //get the number of days
	    $noOfDays = $range / 86400;	   

	    //Doing this as we want php value inside javascript array dayRange and callBackRec
	    echo "<script type='text/javascript' language='JavaScript'>";

	    //Iterate through the no of Days and print Date + no of records
	    for ($i = 0; $i < $noOfDays; $i++) {

		//Starts with first day
		//$date = date("d M Y h:i:s A", $fromDate);		
		//$day = date("d", $fromDate);
		
		//add 86400sec to get the end of the day
		$fromDateEnd = $fromDate + 86400;
		$resCB = $cb->rawSelect("select count(*) from callbackuserenquiry where callBackDate > 
			$fromDate and callBackDate < $fromDateEnd");

		$countRec;
		
		//get a record for a day
		foreach ($resCB as $num) {
		    foreach ($num as $k => $v) {
			$countRec = $v;
		    }
		}
		
		$resAnsCB = $cb->rawSelect("select count(*) from callbackuserenquiry where callBackDate > 
			$fromDate and callBackDate < $fromDateEnd and cb_status = 1");
		
		$countAnsRec;
		
		//get a answered record for a day
		foreach ($resAnsCB as $num){
		    foreach ($num as $k => $v){
			$countAnsRec = $v;
		    }
		}

		//Add one more day (86400sec) to the first day 
		$fromDate = $fromDate + 86400;		
		//printing the php var in a javascript arrary we declared earlier
		echo "dayRange.push($fromDate * 1000);";//need to multiply unix timestamp by 1000 to get javascript timestamp
		echo "callBackRec.push($countRec);";
		echo "ansCBRec.push($countAnsRec);";
	    }
	    echo "</script>";	   
	}
	?>

	<form action="Date_Range_stats.php" method="post">
	    <input type="text" id="from" name="from"/>
	    <input type="text" id="to" name="to"/>
	    <input type="hidden" id="fromDate" name="fromDate" />
	    <input type="hidden" id="toDate" name="toDate" />
	    <input type="submit" id="date" name="dateRange" value="Display" />
	</form>

	<div id="placeholder" style="width:818px;height:300px"></div>

	<script>	
	    
	    console.log("Day Range: " + dayRange);
	    console.log("Call Back Record: " + callBackRec);
	    console.log("Answered Call Back Records: " + ansCBRec);
	
	    if (dayRange != 0 && callBackRec != 0 && ansCBRec != 0){ //to avoid js error at begining
		
		/**************************************************
		 * CODE BELOW FOR GENERATING STATISTICS
		 **************************************************/
		
		//We get following from the php code above
		//var dayRange = new Array(6, 7, 8, 9, 10, 11, 12);
		//var callBackRec = new Array(66, 44, 12, 15, 60, 30, 22);
	    
		//To be Used when data for answered call retrieved
		//var answeredRec = new Array(44, 30, 10, 10, 55, 30, 21);
	    
		/**
		 * Construct the multidimentional array based on the day and record passed!
		 * 
		 * @param int day   no of days retrieved from date range
		 * @param int rec   no of record for each day
		 */
		function multiDimenArray(day, rec){
		    var nOfR = day.length;
	   
		    //this unit is a array that holds day and record for e.g [6, 66]
		    var unit = new Array();

		    //data is multidimentional array that holds no of unit array for 
		    //e.g., [[6, 66], [7, 44]]
		    var data = new Array();

		    for (i=0; i<nOfR; i++){
			unit = new Array(day[i], rec[i]);
			data.push(unit);
			console.log("Data: " + data);
		    }	       
		    return data;
		}	   
		console.log("Day Range: " + dayRange);
	   
		//Following Script is for generating callback statistics
		var callbackData = multiDimenArray(dayRange, callBackRec);		
	    
		//To be Used when data for answered call retrieved
		var answeredData = multiDimenArray(dayRange, ansCBRec);	
	    	    
		//console.log("Returned CallBack Data: ", callbackData);	
		console.log("Returned CallBack Data: ", answeredData);	
					    	    
		var data = [ 
		    { 
			color: "#CB413B", 
			label: "CallBack", 		    
			data: callbackData,
			fill: true, 
			fillColor: "rgba(255, 0, 0, 0.1)"			
		    }//To be Used when data for answered call retrieved: 
		    , 
		    { 
			color: "green", 
			label: "Answered", 		     
			data: answeredData
		    }
		]; 
		var options = {
		    series: {			
			lines: { 
			    show: true, 
			    lineWidth: 4			    
			},
			points: { 
			    show: true,
			    radius: 4, 			    
			    fillColor: "#ffffff", 			    
			    borderColor: "#ffffff"
			}
			
		    },
		    grid: {
			    borderWidth: 2, 
			    borderColor: "#999999", 
			    hoverable: true, 
			    clickable: true
			}, 		    
		    xaxis: {			
			mode: "time"
			//timeformat: "%0d/%m/%y"			
		    }
		};
	
		$.plot($("#placeholder"), data, options);
	    }
	</script>
    </body>
</html>
