<?php

//require_once 'gfDebug.php';
require_once 'gfCRUD.class.php';
require_once 'gfInstances.class.php';
require_once 'gfOwnersManage.class.php';
require_once 'gfEmailPostmark.class.php';

class CallBackForm {

    private $_crud;
    private $_instId;
    private $_name;
    private $_email;
    private $_tel;
    private $_enquiry;

    public function __construct($name, $email, $tel, $enquiry) {
	$this->_name = $name;
	$this->_email = $email;
	$this->_tel = $tel;
	$this->_enquiry = $enquiry;

	$this->_crud = new CRUD();
	$this->setInstId();
	$this->addCallBackRequest();
	$this->sendEmail();
    }

    public function addCallBackRequest() {
	$dbRow = $this->getRow('callbackuser', 'email', $this->_email);

	//if email exist
	if ($dbRow[email] == $this->_email) {
	    $this->updateExistingRecord($dbRow[user_id], $dbRow[telephone]);
	} else {
	    $this->updateNewRecord();
	}
    }

    /**
     *
     * @param type $dbUserId
     * @param type $dbTel 
     */
    private function updateExistingRecord($dbUserId, $dbTel) { 
	if (Debug::getDebug()) {
	    Fb::info("CallBackForm: Email exist!");
	}

	if (Debug::getDebug()) {
	    $message = "CallBackForm: User Id is " . $dbUserId . " and Tel is: " . $dbTel;
	    Fb::info($message);
	}

	//use the id of existing user to insert into the $enquiry database
	$enquiry = array(
	    array('user_id' => $dbUserId, 'instanceId' => $this->_instId, 'enquiry' => $this->_enquiry, 'callBackDate' => time())
	);

	$this->insertRow('callbackuserenquiry', $enquiry);

	//if telephone retrived from database is not equal to one passed on the form
	if ($dbTel != $this->_tel) {
	    if (Debug::getDebug()) {
		Fb::warn("Need to update the database!");
		fb($dbUserId, "User ID");
		fb($this->_tel, "New Telephone");
		fb($dbTel, "Old Telephone");
	    }

	    $this->updateRow('callbackuser', 'telephone', $this->_tel, 'user_id', $dbUserId);
	} else {
	    if (Debug::getDebug()) {
		Fb::info("Telephone no is same");
	    }
	}
    }

    /**
     * 
     */
    private function updateNewRecord() {
	if (Debug::getDebug()) {
	    Fb::info("CallBackForm: Email doesn't exist:");
	}

	//Insert new user into the callbackuser table
	$user = array(
	    array('name' => $this->_name, 'email' => $this->_email, 'telephone' => $this->_tel)
	);

	//$this->_crud->dbInsert('callbackuser', $user);
	$this->insertRow('callbackuser', $user);

	//Get the user Id of the inserted user	
	$dbRow = $this->getRow('callbackuser', 'email', $this->_email);
	//$dbUserId = $dbRow[user_id];
	//Used the retrieved user id to insert rest of info in enquiry table
	$enquiry = array(
	    array('user_id' => $dbRow[user_id], 'instanceId' => $this->_instId, 'enquiry' => $this->_enquiry, 'callBackDate' => time())
	);

	$this->insertRow('callbackuserenquiry', $enquiry);
    }

    /**
     * Insert ta row on a given table
     * 
     * @param type $table
     * @param type $values 
     */
    private function insertRow($table, $values) {
	$this->_crud->dbInsert($table, $values);
    }

    /**
     * Return row(s) of the given fieldname
     * 
     * @param string $table	    Tablename
     * @param string $fieldname	    Fieldname of table
     * @param string $id	    Value of a Fieldname
     * @return array		    Success or throw PDOExcepton on failure 
     */
    private function getRow($table, $fieldname, $id) {
	$dbRow = $this->_crud->dbSelect($table, $fieldname, $id);
	return $dbRow[0];
    }

    /**
     *
     * @param type $table
     * @param type $fieldname
     * @param type $value
     * @param type $pk
     * @param type $id 
     */
    private function updateRow($table, $fieldname, $value, $pk, $id) {
	$this->_crud->dbUpdate($table, $fieldname, $value, $pk, $id);
    }
    
    /**
     * 
     */
    private function setInstId() {
	$gfInt = new gfInstances();
	$this->_instId = $gfInt->getInstanceId();
    }
    
    /**
     *
     * @return type 
     */
    private function getOwnerEmail() {
	$ow = new gfOwnersManage();
	$owEmail = $ow->getDetailsByInstance($this->_instId);
	foreach ($owEmail as $oe) {
	    return $oe[0];
	}
    }

    /**
     * 
     */
    private function sendEmail() {
	$ow_email = $this->getOwnerEmail();

	if (Debug::getDebug()) {
	    FB::info("CallBackForm: Email to be sent to : $ow_email using gfEmailPostmark class with follwoing information: ");
	    $message = "Name: " . $this->_name . "<br /> Email: " . $this->_email . "<br /> Telephone: " . $this->_tel . "<br /> Enquiry: " . $this->_enquiry . "<br />";
	    FB::info($message);
	}
	/* $message = "Did you receive my message";
	  $email = new gfEmailPostmark();
	  $email->to($ow_email)->subject($subject)->messagePlain($message)->send(); */
    }

}

?>
