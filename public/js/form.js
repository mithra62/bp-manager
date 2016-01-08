var nav_away = true;

jQuery(document).ready(function($){
	
	//start date picker for start_date
	$('#start_date').DatePicker({
		format:'Y-m-d',
		date: $('#start_date').val(),
		current: $('#start_date').val(),
		starts: 1,
		position: 'r',
		eventName: 'focus',
		onBeforeShow: function(){
			var default_start_date = ($('#start_date').val() != '' ? $('#start_date').val() : $('#start_date_default').val())
			$('#start_date').DatePickerSetDate(default_start_date, true);
		},
		onChange: function(formated, dates){
			$('#start_date').val(formated);
			$('#start_date').DatePickerHide();
			
		}
	});
	
	//start date picker for end_date
	$('#end_date').DatePicker({
		format:'Y-m-d',
		date: $('#end_date').val(),
		current: $('#end_date').val(),
		starts: 1,
		position: 'r',
		eventName: 'focus',
		onBeforeShow: function(){
		var default_end_date = ($('#end_date').val() != '' ? $('#end_date').val() : $('#end_date_default').val())
			$('#end_date').DatePickerSetDate(default_end_date, true);
		},
		onChange: function(formated, dates){
			$('#end_date').val(formated);
			$('#end_date').DatePickerHide();
			
		}
	});
	
	$('#date').DatePicker({
		format:'Y-m-d',
		date: $('#date').val(),
		current: $('#date').val(),
		starts: 1,
		position: 'r',
		eventName: 'focus',
		onBeforeShow: function(){
		var default_end_date = ($('#date').val() != '' ? $('#date').val() : $('#date_default').val())
			$('#date').DatePickerSetDate(default_end_date, true);
		},
		onChange: function(formated, dates){
			$('#date').val(formated);
			$('#date').DatePickerHide();
			
		}
	});	
	
	$('#company_id').chainSelect('#project_id','/api/chain-projects',
	{ 
		before:function (target) //before request hide the target combobox and display the loading message
		{ 
			$("#loading").css("display","block");
			$(target).css("display","none");
			$("#projects").css("display","inline");
			$("#tasks").css("display","none");
		},
		after:function (target) //after request show the target combobox and hide the loading message
		{ 
			$("#loading").css("display","none");
			$(target).css("display","inline");
		}
	});
	
	$('#project_id').chainSelect('#task_id','/api/chain-tasks',
	{ 
		before:function (target) 
		{ 
			$("#loading").css("display","block");
			$(target).css("display","none");
			$("#tasks").css("display","inline");
		},
		after:function (target) 
		{ 
			$("#loading").css("display","none");
			$(target).css("display","inline");
		}
	});	
	
	//$('#description').wysiwyg(); // Create the editor	
	$('#form_tabs').tabs();
	
	// ":not([safari])" is desirable but not necessary selector
	$('input:checkbox').checkbox();
	$('input:radio').checkbox();
	
	$("#new_password").passStrength({
		shortPass: 		"top_shortPass",
		badPass:		"top_badPass",
		goodPass:		"top_goodPass",
		strongPass:		"top_strongPass",
		baseStyle:		"top_testresult",
		userid:			"#old_password",
		enableUcheck:	0,
		messageloc:		0
	});	
	
	$("#password").passStrength({
		shortPass: 		"top_shortPass",
		badPass:		"top_badPass",
		goodPass:		"top_goodPass",
		strongPass:		"top_strongPass",
		baseStyle:		"top_testresult",
		enableUcheck:	1,
		userid:			"#email",
		messageloc:		0
	});	
});