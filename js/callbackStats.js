if (dayRange != 0 && totalRec != 0 && ansRec != 0){ //to avoid js error at begining
		
    /**************************************************
     * CODE BELOW FOR GENERATING STATISTICS
     **************************************************/
    
    /**
     * Construct the multidimentional array based on the day and record passed!
     * 
     * @param int day   no of days retrieved from date range
     * @param int rec   no of record for each day
     */
    function multiDimenArray(day, rec){
	var nOfR = day.length;
	   
	//this unit is a array that holds day and record for e.g [6, 66]
	var unit = new Array();

	//data is multidimentional array that holds no of unit array for 
	//e.g., [[6, 66], [7, 44]]
	var data = new Array();

	for (i=0; i<nOfR; i++){
	    unit = new Array(day[i], rec[i]);
	    data.push(unit);			
	}	       
	return data;
    }	   		
	   
    //Following Script is for generating callback statistics
    var callbackData = multiDimenArray(dayRange, totalRec);		
	    
    //To be Used when data for answered call retrieved
    var answeredData = multiDimenArray(dayRange, ansRec);	    
    
    
    /*
     * control the line and point value based on the day range
     */
    var noOfDays = dayRange.length;  
    var rangeValue;
    if (noOfDays >= 0 && noOfDays <= 31){
	lineValue = 4;    
	pointValue = 4;
    } else if (noOfDays > 31 && noOfDays <= 60) {
	lineValue = 4;
	pointValue = 3;
    } else if (noOfDays > 60 && noOfDays <= 90) {
	lineValue = 3;
	pointValue = 2
    } else if (noOfDays > 90 && noOfDays <= 120){
	lineValue = 3;
	pointValue = 1;
    } else if (noOfDays > 120){
	lineValue = 2;
	pointValue = 0;
    }
							    	    
    var datasets = [ 
    { 
	color: "#CB413B", 
	label: "CallBacks", 		    
	data: callbackData, 
	shadowSize: 4
				
    },
    { 
	color: "#058DC7", 
	label: "Answered", 		     
	data: answeredData, 
	shadowSize: 3				
    }
    ]; 
    var options = {
	series: {			
	    lines: { 
		show: true, 
		lineWidth: lineValue, 
		fill: true, 
		fillColor: "rgba(5, 141, 199, 0.1)"
	    },
	    points: { 
		show: true,
		radius: pointValue, 			    
		fillColor: "#ffffff", 			    
		borderColor: "#ffffff"
	    }
			
	},
	grid: {			   
	    hoverable: true, 
	    clickable: true
	}, 		    
	xaxis: {			
	    mode: "time"
	    //timeformat: "%0d/%m/%y"	    
	}
    };
	
    var plot = $.plot($("#statPlaceholder"), datasets, options);


    /*****************************************************************
    * Following script for showing the tooltips on mouse hover
    ******************************************************************/
   
    function showTooltip(x, y, contents) {
	$('<div id="tooltip">' + contents + '</div>').css( {
	    position: 'absolute',
	    display: 'none',
	    top: y + -15,
	    left: x + 15,			
	    border: '1px solid #000',//#fdd
	    padding: '6px',
	    'background-color': '#fff',//fee			
	    opacity: 0.80			
	}).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;
    $("#statPlaceholder").bind("plothover", function (event, pos, item) {
	$("#x").text(pos.x);
	$("#y").text(pos.y);

	if ($("#enableTooltip:checked").length > 0) {
	    if (item) {
		if (previousPoint != item.dataIndex) {
		    previousPoint = item.dataIndex;

		    $("#tooltip").remove();
		    var x = item.datapoint[0],//date timestamp [for eg ]1324400068000]
		    y = item.datapoint[1];//.toFixed(2);
		    
		    var date = new Date(x);		    		    		    
		    
		    function ddmmyy(){
			return date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear();		
		    }
		   
		    showTooltip(item.pageX, item.pageY,					    
			y + " " + item.series.label + " on " + ddmmyy());
		}
	    }
	    else {
		$("#tooltip").remove();
		previousPoint = null;            
	    }
	}
    });
}

/*****************************************************
 * SWITCH BETWEEN GRAPHICAL STATS AND DASHBOARD
 *****************************************************/

//http://code.google.com/p/cookies/wiki/Documentation#Options_object
var ns = jaaulde.utils.cookies;

//console.log("Dashboard Value: " + ns.get('dashboard'));

$('#viewDashboardBtn').hide();
$('#viewStatBtn').hide();		

//Show Stats Pnl
$('#statPlaceholder').hide();
$('#viewDashboardPnl').hide();

//If cookie is set to stat
if (ns.get('dashboard') == 'stat'){     
    //Display Dashboard Btn
    $('#viewDashboardBtn').show();
    $('#viewStatBtn').hide();		

    //Show Stats Pnl
    $('#statPlaceholder').show();
    $('#viewDashboardPnl').hide();

//If the dashboard value is either set to dashboard || or is null || or is empty then
} else if ((ns.get('dashboard') == 'dashboard') || (ns.get('dashboard') == null) || (ns.get('dashboard') == '')){ 
    //Display the stat button
    $('#viewDashboardBtn').hide();
    $('#viewStatBtn').show();

    //Display the dashboard Pnl
    $('#statPlaceholder').hide();
    $('#viewDashboardPnl').show();
}

//When View Statistic button clicked
$('#viewStatBtn').click(function(){		
    $(this).hide();//hide current button [#viewStat]
    $('#viewDashboardBtn').show();//show View Dashboard button	
    ns.set('dashboard', 'stat');
    //console.log(ns.get('dashboard'));		
    $('#statPlaceholder').show();
    $('#viewDashboardPnl').hide();		
});

$('#viewDashboardBtn').click(function(){		
    $(this).hide();//hide current button [#viewStat]
    $('#viewStatBtn').show();//show View Dashboard button
    ns.set('dashboard', 'dashboard');
    //console.log(ns.get('dashboard'));
    $('#statPlaceholder').hide();
    $('#viewDashboardPnl').show();
}); 