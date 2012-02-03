/**************************************************
 * Highlight Dashboard Link Based on current Status 
 **************************************************/
//Get the php variable set above	   	   
var quoteStatus = $('#quoteStatus').val();

switch (quoteStatus){
    case '0':		   	
	$('#unAnsQtReq').addClass('activeLink');
	break;
    case '1':		    	
	$('#ansQtReq').addClass('activeLink');		    
	break;
    case '2': 		
	$('#totQtReq').addClass('activeLink');
	break;
}

