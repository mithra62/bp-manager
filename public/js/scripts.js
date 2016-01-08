var oTable;
$(document).ready(function() {


	// Closing Divs - used on Notification Boxes
	$(".canhide").append("<div class='close-notification png_bg'></div>").css("position", "relative");
	$(".close-notification").click(function() {
		$(this).hide();
		$(this).parent().fadeOut(700);
	});
	
	// Load Facebox - simple add "rel="facebook" to any link to activate Modal Dialog
	$('a[rel*=facebox]').facebox();
	
	// Load Table Sorter - change this if you only want to sort a particular table.

    /*
    oTable = $('table.tablesorter').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
	*/
	
	$("#tasks_due_tomorrow").dragCheck('td');
	$("#moji_times").dragCheck('td');
	$("#moji_invoice_times").dragCheck('td');
    
    $('#tasks_overdue').dataTable({
		"bJQueryUI": true,
		"bFilter": false,
		"bPaginate": false
	});
    
    $("#tasks_overdue").dragCheck('td');
    
    $('#tasks_due_tomorrow').dataTable({
		"bJQueryUI": true,
		"bFilter": false,
		"bPaginate": false
	});
    
    $('#tasks_due_today').dataTable({
		"bJQueryUI": true,
		"bFilter": false,
		"bPaginate": false
	});
    
    $("#tasks_due_today").dragCheck('td');
    
    $('#tasks_due_week').dataTable({
		"bJQueryUI": true,
		"bFilter": false,
		"bPaginate": false
	});
    
    $("#tasks_due_week").dragCheck('td');
    
    $('#tasks_upcoming').dataTable({
		"bJQueryUI": true,
		"bFilter": false,
		"bPaginate": false
	});
    
    $("#tasks_upcoming").dragCheck('td');
    
    $('#moji_projects').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false,
		"sCookiePrefix": false
	});  
    
    $('#moji_proj_team').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});      
    
    $('#moji_tasks').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": true,
		"sCookiePrefix": ''
	}); 
    
    $('#moji_task_assign').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});     
    
    $('#moji_companies').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});
    
    $('#moji_bookmarks').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});
    
    $('#moji_notes').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});
    
    $('#moji_contacts').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});   
    
    $('#moji_times').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false,
		"aaSorting": [[ 0, "desc" ]]
	});  
    
    $('#moji_users').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	}); 
    
    $('#moji_invites').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	}); 
    
    $('#project_team').dataTable({
		"bJQueryUI": true,
		"bFilter": false,
		"bPaginate": false
	});     
    
    $('#moji_roles').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});   

    $('#moji_ips').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});     
    
    $('#moji_permissions').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	}); 
    
    $('#moji_files').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});
    
    $('#file_revisions').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false,
		"aaSorting": [[ 0, "desc" ]]
	});  
    
    $('#file_reviews').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});   
    
    $('#moji_invoices').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": false
	});   
    

    $('#moji_invoice_times').dataTable({
		"bJQueryUI": true,
		"bStateSave": false,
        "bLengthChange": false,
        "bPaginate": false,
		"aaSorting": [[ 0, "desc" ]]
	}); 
    
    $('#dateSelect').bind('change', function () {
        var url = $(this).val(); // get selected value
        if (url) { // require a URL
            window.location = url; // redirect
        }
        return false;
    });
    
// Closing jQuery
});