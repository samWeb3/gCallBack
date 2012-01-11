<?php

/**
 * http://www.phpro.org/classes/PDO-CRUD.html
 */
require_once 'gfDebug.php';

class CRUD {

    private $_dbConn;
    private $_success;

    /**
     * http://www.hiteshagrawal.com/php/php5-tutorial-__set-magic-method
     * 
     * @param type $name    holds the name of undefined attributes
     * @param type $value   holds the value assigned to the undefined attributes
     */
    public function __set($name, $value) {
	switch ($name) {
	    case 'username':
		$this->username = $value;
		if (Debug::getDebug()){
		    fb($value, "Username", FirePHP::INFO);		    
		}
		break;

	    case 'password':
		$this->password = $value;
		if (Debug::getDebug()){
		    fb($value, "Password", FirePHP::INFO);		    
		}
		break;

	    case 'dsn':
		$this->dsn = $value;
		if (Debug::getDebug()){
		    fb($value, "DSN Set", FirePHP::INFO);		    
		}		
		break;
	    default:
		throw new Exception("$name is invalid");
	}
    }
    /**
     * Returns database connection 
     */
    public function getDbConn(){
	return $this->_dbConn;
    }

    /**
     * Check for the undeclared variable in the code
     * 
     * @param type $name 
     */
    public function __isset($name) {
	switch ($name) {
	    case 'username':
		$this->username = null;
		break;

	    case 'password':
		$this->password = null;
		break;
	}
    }

    /**
     * @Connect to the database and set the error mode to Exception 
     * @Throws PDOException on Failure
     */
    public function conn() {
	isset($this->username);
	isset($this->password);
	if (!$this->_dbConn instanceof PDO) {
	    $this->_dbConn = new PDO($this->dsn, $this->username, $this->password);
	    $this->_dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}	
	if (Debug::getDebug()){
	    FB::info("CRUD class: Connection successful!");	    
	}
    }
    

    //***********************************************************************
    // (C): Create Row(s)
    //***********************************************************************

    /**
     * Insert a value into a table from arrays;
     * @param string $table   table name into which value are inserted
     * @param array $values  values retrieved from the array
     */
    public function dbInsert($table, $values) {
	$this->conn();	

	//Gets the arary key of first array item "array_values($values[0]" returns values of first array item
	$fieldnames = array_keys($values[0]);
	if (Debug::getDebug()){
	    fb($fieldnames, "Fieldnames", FirePHP::INFO);	    
	}	

	$sql = "INSERT INTO $table";

	//set the field name
	$fields = '(' . implode(', ', $fieldnames) . ')';
	if (Debug::getDebug()){
	    fb($fields, "Fields", FirePHP::INFO);	    
	}
	//set the placeholder values
	$bound = '(:' . implode(', :', $fieldnames) . ')';
	if (Debug::getDebug()){
	    fb($bound, "Bounds", FirePHP::INFO);	    
	}

	//put the query together
	$sql .= $fields . ' VALUES ' . $bound;
	if (Debug::getDebug()){
	    fb($sql, "SQL Query", FirePHP::INFO);	    
	}	
	//Prepare statement
	$stmt = $this->_dbConn->prepare($sql);

	/* Iterate through multi-dimentional array and execute statement */
	foreach ($values as $vals) {
	    foreach ($vals as $v) {
		if (Debug::getDebug()){
		    fb($v, "Values", FirePHP::INFO);
		}		
	    }
	    
	    $result = $stmt->execute($vals);
	}
	
	if ($result) {
	    return $this->_success = true;
	} else {
	    return $this->_success = false;
	}
    }
    
    //***********************************************************************
    // (R): Read Row(s)
    //***********************************************************************
    
    /**
     * Select values from table
     * 
     * @param string $table
     * @param string $fieldname
     * @param string $id
     * @return array on success or throw PDOExcepton on failure 
     */
    public function dbSelect($table, $fieldname=null, $id=null, $fromDate=null, $toDate=null) {
	$this->conn();	
	if ($fieldname && $id != null) {
	    $sql = "SELECT * FROM $table WHERE $fieldname =:id";
	} else {
	    $sql = "SELECT * FROM $table";
	}

	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Returns the callback data relevent to the filtering of the data accroding to the provided parameters	
     * 
     * @param string $table	    Tablename from where the records to be selected
     * @param int    $instanceId    Instance Id of a partner website
     * @param string $fieldname	    Fieldname to be filtered
     * @param int    $id	    Id of a fieldname to be filtered from	
     * @param string $dateFieldName 
     * @param string $fromDate	    Date From
     * @param string $toDate	    Date Until
     * @return string		    Records 
     */
    public function dbSelectFromTo($table, $instanceId, $fieldname=null, $id=null, $dateFieldName = null, $fromDate=null, $toDate=null) {
	fb($fieldname, "FieldName", FirePHP::INFO);
	fb($id, "ID", FirePHP::INFO);
	fb($dateFieldName, "Date Field Name", FirePHP::INFO);
	fb($fromDate, "From Date", FirePHP::INFO);
	fb($toDate, "To Date", FirePHP::INFO);
	
	$sql = "SELECT * FROM $table WHERE instanceId = $instanceId";	
	
	if (Debug::getDebug()){
	   fb($sql, "SQL Query 1 ", FirePHP::INFO);	   
	}
	
	//if parameters is not null
	if ($fieldname != null && $id != null && $dateFieldName != null && $fromDate == null && $toDate == null){
	    Fb::warn("Only Field name and id set:");
	    $sql .= " AND $fieldname = :id";
	    if (Debug::getDebug()){
	       fb($sql, "SQL Query 2", FirePHP::INFO);
	    }
	    $stmt = $this->_dbConn->prepare($sql);
	    $stmt->bindParam(':id', $id);
	     
	} else if ($fieldname != null && $id != null && $dateFieldName != null && $fromDate != null && $toDate != null){   	
	   Fb::warn("All Parameter Set:");
	   $sql .= " AND $fieldname = :id AND $dateFieldName > :fromDate AND $dateFieldName < :toDate";
	   if (Debug::getDebug()){
	    fb($sql, "SQL Query3", FirePHP::INFO);
	   }
	   $stmt = $this->_dbConn->prepare($sql);
	   $stmt->bindParam(':id', $id);
	   $stmt->bindParam(':fromDate', $fromDate);
	   $stmt->bindParam(':toDate', $toDate);	
	} else if ($fieldname == null && $id == null && $dateFieldName != null && $fromDate != null && $toDate != null){
	    Fb::warn("Fields name and id not set");
	    $sql .= " AND $dateFieldName > :fromDate AND $dateFieldName < :toDate";
	    if (Debug::getDebug()){
		fb($sql, "SQL Query4", FirePHP::INFO);
	    }
	    $stmt = $this->_dbConn->prepare($sql);	   
	    $stmt->bindParam(':fromDate', $fromDate);
	    $stmt->bindParam(':toDate', $toDate);
	} else if ($fieldname == null && $id == null && $dateFieldName != null && $fromDate == null && $toDate == null){
	    Fb::warn("Only Datefield set");
	      $stmt = $this->_dbConn->prepare($sql);
	}
		
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    
    /**
     *
     * @execute a raw query
     *
     * @access public     
     * @param string $sql  
     * @return array
     *
     */
     public function rawSelect($sql) {
	//$this->conn();
	//return $this->_dbConn->query($sql);
	$stmt = $this->_dbConn->query($sql);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function dbJoinTable($tablenames, $fieldnames, $id){	
	
	print_r($tablenames);	
	print_r($fieldnames);
	
	
	$fields = implode(', ', $fieldnames);
	if (Debug::getDebug()){
	   echo "<br />Fields: " . $fields."<br />"; 
	}	
	
	$tables = implode(', ', $tablenames);
	if (Debug::getDebug()){
	    echo "<br />Tables: " .$tables."<br />";
	}
	
	//$tablePlusId = implode('.'.user_id.', ', $tablenames).".id".$id;
	
	foreach ($tablenames as $tn){
	    if (Debug::getDebug()){
		echo $tn.".".$id."<br>";
	    }	    
	}	
	
	if (Debug::getDebug()){    
	    echo "<br /> Table plus Id: ". $tablePlusId."<br>";
	}
	
	$sql = "SELECT ".$fields." FROM ".$tables. " WHERE ";
	
	if (Debug::getDebug()){
	    echo "SQL So far: ".$sql;
	}
    }
    
    //***********************************************************************
    // (U): Update Row(s)
    //***********************************************************************
    
    /**
     * Updates column of a table name identified by the primary key
     * 
     * @param type $table	Table name
     * @param type $fieldname	Column of a table to be updated
     * @param type $value	Value of Column to be updated with
     * @param type $pk		Primary key of the record to be updated
     * @param type $id		Value of primary key
     */
    public function dbUpdate($table, $fieldname, $value, $pk, $id){
	//$this->conn();
	
	$result = $this->chkRowExist($table, $pk, $id);
	if (!$result){
	    throw new Exception("Row $id Doesn't exist");
	}
	$sql = "UPDATE $table SET $fieldname = '$value' WHERE $pk = :id";
	
	if (Debug::getDebug()){	    	    
	    fb($sql, "SQL Query", FirePHP::INFO);
	}
	
	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	
	if ($stmt->rowCount() > 0) {
	    //return $this->_success = true;
	    if (Debug::getDebug()){
		FB::info("Row $id successfully updated!");		
	    }	    
	} 
    }
    
    //***********************************************************************
    // (D): Delete Row
    //***********************************************************************

    /**
     * @Delete single record from a table
     * 
     * @param string $table	Table name from where records are to be deleted
     * @param string $fieldname 
     * @param string $id 
     */
    public function dbDeleteSingleRow($table, $fieldname, $id) {
	//$this->conn();
	$result = $this->chkRowExist($table, $fieldname, $id);
	if (!$result){
	    throw new Exception("Row Doesn't exist");
	}
	$sql = "DELETE FROM $table WHERE $fieldname =:id";
	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id); //use parameterized sql stmt to prevent sql injection
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
	    if (Debug::getDebug()){
		echo "Row $id Deleted ";
	    }
	}
    }

    public function dbDeleteMultipleRow($table, $fieldname, $array) {
	//$this->conn();
	foreach ($array as $id) {
	    $result = $this->chkRowExist($table, $fieldname, $id);
	    if (!$result){
		//throw new Exception("Row $id Doesn't exist");
		echo ("Row $id Doesn't Exist <br>");
	    } else {
	    //if ($result) {
		if (Debug::getDebug()){
		    echo "Row " . $id . " Exists!!! <br>";
		}
		$sql = "DELETE FROM $table WHERE $fieldname = :id";
		$stmt = $this->_dbConn->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
		    if (Debug::getDebug()){
			//return $this->_success = true;
			echo "Row $id Deleted <br>";
		    }
		}
	    }
	}
    }
    
    //***********************************************************************
    // WORKER CLASS
    //***********************************************************************
    /**
     * Checks if the row exists.
     * 
     * @param string $table	    Name of table 
     * @param string $fieldname	    Column
     * @param string $id	    Id
     * @return boolean 
     */
    private function chkRowExist($table, $fieldname=null, $id=null) {
	//$this->conn();
	$sql = "SELECT * FROM $table WHERE $fieldname =:id";
	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	if ($stmt->rowCount() > 0) {
	    return $this->_success = true;
	} else {
	    return $this->_success = false;
	}
    }
}

?>
