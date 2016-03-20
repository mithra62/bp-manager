/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/projects/view/backup-pro
 * @version		3.0
 * @filesource 	./dashboard.js
 */

jQuery(document).ready(function() {

	jQuery('#NewStorageDropdown').change(function(){
		window.location = jQuery("#NewStorageDropdown").val();
	});

	//check all checkboxes
	jQuery('.bp_toggle_all').change(function () {
		jQuery("input:checkbox").prop('checked', jQuery(this).prop("checked"));
	});
	//end checkboxes
	
	//backup note editable
	jQuery(".bp_editable").on("click", function(e) {
		
		var file_id = "#note_"+jQuery(this).attr("rel");
		var note_div = "#note_div_"+jQuery(this).attr("rel");
		var note_html = "#note_div_"+jQuery(this).attr("rel");
		var backup_type = jQuery(file_id).attr("data-backup-type");
		var def_value = jQuery(file_id).val();
		
		//first, prevent using Enter to submit the parent form
		jQuery(file_id).bind("keypress", function(e) {
			  var code = e.keyCode || e.which; 
			  if (code  == 13) 
			  {               
			    e.preventDefault();
			    bp_save_note(note_div, file_id, backup_type);
			    return false;
			  }
		});	
		
		jQuery(document).keyup(function(e) {
			  if (e.keyCode == 27) { 
				  jQuery(note_div).html(jQuery(note_html).html()).show();
				  jQuery(file_id).val(def_value);
				  jQuery(file_id).hide();
			  }   // esc
		});		

		//now do first display
		jQuery(this).hide();
		jQuery(file_id).show();
		jQuery(file_id).focus();
		jQuery(file_id).on("blur", function(e) {
			jQuery(note_div).html(jQuery(note_html).html()).show();
			jQuery(file_id).hide();
		});
	});
	//end backup note editable
	
});	