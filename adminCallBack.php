<?php
    //Include the PS_Pagination class
    require_once 'class/gfAdminCallBack.class.php';    
    require_once 'FirePHP/firePHP.php';
    ob_start();
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

	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/recordFilter.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body>
<?php

    $firephp = FirePHP::getInstance(true);
    fb($var);
    fb($var, 'Label');
    Fb::log("log message");
    Fb::info("information");
    Fb::warn("Warning");

    try {
	$adminCallBack = new AdminCallBack();
	$result = $adminCallBack->viewPaginateCallBacks(6, 5);	
    } catch (Exception $ex){
	echo $ex->getMessage();
    }
?>
    </body>
</html>

