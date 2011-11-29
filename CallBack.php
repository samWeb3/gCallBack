<?php
require_once 'class/gfCallBackForm.class.php';
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
	    fieldset, input, select, textarea, button, table, th, td, pre {
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


	</style>

    </head>

    <?php
    if (isset($_POST['request_callback'])) {

	$fname = $_POST['user_fname'];
	$email = $_POST['user_email'];
	$tel = $_POST['user_tel'];
	$enquiry = $_POST['user_enquiry'];
	try {
	    $cbf = new CallBackForm($fname, $email, $tel, $enquiry);
	    
	    //$result = $cbf->addCallBackRequest($fname, $email, $tel, $enquiry);

	    /*if ($result) {
		echo "Row inserted successfully!";
	    } else {
		echo "Unable to insert row";
	    }*/
	} catch (Exception $e) {
	    echo $e->getMessage();
	}

	 
    }
    ?>

    <body>
	<h1>CallBack Form</h1>
	<form action="CallBack.php" method="POST">
	    <ul>
		<li>
		    <span class="leftWidth">First Name: </span><input type="text" value="" maxlength="32" size="20" name="user_fname" />
		</li>		
		<li>
		    <span class="leftWidth">Email: </span><input type="text" value="" maxlength="96" size="20" name="user_email" />
		</li>
		<li>
		    <span class="leftWidth">Telephone via: </span><input type="text" value="" maxlength="96" size="20" name="user_tel" />
		</li>
		<li>
		    <span class="leftWidth">Nature of enquiry (what would you like us to call you about?): </span><textarea maxlength="250" cols="40" name="user_enquiry" rows="6"></textarea>
		</li>
		<li><span class="leftWidth">&nbsp; </span>
		    <input class="buttonBackground" type="submit" value="Request Callback" name="request_callback" />
		    <input class="buttonBackground" type="reset" value="Reset" id="reset" name="reset">
		</li>
	    </ul>
	</form>
    </body>
</html>
