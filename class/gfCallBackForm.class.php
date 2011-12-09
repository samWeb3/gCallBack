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
	$this->_crud->conn();
    }

    public function addCallBackRequest() {
	$unixtime = time();
	
	$result = $this->_crud->dbSelect('callbackuser', 'email', $this->_email);
	foreach ($result as $r){	    
	    $email = $r[email];
	    $tel = $r[telephone];
	}
	
	if ($email == $this->_email){ //if email exist
	    if (Debug::getDebug()){
		Fb::info("CallBackForm: Email exist!");
	    }	    
	    
	    //get the user id of the existing user
	    $user_id = $r[user_id]; 
	    
	    if (Debug::getDebug()){
		$message = "CallBackForm: User Id of the email address".$email." is ".$user_id."and Tel is: ".$tel;
		Fb::info($message);
	    }
	    
	    //use the id of existing user to insert into the $enquiry database
	    $enquiry = array(
		array('user_id' => $user_id, 'instanceId' => $this->_instId, 'enquiry' => $this->_enquiry, 'callBackDate' => $unixtime)
	    );
	    
	    $this->_crud->dbInsert('callbackuserenquiry', $enquiry);
	    
	    //if telephone retrived from database is not equal to one passed on the form
	    if ($tel != $this->_tel){
		if (Debug::getDebug()){
		    Fb::warn("Need to update the database!");
		    fb($user_id, "User ID");
		    fb($this->_tel, "New Telephone");
		    fb($tel, "Old Telephone");
		    $this->_crud->dbUpdate('callbackuser', 'telephone', $this->_tel, 'user_id', $user_id);
		    
		}		
	    } else {
		if (Debug::getDebug()){
		    Fb::info("Telephone no is same");
		}		
	    }
	    
	} else {
	    if (Debug::getDebug()){
		Fb::info("CallBackForm: Email doesn't exist:");
	    }
	    
	    //Insert new user into the callbackuser table
	    $user = array(
		array('name' => $this->_name, 'email' => $this->_email, 'telephone' => $this->_tel)
	    );	    
	    
	    $this->_crud->dbInsert('callbackuser', $user);

	    //Get the user Id of the inserted user
	    $result = $this->_crud->dbSelect('callbackuser', 'email', $this->_email);	
	    foreach ($result as $r){
		$user_id = $r[user_id];
		
		if (Debug::getDebug()){
		    fb($user_id, "CallBackForm: User ID: ");		    
		}
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
	
	if (Debug::getDebug()){	    
	    FB::info("CallBackForm: Email to be sent to : $ow_email using gfEmailPostmark class with follwoing information: "); 
	    $message = "Name: ".$this->_name."<br /> Email: ".$this->_email."<br /> Telephone: ".$this->_tel."<br /> Enquiry: ".$this->_enquiry."<br />";	    
	    FB::info($message);
	}
	
	  /*$message = "Did you receive my message";
	  $email = new gfEmailPostmark();
	  $email->to($ow_email)->subject($subject)->messagePlain($message)->send(); */
    }
    
    /**
     * Set debug mode
     *
     * @access public
     * @param bool $debug Set to TRUE to enable debug messages
     * @return void
     */
    /*public static function setDebug($debug) {
	self::$_debug = $debug;
    }*/
}
?>
