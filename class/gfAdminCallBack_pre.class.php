<?php

require_once 'gfCRUD.class.php';

class AdminCallBack {
    private $_crud;
    
    public function __construct() {
	$this->dbConnSetup();
    }

    private function dbConnSetup() {
	$this->_crud = new CRUD();
	$this->_crud->username = 'root';
	$this->_crud->password = 'root123';
	$this->_crud->dsn = "mysql:dbname=griff;host=localhost";
    }
    
     public function viewAllCallBacks(){
	$sql = "SELECT callbackuser.user_id, name, email, telephone, enquiry, callBackDate
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		ORDER BY callbackuserenquiry.callBackDate DESC";
	
	$this->_crud->conn();
	$stmt= $this->_crud->getDbConn()->prepare($sql);
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
	

//$conn->
	/*$fields = implode(', ', $fieldnames);
	$tables = implode(', ', $tablenames);
	
	$sql = "SELECT $fields FROM $table WHERE $tablenames[0].user_id = $tablenames[1].user_id
		ORDER BY $tablenames[1].callBackDate ASC";
	
	$this->_crud->*/
    }
    public function viewCallBack($start, $display){
	$sql = "SELECT callbackuser.user_id, name, email, telephone, enquiry, callBackDate
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		ORDER BY callbackuserenquiry.callBackDate DESC LIMIT $start, $display";
	
	$this->_crud->conn();
	$stmt= $this->_crud->getDbConn()->prepare($sql);
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
	

//$conn->
	/*$fields = implode(', ', $fieldnames);
	$tables = implode(', ', $tablenames);
	
	$sql = "SELECT $fields FROM $table WHERE $tablenames[0].user_id = $tablenames[1].user_id
		ORDER BY $tablenames[1].callBackDate ASC";
	
	$this->_crud->*/
    }

}

?>
