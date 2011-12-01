<?php

require_once 'gfCRUD.class.php';
require_once 'gfInstances.class.php';
require_once 'gfOwnersManage.class.php';
require_once 'gfEmailPostmark.class.php';

class CallBackForm {

    private $_crud;
    private $_instId;
    private $_fname;
    private $_email;
    private $_tel;
    private $_enquiry;    

    public function __construct($fname, $email, $tel, $enquiry) {
	
	$this->_fname = $fname;
	$this->_email = $email;
	$this->_tel = $tel;
	$this->_enquiry = $enquiry;

	$this->dbConnSetup();
	$this->setInstId();
	$this->addCallBackRequest();
	$this->sendEmail();
    }

    private function dbConnSetup() {
	$this->_crud = new CRUD();
	$this->_crud->username = 'root';
	$this->_crud->password = 'root123';
	$this->_crud->dsn = "mysql:dbname=griff;host=localhost";
    }

    public function addCallBackRequest() {
	$unixtime = time();
	
	$result = $this->_crud->dbSelect('callbackuser', 'email', $this->_email);
	foreach ($result as $r){	    
	    $email = $r[email];
	}
	
	if ($email == $this->_email){ //if email exist
	    echo "Email exist: <br>";
	    
	    //get the user id of the existing user
	    $user_id = $r[user_id]; 
	    echo "User Id of the email address $email is $user_id <br>";
	    
	    //use the id of existing user to insert into the $enquiry database
	    $enquiry = array(
		array('user_id' => $user_id, 'instanceId' => $this->_instId, 'enquiry' => $this->_enquiry, 'callBackDate' => $unixtime)
	    );
	    $this->_crud->dbInsert('callbackuserenquiry', $enquiry);	    
	} else {
	    echo "Email doesn't exist: <br>";
	    
	    //Insert new user into the callbackuser table
	    $user = array(
		array('name' => $this->_fname, 'email' => $this->_email, 'telephone' => $this->_tel)
	    );	    
	    $this->_crud->dbInsert('callbackuser', $user);

	    //Get the user Id of the inserted user
	    $result = $this->_crud->dbSelect('callbackuser', 'email', $this->_email);	
	    foreach ($result as $r){
		$user_id = $r[user_id];
		echo "<br />user ID: " .$user_id."<br />";	    
	    }

	    //Used the retrieved user id to insert rest of info in enquiry table
	    $enquiry = array(
		array('user_id' => $user_id, 'instanceId' => $this->_instId, 'enquiry' => $this->_enquiry, 'callBackDate' => $unixtime)
	    );
	    $this->_crud->dbInsert('callbackuserenquiry', $enquiry);	    
	}
    }

    private function setInstId() {
	$gfInt = new gfInstances();
	$this->_instId = $gfInt->getInstanceId();
    }

    private function getOwnerEmail() {
	$ow = new gfOwnersManage();
	$owEmail = $ow->getDetailsByInstance($this->_instId);
	foreach ($owEmail as $oe) {
	    return $oe[0];
	}
    }

    private function sendEmail() {
	$ow_email = $this->getOwnerEmail();
	echo "Email to be sent to : $ow_email using gfEmailPostmark class<br />";
	/* $subject = "Hey Son!";
	  $message = "Did you receive my message";
	  $email = new gfEmailPostmark();
	  $email->to($ow_email)->subject($subject)->messagePlain($message)->send(); */
    }
}
?>
