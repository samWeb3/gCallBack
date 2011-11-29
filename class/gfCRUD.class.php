<?php

/**
 * http://www.phpro.org/classes/PDO-CRUD.html
 */
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
		echo "Username Set: " . $value . "<br />";
		break;

	    case 'password':
		$this->password = $value;
		echo "Password Set: " . $value . "<br />";
		break;

	    case 'dsn':
		$this->dsn = $value;
		echo "DSN Set: " . $value . "<br />";
		break;

	    default:
		throw new Exception("$name is invalid");
	}
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
	echo "Connection successful!";
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
	echo "inside insert";

	//Gets the arary key of first array item "array_values($values[0]" returns values of first array item
	$fieldnames = array_keys($values[0]);
	echo "Fieldnames: " . $fieldnames . "<br>";

	foreach ($fieldnames as $fn) {
	    echo $fn . "<br>";
	}

	$sql = "INSERT INTO $table";

	//set the field name
	$fields = '(' . implode(', ', $fieldnames) . ')';
	echo "Fields: " . $fields . "<br>";
	//set the placeholder values
	$bound = '(:' . implode(', :', $fieldnames) . ')';
	echo "Bounds: " . $bound . "<br>";

	//put the query together
	$sql .= $fields . ' VALUES ' . $bound;
	echo "Sql: " . $sql . "<br>";
	//Prepare statement
	$stmt = $this->_dbConn->prepare($sql);

	/* Iterate through multi-dimentional array and execute statement */
	foreach ($values as $vals) {
	    foreach ($vals as $v) {
		echo $v;
	    }
	    echo "<br>";
	    $result = $stmt->execute($vals);
	}
	
	if ($result) {
	    return $this->_success = true;
	} else {
	    return $this->_success = false;
	}

	/*if (!$result) {
	    throw new Exception("Unable to execute query");
	}*/
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
    public function dbSelect($table, $fieldname=null, $id=null) {
	$this->conn();
	//$sql="SELECT * FROM $table WHERE $fieldname = :id";
	if ($fieldname && $id != null) {
	    $sql = "SELECT * FROM $table WHERE $fieldname =:id";
	} else {
	    $sql = "SELECT * FROM $table";
	}

	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_ASSOC);
	/* if ($stmt->rowCount()>0){
	  echo "Row Exists!";
	  } else {
	  echo "Row doesn't exist!";
	  } */
    }
    
    //***********************************************************************
    // (U): Update Row(s)
    //***********************************************************************
    public function dbUpdate($table, $fieldname, $value, $pk, $id){
	$this->conn();	
	echo "Tablename: ".$table."<br>";
	echo "Fieldname: ".$pk."<br>";
	echo "ID: ".$id."<br>";
	
	$result = $this->chkRowExist($table, $pk, $id);
	if (!$result){
	    throw new Exception("Row $id Doesn't exist");
	}
	$sql = "UPDATE $table SET $fieldname = '$value' WHERE $pk = :id";
	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	
	if ($stmt->rowCount() > 0) {
	    //return $this->_success = true;
	    echo "Row $id successfully updated! ";
	}
	
	/*if ($stmt->rowCount() > 0) {
	    return $this->_success = true;
	    
	} else {
	    return $this->_success = false;
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
	$this->conn();
	$result = $this->chkRowExist($table, $fieldname, $id);
	if (!$result){
	    throw new Exception("Row Doesn't exist");
	}
	$sql = "DELETE FROM $table WHERE $fieldname =:id";
	$stmt = $this->_dbConn->prepare($sql);
	$stmt->bindParam(':id', $id); //use parameterized sql stmt to prevent sql injection
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
	    //return $this->_success = true;
	    echo "Row $id Deleted ";
	}
    }

    public function dbDeleteMultipleRow($table, $fieldname, $array) {
	$this->conn();
	foreach ($array as $id) {
	    $result = $this->chkRowExist($table, $fieldname, $id);
	    if (!$result){
		//throw new Exception("Row $id Doesn't exist");
		echo ("Row $id Doesn't Exist <br>");
	    } else {
	    //if ($result) {
		echo "Row " . $id . " Exists!!! <br>";
		$sql = "DELETE FROM $table WHERE $fieldname = :id";
		$stmt = $this->_dbConn->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
		    //return $this->_success = true;
		    echo "Row $id Deleted <br>";
		}
	    }
		//echo "Row " . $id . " Deleted!<br>";
	    //} else {
		//echo "Row " . $id . " doesn't exist!!! <br>";
	    //}
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
	$this->conn();
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
