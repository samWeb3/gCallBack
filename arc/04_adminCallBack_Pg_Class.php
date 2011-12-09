<?php
    //Include the PS_Pagination class
    require_once 'gfAdminCallBack_pre.class.php';
    require_once 'ps_pagination_bak.php';
    
    //Connect to mysql db
	$conn = mysql_connect('localhost','root','root123');
	if(!$conn) die("Failed to connect to database!");
	$status = mysql_select_db('griff', $conn);
	if(!$status) die("Failed to select database!");
        
    try {
	$adminCallBack = new AdminCallBack();
	$result = $adminCallBack->viewAllCallBacks();	
	//print_r($result);
	//echo count($result);
	
	$sql = "SELECT callbackuser.user_id, name, email, telephone, enquiry, callBackDate
		FROM callbackuserenquiry, callbackuser
		WHERE callbackuser.user_id = callbackuserenquiry.user_id
		ORDER BY callbackuserenquiry.callBackDate DESC";
	
	
	/*
	 * Create a PS_Pagination object
	 * 
	 * $conn = MySQL connection object
	 * $sql = SQl Query to paginate
	 * 10 = Number of rows per page
	 * 5 = Number of links
	 * "param1=valu1&param2=value2" = You can append your own parameters to paginations links
	 */
	$pager = new PS_Pagination($conn, $sql, 10, 5, "param1=valu1&param2=value2");
	
	/*
	 * Enable debugging if you want o view query errors
	*/
	$pager->setDebug(true);
	
	/*
	 * The paginate() function returns a mysql result set
	 * or false if no rows are returned by the query
	*/
	$rs = $pager->paginate();
	print_r($rs);
	
	if(!$rs) die(mysql_error());
	
	//foreach ($result as $r) {
	while($r = mysql_fetch_assoc($rs)) {
	    $date = date('d.M.Y', $r[callBackDate]);
	    echo "<tr><td>".$date."</td><td>".$r[name]."</td><td>".$r[email]."</td><td>".$r[telephone]."</td><td>".$r[enquiry]."</td></tr>";	
	    echo "<br>";
	}
	
	//Display the full navigation in one go
	echo $pager->renderFullNav();
	
	echo "<br />\n";
    } catch (Exception $ex){
	echo $ex->getMessage();
    }
?>

