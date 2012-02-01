<?php

//require_once 'gfDebug.php';
require_once 'gfCRUD.class.php';
require_once 'gfInstances.class.php';
require_once 'gfOwnersManage.class.php';
require_once 'gfEmailPostmark.class.php';
require_once 'gfUser.class.php';

class CallBackForm {

    private $_crud;
    private $_instance;
    private $_user;
    private $_enquiry;

    public function __construct(Crud $crud, gfInstances $instances, User $user, $enquiry) {
	$this->_crud = $crud;
	$this->_user = $user;
	$this->_instance = $instances;	
	$this->_enquiry = $enquiry;	
	
	$this->addCallBackRequest();
	$this->sendEmail();
    }

    /**
     * Add new or Update existing records based on a valued submitted from the Callback Form
     */
    public function addCallBackRequest() {
	$dbRow = $this->getRow('callbackuser', 'email', $this->_user->getEmail());

	//if email exist
	if ($dbRow[email] == $this->_user->getEmail()) {
	    $this->updateExistingRecord($dbRow[user_id], $dbRow[telephone]);
	} else {
	    $this->updateNewRecord();
	}
    }
    
    /**
     * Reset the form field after the submission
     * This method have to be called after unsetCookie(), Else it won't reset for those values which are cached by sticky form
     * 
     * @param <array> $fieldnameArr    Fieldname(s) to be reset
     */
    public function resetForm($fieldnameArr){	
	foreach ($fieldnameArr as $key => $value){
	    unset($_POST[$value]);
	}
    }

    /**
     *
     * @param type $dbUserId
     * @param type $dbTel 
     */
    private function updateExistingRecord($dbUserId, $dbTel) { 

	//use the id of existing user to insert into the $enquiry database
	$enquiry = array(
	    array('user_id' => $dbUserId, 
		'instanceId' => $this->getInstId(), 
		'enquiry' => $this->_enquiry, 
		'callBackDate' => time()
	    )
	);

	$this->insertRow('callbackuserenquiry', $enquiry);

	//if telephone retrived from database is not equal to one passed on the form
	if ($dbTel != $this->_user->getTel()) { 
	    $this->updateRow('callbackuser', 'telephone', $this->_user->getTel(), 'user_id', $dbUserId);
	}
    }

    /**
     * 
     */
    private function updateNewRecord() {
	//Insert new user into the callbackuser table
	$user = array(
	    array('name' => $this->_user->getName(), 
		'email' => $this->_user->getEmail(), 
		'telephone' => $this->_user->getTel())
	);

	//$this->_crud->dbInsert('callbackuser', $user);
	$this->insertRow('callbackuser', $user);

	//Get the user Id of the inserted user	
	$dbRow = $this->getRow('callbackuser', 'email', $this->_user->getEmail());
	
	//Used the retrieved user id to insert rest of info in enquiry table
	$enquiry = array(
	    array('user_id' => $dbRow[user_id], 
		'instanceId' => $this->getInstId(), 
		'enquiry' => $this->_enquiry, 
		'callBackDate' => time())
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
    
    private function getInstId(){
	return $this->_instance->getInstanceId();
    }
    
    /**
     * Return email address of an instance
     * @return type 
     */
    private function getOwnerEmail() {
	$ow = new gfOwnersManage();
	$owEmail = $ow->getDetailsByInstance($this->getInstId());
	foreach ($owEmail as $oe) {
	    return $oe[0];
	}
    }

    /**
     * Sends an email to the instance owner
     */
    private function sendEmail() {
	$ow_email = $this->getOwnerEmail();

	if (Debug::getDebug()) {
	    FB::info("CallBackForm: Email to be sent to : $ow_email using gfEmailPostmark class with follwoing information: ");
	    $message = "Name: " . $this->_user->getName() . "<br /> Email: " . $this->_user->getEmail() . "<br /> Telephone: " . $this->_user->getTel() . "<br /> Enquiry: " . $this->_enquiry . "<br />";
	    FB::info($message);
	}
	/* $message = "Did you receive my message";
	  $email = new gfEmailPostmark();
	  $email->to($ow_email)->subject($subject)->messagePlain($message)->send(); */
    }

}

?>
