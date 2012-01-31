<?php
class User {
    private $_userName;
    private $_userEmail;
    private $_userTel;
    
    public function __construct($userName, $userEmail, $userTel) {
	$this->_userName = $userName;
	$this->_userEmail = $userEmail;
	$this->_userTel = $userTel;
    }
    
   /*************************************************************
     * FUNCTIONS TO BE ADDED 
     *************************************************************/
    public function addUser(){}
    
    public function deleteUser(){}
    
    public function updateUser(){}
    
    /******************************************************
     *  Getters and Setter
     ******************************************************/
    public function getName(){
	return $this->_userName;
    }
    public function getEmail(){
	return $this->_userEmail;
    }
    public function getTel(){
	return $this->_userTel;
    }
}
?>
