/**************************************************
 * Highlight Dashboard Link Based on current Status 
 **************************************************/
//Get the php variable set above	   	   
var cbStatus = $('#cbStatus').val();

switch (cbStatus){
    case '0':		   	
	$('#unAnsCB').addClass('activeLink');
	break;
    case '1':		    	
	$('#ansCB').addClass('activeLink');		    
	break;
    case '2': 		
	$('#totCB').addClass('activeLink');
	break;
}