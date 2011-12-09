<?php
require_once 'class/gfCallBackForm.class.php';
?>

<?php
    $missing = null;
    $errors = null;

    if (filter_has_var(INPUT_POST, request_callback)){	//better than isset as it returns true even in case of an empty string 	
	try {
	    require_once 'class/gfValidator.php';

	    $required = array('user_fname', 'user_email', 'user_tel', 'user_enquiry');

	    //retrieve the input and check whether any fields are missing	    
	    $val = new Validator($required);

	    //Validate each field and generate errror
	    $val->checkTextLength('user_fname', 3, 30);			   
	    $val->removeTags('user_fname');
	    $val->isEmail('user_email');	    
	    $val->matches('user_tel', '/[0-9][3,11]/');
	    $val->checkTextLength('user_enquiry', 5, 500);
	    $val->useEntities('user_enquiry');

	    //check the validation test has been set for each required field
	    $filtered = $val->validateInput();

	    $fname = $filtered['user_fname'];
	    $email = $filtered['user_email'];
	    $tel = $filtered['user_tel'];
	    $enquiry = $filtered['user_enquiry'];

	    $missing = $val->getMissing();
	    $errors = $val->getErrors();	    

	} catch (Exception $e){
	    echo $e;
	}
    }
?>

<?php
    require_once('class/recaptchalib.php');
    // Get a key from https://www.google.com/recaptcha/admin/create
    $publickey = "6LfZ5MoSAAAAAE2F-qJQSfJyu2hxKhjE2MWoSfzv";
    $privatekey = "6LfZ5MoSAAAAAMKc_l2F7qAowkQKBYIesqZz3GGX ";

    # the response from reCAPTCHA
    $resp = null;
    # the error code from reCAPTCHA, if any
    $error = null;

    # was there a reCAPTCHA response?
    if ($_POST["recaptcha_response_field"]) {
	    $resp = recaptcha_check_answer ($privatekey,
					    $_SERVER["REMOTE_ADDR"],
					    $_POST["recaptcha_challenge_field"],
					    $_POST["recaptcha_response_field"]);

	    if ($resp->is_valid) {
		echo "Correct Capche is entered";
		
		    //if nothing is mission or no errors is thrown
		if (!$missing && !$errors){
		    try {
			$cbf = new CallBackForm($fname, $email, $tel, $enquiry);
		    } catch (Exception $e) {
			echo $e->getMessage();
		    }		
		} else {
		    echo '<span class="warning">All values not set. Therefore, form couldn\'t be submitted: <br />';
		    print_r($filtered);
		    echo '</span><br />';
		}
		   
	    } else {
		    # set the error code so that we can display it
		    $error = $resp->error;
	    }
    }
    
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
    
    

    ?>

    <body>
	<h1>CallBack Form</h1>
	<form action="CallBack_CAPTCHA.php" method="POST">
	    

	    <ul>
		<li>
		    <?php 
			if (isset($errors['user_fname'])) { 
			    echo '<span class="warning">' . $errors['user_fname'] . '</span><br />'; 				
			} 
		    ?>
		    <span class="leftWidth">First Name:</span>
			<input type="text" maxlength="32" size="20" name="user_fname"
			   <?php
				//Sticky Form: The Essential Guide to Dreamweaver CS4 with CSS, Ajax, and PHP
				if (isset($missing)) { //if any field a are missing retain the info
				    //ENT_COMPAT: converts double quote to $quote; but lives single quote alone
				    echo 'value ="'.htmlentities($_POST['user_fname'], ENT_COMPAT, 'UTF-8').'"';
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
		    
		    
		    
		    <?php echo recaptcha_get_html($publickey, $error); ?>
		    <input class="buttonBackground" type="submit" value="Request Callback" name="request_callback" />
		    <input class="buttonBackground" type="reset" value="Reset" id="reset" name="reset">
		</li>
	    </ul>
	    
	</form>
	
	<!--div id="callback">
	    <div id="cbHeader">
		  <div class="leftFloat">CallBack</div>
	    </div>
	    <div id="cbMiddle">
		
	    </div>    	    
	</div-->
	
    </body>
</html>