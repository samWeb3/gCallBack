<?php
    require_once 'class/gfCRUD.class.php';
    require_once 'FirePHP/firePHP.php';
    Debug::setDebug(true);
    
    $cb = new CRUD();
    $cb->username = 'root';
    $cb->password = 'root123';
    $cb->dsn = "mysql:dbname=griff;host=localhost";
    
    //$cb->conn();
    /* array of values to insert*/
    $values = array(
	array('instanceId'=>'151', 'name'=>'shanti', 'email'=>'shanti@shanti.com', 'telephone'=>'07986532546', 'enquiry'=>'Hello!', 'callBackDate'=>'UNIX_TIMESTAMP(now())'),
	array('instanceId'=>'151', 'name'=>'krishna', 'email'=>'krishna@krishna.com', 'telephone'=>'07566532546', 'enquiry'=>'Hi!', 'callBackDate'=>'UNIX_TIMESTAMP(now())'),
	array('instanceId'=>'151', 'name'=>'gita', 'email'=>'gita@gita.com', 'telephone'=>'07566532546', 'enquiry'=>'Hey!', 'callBackDate'=>'UNIX_TIMESTAMP(now())'), 
	array('instanceId'=>'151', 'name'=>'nanu', 'email'=>'nanu@nanu.com', 'telephone'=>'07566532546', 'enquiry'=>'Hi! Hi', 'callBackDate'=>'UNIX_TIMESTAMP(now())'), 
	array('instanceId'=>'151', 'name'=>'puspa', 'email'=>'puspa@puspa.com', 'telephone'=>'07566532546', 'enquiry'=>'Hi! Hi', 'callBackDate'=>'UNIX_TIMESTAMP(now())'), 
	array('instanceId'=>'151', 'name'=>'sambhu', 'email'=>'sambhu@sambhu.com', 'telephone'=>'07566532546', 'enquiry'=>'me me', 'callBackDate'=>'UNIX_TIMESTAMP(now())'), 
    );
    //Insert into table
    //$cb->dbInsert('gccallback', $values);
    
    $res = $cb->dbSelect('gccallback');    
    foreach ($res as $array){
	foreach ($array as $k => $v){
	    echo "key <strong>" . $k . "</strong> has value of: <strong>" . $v . "</strong><br>";
	}	
    }   
    
    
    /*$delete = array(1, 9, 10, 11, 12, 13, 14, 15);    
    $cb->dbDeleteMultipleRow('gccallback', 'cid', $delete);        
     */
    
    
    /*
     * Update Row {return value]
     * 
     */
    //$result = $cb->dbUpdate('gccallback', 'name', 'shanti', 'cId', 16);   
    /*$result = $cb->dbUpdate('callbackuserenquiry', 'cb_status', 1, 'enq_id', 127);
    
    if ($result){
	echo "row successfully updated";
    } else {
	echo "Row / cId doesn't exit";
    }
    
    /* Select row
     * 
     * $res = $cb->dbSelect('gccallback');
    foreach ($res as $array){
	foreach ($array as $k => $v){
	    echo "key <strong>" . $k . "</strong> has value of: <strong>" . $v . "</strong><br>";
	}	
    }*/
    
    /** Delete Single row
     * 
     */
    /*try {
	$cb->dbDeleteSingleRow('gccallback', 'cid', 8);
    } catch (Exception $e){
	echo $e->getMessage();
    }
    
    /** Delete Multiple row
     * 
     */
    /*try {
	$delete = array(18, 21, 17, 24);
	$cb->dbDeleteMultipleRow('gccallback', 'cid', $delete);
    } catch (Exception $e){
	echo $e->getMessage();
    }*/
    
    
    /**
     * Update row 
     * try catch 
     */
    /*try{
	$cb->dbUpdate('gccallback', 'name', 'shant', 'cId', 22);
    } catch (Exception $e){
	echo $e->getMessage();
    }*/
    
    $tablenames = array(callbackuser, callbackuserenquiry);
    $fieldnames = array(user_id, name, email, telephone, enquiry, callBackDate);
    
    $cb->dbJoinTable($tablenames, $fieldnames, 'user_id');
    
    
   
?>
