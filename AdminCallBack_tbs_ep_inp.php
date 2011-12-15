<?php
//Include the PS_Pagination class
require_once 'class/gfAdminCallBack.class.php';
require_once 'FirePHP/firePHP.php';
//Set the Debugging mode to True
Debug::setDebug(true);

try {
    //Get the From and To Date Range
    if (isset($_GET['dateRange'])){	
	$fromDate = $_GET['fromDate'];
	$toDate = $_GET['toDate'];
	$dateRange = $_GET['dateRange'];
	
	$ukFromDate = date("d M Y", strtotime($fromDate));
	$ukToDate = date("d M Y", strtotime($toDate));
	
	if ($fromDate != "" && $toDate != ""){
	    $infoMessage = "Displaying Callback Records From <strong>$ukFromDate</strong> to <strong>$ukToDate</strong>";
	} else {
	    $infoMessage = "Displaying All Callback Records";
	}		
    } else {
	$infoMessage = "Displaying All Callback Records";
    }
    
    $adminCallBack = new AdminCallBack($fromDate, $toDate, $dateRange);

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

    $TotalCB = $adminCallBack->countTotCB();
    $AnsCB = $adminCallBack->countAnsCB();
    $UnAnsCB = $adminCallBack->countUnAnsCB();
    
    if((isset($_GET['cbStatus']))){
	$cbStatus = $_GET['cbStatus'];	
	if ($cbStatus == 0){//Display UnAnswered CallBacks	   
	    $callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '0');
	} else if ($cbStatus == 1){//Display Answered CallBacks	   
	    $callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '1');
	} else if ($cbStatus == 2){ // Total CallBacks
	    $callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '2');
	}
    } else { //Display Total Callbacks
	$callBackTableSet = $adminCallBack->viewPaginateCallBacks($inputNum, 10, '2');
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
    </head>
    <body>
	
	
	<div id="container">
	    
	    <div id="dateRange">
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get" class="pull-right">
		    <!--Used by JavaScript-->
		    <input class="medium" type="text" id="from" name="from" placeholder="Date From" />
		    <input class="medium" type="text" id="to" name="to" placeholder="Date To" />

		    <!--Used by PHP-->
		    <input type="hidden" id="fromDate" name="fromDate"/>
		    <input type="hidden" id="toDate" name="toDate"/>

		    <input type="submit" id="date" name="dateRange" value="Display" class="btn default"/>
		</form>
	    </div>
	    
	    <?php
		if (isset($infoMessage)){		
		    echo "<div class='alert-message info fade in clear' data-alert='alert'><a class='close' href='#'>&times;</a>$infoMessage</div>";
		}				
	    ?>

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
		if ($callBackTableSet) {
		    echo $callBackTableSet;
		} else { // no query returned then
		    echo "<div class='alert-message error fade in' data-alert='alert'><a class='close' href='#'>&times;</a>Records not available!</div>";
		}
		
	    ?>
	</div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script><!--For Date Range Picker-->
	<script src="js/jquery.tablesorter.min.js"></script>	
	<script type="text/javascript" src="js/easypaginate.js"></script>	
	<script type="text/javascript" src="js/recordFilter.js"></script>
	<script type="text/javascript" src="js/bootstrap-alerts.js"></script>	
	<!--for Date Range Picker-->
	<script src="js/dr/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/dr/jquery.ui.datepicker.js" type="text/javascript" charset="utf-8"></script>

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
	    
	    /**************************************************
	     * Date Range Picker 
	     **************************************************/	    
	    $(function() {
		var dates = $( "#from, #to" ).datepicker({
		    defaultDate: "+1w",
		    changeMonth: true,
		    numberOfMonths: 1,
		    //dateFormat: 'dd/mm/yy',
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
