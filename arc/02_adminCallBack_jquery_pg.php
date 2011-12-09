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
	try {
	    $acb = new AdminCallBack();
	    $result = $acb->viewAllCallBacks();
	    ?>

    	Total Call Backs: <?php echo count($result) ?>

    	<!-- the input fields that will hold the variables we will use -->
    	<input type='hidden' id='current_page' />
    	<input type='hidden' id='show_per_page' />


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
    		<tbody>
			<?php
			foreach ($result as $r) {
			    //echo $r[callBackDate]."<br/>";
			    $date = date('d.M.Y', $r[callBackDate]) . "<br/>";
			    echo "<tr><td>" . $date . "</td><td>" . $r[name] . "</td><td>" . $r[email] . "</td><td>" . $r[telephone] . "</td><td>" . $r[enquiry] . "</td></tr>";
			    /* foreach ($r as $k => $v) {	    
			      echo $k . " = " . $v;
			      echo "<br>";
			      }
			      echo "<br>"; */
			}
		    } catch (Exception $ex) {
			$ex->getMessage();
		    }
		    ?>
		    
		</tbody>
		<tfoot>
		    <tr>
			<td colspan="5">
			    <!-- An empty div which will be populated using jQuery -->
			    <div id='page_navigation'></div>
			</td>
		    </tr>
		</tfoot>
	    </table>
	</div>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/recordFilter.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="../js/pagination.js"></script>
	
    </body>
</html>