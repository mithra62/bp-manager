/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/projects/view/backup-pro
 * @version		3.0
 * @filesource 	./restore.js
 */

jQuery(document).ready(function() {
	
	jQuery("#_restore_direct").on("click", function(e) {
		jQuery("#restore_running_details").show();
		jQuery("#_restore_details_table").hide();
		jQuery(this).hide();
	});
});	