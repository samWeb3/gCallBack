//http://net.tutsplus.com/tutorials/javascript-ajax/using-jquery-to-manipulate-and-filter-data/
$(document).ready(function() {    
	
    $('#CallBackTable tbody tr').hover(function(){
	$(this).find('#CallBackTable td').addClass('hovered');
    }, function(){
	$(this).find('#CallBackTable td').removeClass('hovered');
    });
	
    //default each row to visible
    $('#CallBackTable tbody tr').addClass('visible');
	
    //overrides CSS display:none property
    //so only users w/ JS will see the
    //filter box
    $('#search').show();
	
    $('#filter').keyup(function(event) {
	//if esc is pressed or nothing is entered
	if (event.keyCode == 27 || $(this).val() == '') {
	    //if esc is pressed we want to clear the value of search box
	    $(this).val('');
			
	    //we want each row to be visible because if nothing
	    //is entered then all rows are matched.
	    $('#CallBackTable tbody tr').removeClass('visible').show().addClass('visible');
	}

	//if there is text, lets filter
	else {
	    filter('#CallBackTable tbody tr', $(this).val());
	}	
    });
	
    //grab all header rows
    $('#CallBackTable thead th').each(function(column) {
	$(this).addClass('sortable')
	.click(function(){
	    var findSortKey = function($cell) {
		return $cell.find('.sort-key').text().toUpperCase() + ' ' + $cell.text().toUpperCase();
	    };
						
	    var sortDirection = $(this).is('.sorted-asc') ? -1 : 1;
						
	    //step back up the tree and get the rows with data
	    //for sorting
	    var $rows		= $(this).parent()
	    .parent()
	    .parent()
	    .find('#CallBackTable tbody tr')
	    .get();
						
	    //loop through all the rows and find 
	    $.each($rows, function(index, row) {
		row.sortKey = findSortKey($(row).children('#CallBackTable td').eq(column));
	    });
						
	    //compare and sort the rows alphabetically
	    $rows.sort(function(a, b) {
		if (a.sortKey < b.sortKey) return -sortDirection;
		if (a.sortKey > b.sortKey) return sortDirection;
		return 0;
	    });
						
	    //add the rows in the correct order to the bottom of the table
	    $.each($rows, function(index, row) {
		$('#CallBackTable tbody').append(row);
		row.sortKey = null;
	    });
						
	    //identify the column sort order
	    $('#CallBackTable th').removeClass('sorted-asc sorted-desc');
	    var $sortHead = $('#CallBackTable th').filter(':nth-child(' + (column + 1) + ')');
	    sortDirection == 1 ? $sortHead.addClass('sorted-asc') : $sortHead.addClass('sorted-desc');
						
	    //identify the column to be sorted by
	    $('#CallBackTable td').removeClass('sorted')
	    .filter(':nth-child(' + (column + 1) + ')')
	    .addClass('sorted');
	});
    });
});


//used to apply alternating row styles
function zebraRows(selector, className){
    $(selector).removeClass(className).addClass(className);
}

//filter results based on query
function filter(selector, query) {
    query = $.trim(query); //trim white space
    query = query.replace(/ /gi, '|'); //add OR for regex
  
    $(selector).each(function() {
	($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).hide().removeClass('visible') : $(this).show().addClass('visible');
    });
}


