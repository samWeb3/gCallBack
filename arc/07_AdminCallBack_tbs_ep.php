<?php
//Include the PS_Pagination class
require_once '../class/gfAdminCallBack.class.php';
require_once '../FirePHP/firePHP.php';
//Set the Debugging mode to True
Debug::setDebug(true);

try {
    $adminCallBack = new AdminCallBack();

    //Check if Callback link has been clicked
    if ((isset($_GET['enq_id']))) {
	$adminCallBack->updateCallBackStatus($_GET['enq_id']);
    }

    $TotalCB = $adminCallBack->countTotCB();
    $AnsCB = $adminCallBack->countAnsCB();
    $UnAnsCB = $adminCallBack->countUnAnsCB();

    $callBackTableSet = $adminCallBack->viewPaginateCallBacks(10, 10);
} catch (Exception $ex) {
    echo $ex->getMessage();
}
?>   
<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/callback.css" rel="stylesheet">
	<link href="../css/easypaginate.css" rel="stylesheet">
    </head>
    <body>
	<div id="container">

	    <ul id="items">
		<li>
		    <h3>Total Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><?php echo $TotalCB ?></span></p>

		</li>

		<li>
		    <h3>Answered Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><?php echo $AnsCB ?></span></p>
		</li>

		<li>
		    <h3>Unanswered Callbacks</h3>    	
		    <p class="dashboard"><span class="data"><?php echo $UnAnsCB ?></span></p>
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
	    echo $callBackTableSet;
	    ?>


	</div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/jquery.tablesorter.min.js"></script>
	<script >
	    $(function() {
		$("table#CallBackTable").tablesorter({ sortList: [[0,1]] });
	    });
	</script>
	<script type="text/javascript" src="../js/easypaginate.js"></script>
	<script type="text/javascript">	
	    jQuery(function($){
		$('ul#items').easyPaginate({
		    step:3
		});
	    });        
	</script>
	<script type="text/javascript" src="../js/recordFilter.js"></script>

    </body>
</html>
