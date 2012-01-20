/**************************************************
 * Display Help Text on Focus In and Foucs Out on 
 * Display Record Input box
 **************************************************/	   
$('#disRecHelpText').hide();
$('#disRecord').focusin(function() {			
    $('#disRecHelpText').show();		
});
$('#disRecord').focusout(function() {		
    $('#disRecHelpText').hide();
});
	    
/**************************************************
 * Sort Table
 **************************************************/
/*
 * Running Table sorter when row doesn't exit cuase the script to terminate
 * preventing other script like date picker from running
 * 
 * Therefore, checking if at least one row exist
 */
$(document).ready(function(){
    //Checks if at least one table row exist
    if ($('#CallBackTable tr').is('.visible')){		  
	//if so use tablesorter
	$(function() {
	    $("table#CallBackTable").tablesorter({ sortList: [[0,1]] });
	});
    } 
});
	    
/**************************************************
 * Paginate Dashboard
 **************************************************/
jQuery(function($){
    $('ul#items').easyPaginate({
	step:3
    });
});
	    
/**************************************************
 * Display BootStrap Alert Message
 **************************************************/
$(".alert-message").alert();	    
	    
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
	    
/**************************************************
 * Date Range Picker 
 **************************************************/	    
$(function() {
    var dates = $( "#from, #to" ).datepicker({
	defaultDate: "+1w",		   
	changeMonth: true,
	numberOfMonths: 1,
	//dateFormat: 'dd/mm/yy',
	onSelect: function( selectedDate ) {
	    var option = this.id == "from" ? "minDate" : "maxDate",
	    instance = $( this ).data( "datepicker" ),
	    date = $.datepicker.parseDate(
	    instance.settings.dateFormat ||
		$.datepicker._defaults.dateFormat,
	    selectedDate, instance.settings );
	    dates.not( this ).datepicker( "option", option, date );
	}
    });
}); 
	
$(document).ready(function(){
    $('#date').click(function() {		    
	$('#fromDate').val($('#from').val());
	$('#toDate').val($('#to').val());
    });
});	