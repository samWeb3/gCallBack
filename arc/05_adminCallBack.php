<?php
    //Include the PS_Pagination class
    require_once 'gfAdminCallBack_bak.class.php';    
    require_once '../FirePHP/firePHP.php';
    
    //Set the Debugging mode to True
    Debug::setDebug(true);
?>   

<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css">
	    html, body, div, ol, ul, dl, dd, dt, fieldset, p, form, h1, h2, h3, h4, h5, iframe, blockquote, pre, img, label, legend, strong, span, em, table, caption, tbody, tfoot, thead, tr, th, td {
		font-family: inherit;
		font-style: inherit;
		font-weight: inherit;
		margin: 0;
		padding: 0;
	    }
	    body {
		font: 75%/1.6em "Lucida Grande",Verdana,Geneva,Helvetica,Arial,sans-serif;
	    }
	    ol, ul {
		list-style: none outside none;
	    }
	    body {
		font-family: Arial;
		font-size: 12px;
	    }
	    ul li span.leftWidth{
		float: left;
		width: 125px !important;
		list-style: none;		
	    }
	    li, h1 { padding-bottom: 20px;}
	    .warning {
		color: #f00;
		font-weight: bold;
	    }
	</style>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" charset="utf-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/recordFilter.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
<?php
    try {
	$adminCallBack = new AdminCallBack();
	
	//Check if Callback link has been clicked
	if ((isset($_GET['enq_id']))){	    	
	    $adminCallBack->updateCallBackStatus($_GET['enq_id']);
	}
	/*if ((isset($_GET['ans_CB']))){	    	
	    FB::info("Answered CB");
	    $adminCallBack->viewPaginateCallBacks(6, 10, '1', 'ans_CB');
	} else if ((isset($_GET['unAns_CB']))){
	    FB::info("Unanswered CB");
	    $adminCallBack->viewPaginateCallBacks(6, 10, '0', 'unAns_CB');
	} else {*/
	    $TotalCB = $adminCallBack->countTotCB();
	    $AnsCB = $adminCallBack->countAnsCB();
	    $UnAnsCB = $adminCallBack->countUnAnsCB();

	    echo "Total Call Back:  $TotalCB <br>";
	    //$adminCallBack->countAnsCB();
	    echo "Answered Call Back: $AnsCB <br>";
	    echo "Unanswered Call Back: $UnAnsCB <br>";

	    echo $adminCallBack->viewPaginateCallBacks(6, 10);	
	//}
	
	
    } catch (Exception $ex){
	echo $ex->getMessage();
    }
?>
    </body>
</html>

