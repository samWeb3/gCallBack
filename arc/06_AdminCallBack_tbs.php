<?php
    //Include the PS_Pagination class
    require_once '../class/gfAdminCallBack.class.php';    
    require_once '../FirePHP/firePHP.php';    
    //Set the Debugging mode to True
    Debug::setDebug(true);
    
    try {
	$adminCallBack = new AdminCallBack();
	
	//Check if Callback link has been clicked
	if ((isset($_GET['enq_id']))){	    	
	    $adminCallBack->updateCallBackStatus($_GET['enq_id']);
	}
	
	$TotalCB = $adminCallBack->countTotCB();
	$AnsCB = $adminCallBack->countAnsCB();
	$UnAnsCB = $adminCallBack->countUnAnsCB();

	$callBackTableSet = $adminCallBack->viewPaginateCallBacks(6, 10);	

    } catch (Exception $ex){
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
    </head>
    <body>
	<div id="container">
	<?php	
	    echo "Total Call Back:  $TotalCB <br>";
	    echo "Answered Call Back: $AnsCB <br>";
	    echo "Unanswered Call Back: $UnAnsCB <br><br>";
	    echo $callBackTableSet;
	?>
	</div>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/jquery.tablesorter.min.js"></script>
	<script >
	$(function() {
	    $("table#CallBackTable").tablesorter({ sortList: [[1,0]] });
	});
	</script>
	
    </body>
</html>
