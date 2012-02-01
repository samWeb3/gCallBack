<?php

/**
 * http://www.phpro.org/classes/PDO-CRUD.html
 */
//require_once 'gfDebug.php';
require_once 'config/dbConn.inc.php';

class CRUD {

    private $_dbConn;
    private $_success;
    private $_username;
    private $_password;
    private $_dsn;    

    function __construct() {
	$this->_username = _USERNAME;
	$this->_password = _PASSWORD;
	$this->_dsn = _DSN;
	$this->conn();
    }

    /**
     * @Connect to the database and set the error mode to Exception 
     * @Throws PDOException on Failure
     */
    public function conn() {	
	if (!$this->_dbConn instanceof PDO) {
	    $this->_dbConn = new PDO($this->_dsn, $this->_username, $this->_password);
	    $this->_dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}	
    }

    //***********************************************************************
    // (C): Create Row(s)
    //***********************************************************************

    /**
     * Insert a value into a table from arrays;
     * @param string $table	table name into which value are inserted
     * @param array $values	values retrieved from the array
     */
    public function dbInsert($table, $values) {
	//$this->conn();

	//"array_values($values[0]" returns values of first array item
	$fieldnames = array_keys($values[0]);	

	$sql = "INSERT INTO $table";

	//set the field name
	$fields = '(' . implode(', ', $fieldnames) . ')';
	
	//set the placeholder values
	$bound = '(:' . implode(', :', $fieldnames) . ')';	

	//put the query together
	$sql .= $fields . ' VALUES ' . $bound;
	
	//Prepare statement
	$stmt = $this->_dbConn->prepare($sql);

	/* 
	 * Iterate through multi-dimentional array and execute statement to insert row
	 */
	foreach ($values as $vals) {
	    foreach ($vals as $v) {
		if (Debug::getDebug()) {
		    fb($v, "Values", FirePHP::INFO);
		}
	    }
	   $stmt->execute($vals);
	}	
    }

    //***********************************************************************
    // (R): Read Row(s)
    //***********************************************************************

    /**
     * Select values from table
     * 
     * @param string $table	    Tablename
     * @param string $fieldname	    Fieldname of table
     * @param string $id	    Value of a Fieldname
     * @return array		    Success or throw PDOExcepton on failure 
     */
    public function dbSelect($table, $fieldname=null, $id=null) {
	//$this->conn();
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

	$sql = "SELECT * FROM $table WHERE instanceId = $instanceId";	

	//if parameters is not null
	if ($fieldname != null && $id != null && $dateFieldName != null && $fromDate == null && $toDate == null) {
	    $sql .= " AND $fieldname = :id";
	    if (Debug::getDebug()) {
		Fb::warn("Only Field name and id set:");
		fb($sql, "SQL Query 2", FirePHP::INFO);
	    }
	    $stmt = $this->_dbConn->prepare($sql);
	    $stmt->bindParam(':id', $id);
	} else if ($fieldname != null && $id != null && $dateFieldName != null && $fromDate != null && $toDate != null) {
	    $sql .= " AND $fieldname = :id AND $dateFieldName > :fromDate AND $dateFieldName < :toDate";
	    if (Debug::getDebug()) {
		Fb::warn("All Parameter Set:");
		fb($sql, "SQL Query3", FirePHP::INFO);
	    }
	    $stmt = $this->_dbConn->prepare($sql);
	    $stmt->bindParam(':id', $id);
	    $stmt->bindParam(':fromDate', $fromDate);
	    $stmt->bindParam(':toDate', $toDate);
	} else if ($fieldname == null && $id == null && $dateFieldName != null && $fromDate != null && $toDate != null) {
	    $sql .= " AND $dateFieldName > :fromDate AND $dateFieldName < :toDate";
	    if (Debug::getDebug()) {
		Fb::warn("Fields name and id not set");
		fb($sql, "SQL Query4", FirePHP::INFO);
	    }
	    $stmt = $this->_dbConn->prepare($sql);
	    $stmt->bindParam(':fromDate', $fromDate);
	    $stmt->bindParam(':toDate', $toDate);
	} else if ($fieldname == null && $id == null && $dateFieldName != null && $fromDate == null && $toDate == null) {
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
	$stmt = $this->_dbConn->query($sql);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function dbUpdate($table, $fieldname, $value, $pk, $id) {
	//$this->conn();

	$result = $this->chkRowExist($table, $pk, $id);
	if (!$result) {
	    throw new Exception("Row $id Doesn't exist");
	}
	$sql = "UPDATE $table SET $fieldname = '$value' WHERE $pk = :id";
	

	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();

	/*if ($stmt->rowCount() > 0) {
	    //return $this->_success = true;
	    if (Debug::getDebug()) {
		FB::info("Row $id successfully updated!");
	    }
	}*/
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
	if (!$result) {
	    throw new Exception("Row Doesn't exist");
	}
	$sql = "DELETE FROM $table WHERE $fieldname =:id";
	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id); //use parameterized sql stmt to prevent sql injection
	$stmt->execute();
	/*if ($stmt->rowCount() > 0) {
	    if (Debug::getDebug()) {
		FB::info("Row $id Deleted");
	    }
	}*/
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
    
    //***********************************************************************
    // GETTERS
    //***********************************************************************
    public function getDbConn() {
	return $this->_dbConn;
    }
}

?>
