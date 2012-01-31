<?php
require_once 'class/gfCallBackForm.class.php';
require_once 'class/gfUser.class.php';
require_once 'FirePHP/firePHP.php';

//Set the Debugging mode to True
Debug::setDebug(true);
$crud = new CRUD();
$instance = new gfInstances();
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
	    .warning {
		color: #f00;
		font-weight: bold;
	    }
	</style>
    </head>

    <?php
    
    $missing = null;
    $errors = null;
    $success = null;
    
    if (filter_has_var(INPUT_POST, request_callback)){	//better than isset as it returns true even in case of an empty string 	
	try {
	    require_once 'class/gfValidator.php';
	    
	    $required = array('user_name', 'user_email', 'user_tel', 'user_enquiry');
	    
	    //retrieve the input and check whether any fields are missing	    
	    $val = new Validator($required);
	    
	    //Validate each field and generate errror
	    $val->checkTextLength('user_name', 3, 30);			   
	    $val->removeTags('user_name');
	    $val->isEmail('user_email');	    
	    $val->matches('user_tel', '/[0-9]{3,11}/');
	    $val->checkTextLength('user_enquiry', 5, 500);
	    $val->useEntities('user_enquiry');
	    
	    //check the validation test has been set for each required field
	    $filtered = $val->validateInput();
	    
	    $fname = $filtered['user_name'];
	    $email = $filtered['user_email'];
	    $tel = $filtered['user_tel'];
	    $enquiry = $filtered['user_enquiry'];
	    
	    $missing = $val->getMissing();
	    $errors = $val->getErrors();
	    
	    //if nothing is missing or no errors is thrown
	    if (!$missing && !$errors){
		try {
		    $user = new User($fname, $email, $tel);
		    $cbf = new CallBackForm($crud, $instance, $user, $enquiry);
		    
		    $fieldnameArr = array('user_name',  'user_email', 'user_tel', 'user_enquiry');
		    $cbf->resetForm($fieldnameArr);		    
		    
		} catch (Exception $e) {
		    echo $e->getMessage();
		}		
	    } else {
		if (Debug::getDebug()){
		    fb($filtered, "All Values not set", FirePHP::INFO);		    
		}		
	    }	    
	} catch (Exception $e){
	    echo $e;
	}	
    }
    ?>

    <body>
	<h1>CallBack Form</h1>
	<form action="CallBack.php" method="POST">
	    <ul>
		<li>
		    <?php 
			if (isset($errors['user_name'])) { 
			    echo '<span class="warning">' . $errors['user_name'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">First Name:</span>
			<input type="text" maxlength="32" size="20" name="user_name"
			   <?php
				//Sticky Form: The Essential Guide to Dreamweaver CS4 with CSS, Ajax, and PHP
				if (isset($missing)) { //if any field a are missing retain the info
				    //ENT_COMPAT: converts double quote to $quote; but lives single quote alone
				    echo 'value ="'.htmlentities($_POST['user_name'], ENT_COMPAT, 'UTF-8').'"';
				}				
			    ?>
			/>
		</li>		
		<li>
		    <?php 
			if (isset($errors['user_email'])) { 
			    echo '<span class="warning">' . $errors['user_email'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Email: </span>
			<input type="text" maxlength="96" size="20" name="user_email" 
			   <?php				
				if (isset($missing)) {				    
				    echo 'value ="'.htmlentities($_POST['user_email'], ENT_COMPAT, 'UTF-8').'"';
				}
			    ?>
			/>
		</li>
		<li>
		    <?php 
			if (isset($errors['user_tel'])) { 
			    echo '<span class="warning">' . $errors['user_tel'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Telephone via: </span>
		    <input type="text" maxlength="96" size="20" name="user_tel" 
			<?php				
			    if (isset($missing)) {				    
				echo 'value ="'.htmlentities($_POST['user_tel'], ENT_COMPAT, 'UTF-8').'"';
			    }
			?>						 
		    />
		</li>
		<li>
		    <?php 
			if (isset($errors['user_enquiry'])) { 
			    echo '<span class="warning">' . $errors['user_enquiry'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">Nature of enquiry (what would you like us to call you about?): </span><textarea maxlength="500" cols="40" name="user_enquiry" rows="6"><?php 
			if (isset($missing)) {			    
			    echo htmlentities($_POST['user_enquiry'], ENT_COMPAT, 'UTF-8');
			} //It's important to position the opening and closing PHP tags right up agains the <textarea> tags. Else you get unwanted whitespace in the text area.
		    ?></textarea>
		</li>
		<li><span class="leftWidth">&nbsp; </span>
		    <input class="buttonBackground" type="submit" value="Request Callback" name="request_callback" />
		    <input class="buttonBackground" type="reset" value="Reset" id="reset" name="reset">
		</li>
	    </ul>
	</form>
    </body>
</html>
