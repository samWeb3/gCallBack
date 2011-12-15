<?php
//Include the PS_Pagination class
require_once 'class/gfAdminCallBack_PREV.class.php';
require_once 'FirePHP/firePHP.php';
//Set the Debugging mode to True
Debug::setDebug(true);

try {
    $adminCallBack = new AdminCallBack();

    //Get the From and To Date Range
    if (isset($_GET['dateRange'])){
	$dateRange = $_GET['dateRange'];
	$fromDate = $_GET['fromDate'];
	$toDate = $_GET['toDate'];
    }

    //Check if Callback link has been clicked
    if ((isset($_GET['enq_id']))) {
	$adminCallBack->updateCallBackStatus($_GET['enq_id']);
    }    
    if ((isset($_GET['row_pp']))){
	if (empty($_GET['row_pp'])){
	    $errorMessage = "Please enter the number of records to be displayed";
	    $inputNum = 10;
	} else if (!is_numeric($_GET['row_pp'])){
	    $errorMessage = "Please enter numeric values!";
	    $inputNum = 10;
	} else if ($_GET['row_pp'] <= 0){
	    $errorMessage = "Record number should be greater than or equal to 1";
	    $inputNum = 10;
	} else {
	    $inputNum = $_GET['row_pp'];
	}	
    } else {
	$inputNum = 10;	
    }

    $TotalCB = $adminCallBack->countTotCB($fromDate, $toDate);
    $AnsCB = $adminCallBack->countAnsCB($fromDate, $toDate);
    $UnAnsCB = $adminCallBack->countUnAnsCB($fromDate, $toDate);
    
    if((isset($_GET['cbStatus']))){
	$cbStatus = $_GET['cbStatus'];	
	if ($cbStatus == 0){//Display UnAnswered CallBacks	   
	    $callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '0', $fromDate, $toDate, $dateRange);
	} else if ($cbStatus == 1){//Display Answered CallBacks	   
	    $callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '1', $fromDate, $toDate, $dateRange);
	} else if ($cbStatus == 2){ // Total CallBacks
	    $callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '2', $fromDate, $toDate, $dateRange);
	}
    } else { //Display Total Callbacks
	$callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '2', $fromDate, $toDate, $dateRange);
    }    
} catch (Exception $ex) {
    echo $ex->getMessage();
}
?>   
<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="css/dr/jquery.ui.all.css" rel="stylesheet" />
	<link href="css/dr/demos.css" rel="stylesheet" />
	
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/callback.css" rel="stylesheet">
	<link href="css/easypaginate.css" rel="stylesheet">
	
	
	</script>
    </head>
    <body>
	<div id="dateRange">
	    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
		<!--Used by JavaScript-->
		<input type="text" id="from" name="from"/>
		<input type="text" id="to" name="to"/>
		
		<!--Used by PHP-->
		<input type="hidden" id="fromDate" name="fromDate" />
		<input type="hidden" id="toDate" name="toDate" />
		
		<input type="submit" id="date" name="dateRange" value="Display" />
	    </form>
	</div>
	
	<div id="container">

	    <ul id="items">
		<li>
		    <h3>Total Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><a href="<?php echo $_SERVER['PHP_SELF']."?cbStatus=2&fromDate=$fromDate&toDate=$toDate&dateRange=$dateRange";?>" class="dashboardLink" id="totCB"><?php echo $TotalCB ?></a></span></p>

		</li>

		<li>
		    <h3>Answered Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><a href="<?php echo $_SERVER['PHP_SELF']."?cbStatus=1&fromDate=$fromDate&toDate=$toDate&dateRange=$dateRange"?>" class="dashboardLink" id="ansCB"><?php echo $AnsCB ?></a></span></p>
		</li>

		<li>
		    <h3>Unanswered Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><a href="<?php echo $_SERVER['PHP_SELF']."?cbStatus=0&fromDate=$fromDate&toDate=$toDate&dateRange=$dateRange"?>" class="dashboardLink" id="unAnsCB"><?php echo $UnAnsCB ?></a></span></p>
		    <?php //$_SERVER['PHP_SELF']."?&UnAnsCB='UnAnsCB'"?>
		</li>

		<li>
		    <h3>Total Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><?php echo $TotalCB ?></span></p>
		</li>

		<li>
		    <h3>Unanswered Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><?php echo $UnAnsCB ?></span></p>
		</li>
	    </ul>

	    <?php
		if (isset($errorMessage)){		
		    echo "<div class='alert-message warning fade in' data-alert='alert'><a class='close' href='#'>&times;</a>$errorMessage</div>";
		} 		
		echo $callBackTableSet;
	    ?>
	</div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.tablesorter.min.js"></script>	
	<script type="text/javascript" src="js/easypaginate.js"></script>	
	<script type="text/javascript" src="js/recordFilter.js"></script>
	<script type="text/javascript" src="js/bootstrap-alerts.js"></script>	

	<script type="text/javascript">
	    /**************************************************
	     * Sort Table
	     **************************************************/
	    $(function() {
		$("table#CallBackTable").tablesorter({ sortList: [[0,1]] });
	    });
	    
	    /**************************************************
	     * Paginate Dashboard
	     **************************************************/
	    jQuery(function($){
		$('ul#items').easyPaginate({
		    step:3
		});
	    });
	    
	    /**************************************************
	     * Display BootStrap Alert Message
	     **************************************************/
	    $(".alert-message").alert();	    
	    
	    /**************************************************
	     * Highlight Dashboard Link Based on current Status 
	     **************************************************/
	    //Get the php variable set above
	    var cbStatus = <?php if($cbStatus==""){echo 2;}else{echo $cbStatus;}?>;	   
	    
	    switch (cbStatus){
		case 0:		    
		    $('#unAnsCB').addClass('activeLink');
		    break;
		case 1:		    
		    $('#ansCB').addClass('activeLink');		    
		    break;
		case 2: 		    
		    $('#totCB').addClass('activeLink');
		    break;
	    }
	</script>
	
	
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
	    $('#fromDate').val($('#from').val());
	    $('#toDate').val($('#to').val());
	});
    });
    </script>
    </body>
</html>
