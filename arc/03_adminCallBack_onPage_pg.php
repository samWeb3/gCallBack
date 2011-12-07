<?php
require_once '../class/gfAdminCallBack_pre.class.php';
?>

<!DOCTYPE html>
<html>
    <head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css">
	    html, body, div, ol, ul, dl, dd, dt, fieldset, p, form, h1, h2, h3, h4, h5, iframe, blockquote, pre, img, label, legend, strong, span, em, table, caption, tbody, tfoot, thead, tr, th, td {
		font-family: inherit;
		font-style: inherit;
		font-weight: inherit;
		margin: 0;
		padding: 0;
	    }
	    body {
		font: 75%/1.6em "Lucida Grande",Verdana,Geneva,Helvetica,Arial,sans-serif;
	    }
	    ol, ul {
		list-style: none outside none;
	    }
	    body {
		font-family: Arial;
		font-size: 12px;
	    }
	    ul li span.leftWidth{
		float: left;
		width: 125px !important;
		list-style: none;		
	    }
	    li, h1 { padding-bottom: 20px;}
	    .warning {
		color: #f00;
		font-weight: bold;
	    }


	</style>

	<link rel="stylesheet" href="../css/style.css" type="text/css" media="screen" charset="utf-8">
	
    </head>
    <body>
	
	<?php
	require_once('mysql_connect.php');

	//***************************************************************************
	//********************SECTION 1 FOR PEGINATION STARTS**************************
	//To display number of record per page
	$display = 4;

	//Determine how many pages there are.
	if (isset($_GET['np'])) {//Already been determined
	    $num_pages = $_GET['np'];
	} else { //Need to determine.
	    //Count the number of records
	    $query = "SELECT COUNT(*) FROM callbackuserenquiry";// ORDER BY callBackDate ASC";
	    $result = mysql_query($query);
	    $row = mysql_fetch_array($result, MYSQL_NUM);
	    $num_records = $row[0];


	    //Calculate the number of pages.
	    if ($num_records > $display) { //More than 1 page.
		$num_pages = ceil($num_records / $display);
	    } else {
		$num_pages = 1;
	    }
	}//End of np IF
	//Determine where in the database to start returning results.
	if (isset($_GET['s'])) {
	    $start = $_GET['s'];
	} else {
	    $start = 0;
	}
	//***************************************************************************
	//********************SECTION 1 FOR PEGINATION ENDS**************************

	try {
	    $acb = new AdminCallBack();
	    $result = $acb->viewCallBack($start, $display);
	    ?>
    		
	    Total Call Backs: <?php echo count($result) ?>
    	    
    	    
	    <div id="middle">
		<div id="search">
		    <label for="filter">Filter Record: </label> <input type="text" name="filter" value="" id="filter" />
		</div>
		<table cellpadding="1" cellspacing="1" id="resultTable">
		    <thead>
			<tr>
			    <th>Date: </th>
			    <th>Name: </th>
			    <th>Email: </th>
			    <th>Phone No: </th>
			    <th>Enquiry</th>
			</tr>
		    </thead>
			<?php
			    if ($result) {//if query ran successfully, display the result                         
				foreach ($result as $r) {
				    $date = date('d.M.Y', $r[callBackDate]) . "<br/>";
				    echo "<tr><td>" . $date . "</td><td>" . $r[name] . "</td><td>" . $r[email] . "</td><td>" . $r[telephone] . "</td><td>" . $r[enquiry] . "</td></tr>";
				}


				//********************SECTION 2 FOR PEGINATION STARTS************************
				//*************************DISPLAYES PAGE NUMBER*****************************
				if ($num_pages > 1) {

				    //Determine what page the script is on
				    $current_page = ($start / $display) + 1;

				    //If it's not the first page, make a previous button
				    if ($current_page != 1) {
					echo '<a class="pagenatelink" href="'.$_SERVER[PHP_SELF].'?s=' . ($start - $display) . '&np=' . $num_pages . '"><font class="mainbody">Previous</font></a>&nbsp;&nbsp;';
				    }

				    //Make all the numbered pages.
				    for ($i = 1; $i <= $num_pages; $i++) {
					if ($i != $current_page) {
					    echo '<a class="pagenatelink" href="'.$_SERVER[PHP_SELF].'?s=' . (($display * ($i - 1))) . '&np=' . $num_pages . '"><font class="mainbody">' . $i . '</font></a>&nbsp;&nbsp;';
					} else {
					    echo '<b>' . $i . '</b>&nbsp;&nbsp;';
					}
				    }

				    //If it's not the last page, make the next button.
				    if ($current_page != $num_pages) {
					echo '<a class="pagenatelink" href="'.$_SERVER[PHP_SELF].'?s=' . ($start + $display) . '&np=' . $num_pages . '"><font class="mainbody">Next</font></a>';
				    }

				    echo '</p>';
				} //End of links section.
				//***************************************************************************
				//********************SECTION 2 FOR PEGINATION ENDS**************************
			    } else {//if query didn't ran then display following error message                          
				$error_message = "<i>Couldn't run SQL Query. Check database connection / database name / database table</i>";

				exit();
			    }
			} catch (Exception $ex) {
			    $ex->getMessage();
			}
		    ?>
		    
		    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/recordFilter.js" type="text/javascript" charset="utf-8"></script>
<body>
</html>
			
			