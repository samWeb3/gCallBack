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
	if (!$fname) {
	    throw new Exception("Plese supply first name");
	}
	if (!$email) {
	    throw new Exception("Plese supply email");
	}
	if (!$tel) {
	    throw new Exception("Plese supply telephone");
	}
	if (!$enquiry) {
	    throw new Exception("Plese supply your enquiry");
	}

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
	
	$user = array(
	    array('name' => $this->_fname, 'email' => $this->_email, 'telephone' => $this->_tel,)
	);
	
	$this->_crud->dbSelect('callbackuser', email, $this->_email);
	
	$enquiry = array(
	    array('user_id' => '1', 'instanceId' => $this->_instId, 'enquiry' => $this->_enquiry, 'callBackDate' => $unixtime)
	);
	
	$this->_crud->dbInsert('callbackuser', $user);
	$this->_crud->dbInsert('callbackuserenquiry', $enquiry);
	
	/*$values = array(
	    array('instanceId' => $this->_instId, 'name' => $this->_fname, 'email' => $this->_email,
		'telephone' => $this->_tel, 'enquiry' => $this->_enquiry, 'callBackDate' => $unixtime)
	);
	$result = $this->_crud->dbInsert('gccallback', $values);
	if ($result){
	    echo "Row successfuly inserted!<br />";
	} else {
	    echo "Unable to insert the row! <br />";
	}*/
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
	echo "Email to be sent to : $ow_email using gfEmailPostmark class";
	/* $subject = "Hey Son!";
	  $message = "Did you receive my message";
	  $email = new gfEmailPostmark();
	  $email->to($ow_email)->subject($subject)->messagePlain($message)->send(); */
    }
}
?>
