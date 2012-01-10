if (dayRange != 0 && callBackRec != 0 && ansCBRec != 0){ //to avoid js error at begining
		
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
    var callbackData = multiDimenArray(dayRange, callBackRec);		
	    
    //To be Used when data for answered call retrieved
    var answeredData = multiDimenArray(dayRange, ansCBRec);	    	  
							    	    
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
		lineWidth: 4, 
		fill: true, 
		fillColor: "rgba(5, 141, 199, 0.1)"
	    },
	    points: { 
		show: true,
		radius: 4, 			    
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