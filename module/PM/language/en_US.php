<?php

return array(
	
	//global 
	'help' => 'Help',
	'email' => 'Email',
	'edit' => 'Edit',
	'delete' => 'Delete',
	'title' => 'Title',
	'last_modified' => 'Last Modified',
	'created_date' => 'Created Date',
	'description' => 'Description',
	'something_went_wrong' => 'Something went wrong...',
	'please_fix_the_errors_below' => 'Please fix the errors below.',
	'no' => 'No',
	'yes' => 'Yes',
		
	//admin
	'settings_updated' => 'Settings updated!',
		
	//account
	'prefs_updated' => 'Preferences updated!',
	'password_changed' => 'Password Changed',
	
	//users controller
	'user_added' => 'User Added!',
	'user_updated' => 'User Updated!',
	'edit_user' => 'Edit User',
	'user_cant_remove_self' => 'You can\'t remove yourself',
	'user_removed' => 'User Removed!',
	'view_user' => 'View User',
	'home_phone' => 'Home Phone',
	'work_phone' => 'Work Phone',
	'fax' => 'Fax',
	'mobile_phone' => 'Mobile Phone',
		
	//user role permissions 
	'permission_admin_access' => 'Can the user access the admin area? Required for all admin modules.',
	'permission_manage_companies' => 'Can the user manage the companies?',
	'permission_manage_company_contacts' => 'Can the user manage the company contacts?',
	'permission_manage_files' => 'Can the user manage files?',
	'permission_manage_invoices' => 'Can the user manage invoices?',
	'permission_manage_ips' => 'Can the user manage allowable IP addresses?',
	'permission_manage_options' => 'Can the user manage the available type options?',
	'permission_manage_projects' => 'Can the user manage projects? This includes the management of the "project team".',
	'permission_manage_roles' => 'Can the user manage user roles and permissions?',
	'permission_manage_tasks' => 'Can the user manage tasks?',
	'permission_manage_time' => 'Can the user manage the time of others?',
	'permission_manage_users' => 'Can the user manage other users?',
	'permission_track_time' => 'Can the user track their time?',
	'permission_view_companies' => 'Can the user view companies? Required for pretty much everything.',
	'permission_view_company_contacts' => 'Can the user view the company contacts?',
	'permission_view_files' => 'Can the user view files?',
	'permission_view_invoices' => 'Can the user view invoices?',
	'permission_view_projects' => 'Can the user view projects?',
	'permission_view_tasks' => 'Can the user view tasks?',
	'permission_view_time' => 'Can the user view time tracker data?',
	'permission_view_users_data' => 'Can the user view other users data?',
	'permission_access_rest_api' => 'Can the user access the REST API?',
	'permission_self_allow_ip' => 'Can the user allow their own IP Address?',
		
	//roles controller
	'user_roles' => 'User Roles',
	'role_updated' => 'Role Updated!',
	'update_role_fail' => 'Couldn\'t update role...',
	'role_added' => 'Role Added!',
	'remove_user_role' => 'Remove User Role',
	'cant_remove_role' => 'You can\'t remove this role.',
	'role_removed' => 'Role Removed!',
	'user_roles_updated' => 'User Roles Updated!',
		
	//companies 
	'contact_updated' => 'Company updated!',
	'cant_update_company' => 'Couldn\'t update company...',	
	'company_added' => 'Company Added!',
	'company_removed' => 'Company Removed',
		
	//company contacts
	'contact_updated' => 'Contact Updated!',
	'cant_update_contact' => 'Can\'t Update Contact',
	'contact_added' => 'Contact Added',
	'contact_removed' => 'Contact Removed',
		
	//projects
	'projects' => 'Projects',
	'project_updated' => 'Project Updated!',
	'project_added' => 'Project Added!',
	'cant_update_project' => 'Couldn\'t update project...',
	'project_team_modified' => 'Project Team Modified',
	'project_removed' => 'Project Removed!',

	//tasks
	'tasks_updated' => 'Task(s) Updated!',
	'task_added' => 'Task Added!',
	'task_removed' => 'Task Removed!',
	'task_updated' => 'Task updated!',
	'cant_update_task' => 'Couldn\'t update task...',
	
	//notes
	'note_updated' => 'Note Updated!',
	'note_added' => 'Note Added!',
	'note_removed' => 'Note Removed!',	

	//bookmarks
	'bookmark_updated' => 'Bookmark Updated!',
	'cant_update_bookmark' => 'Couldn\'t update bookmark...',
	'bookmark_added' => 'Bookmark Added!',
	'bookmark_removed' => 'Bookmark Removed',
		
	//files
	'file_added' => 'File Added!',
	'file_removed' => 'File Removed!',
	'file_updated' => 'File Updated!',
	'file_not_found' => 'File Not Found',
	'cant_update_file' => 'Can\'t Update File',
	'cant_upload_file' => 'Can\'t Upload File',
	'cant_remove_file' => 'Can\'t Remove File',
		
	//file revisions
	'file_revision_added' => 'File Revision Added!',
	'file_revision_removed' => 'File Revision Removed!',
	
	//input validation messages
	'required' => 'Required',

	//options
	'option_added' => 'Option Added!',
	'option_updated' => 'Option Updated!',
	'option_removed' => 'Option Removed!',
		
	//times
	'time_added' => 'Time Added!',
	'time_removed' => 'Time Removed!',
		
	//timers
	'timer_stopped' => 'Timer Stopped',
	'timer_started' => 'Timer Started',
	'timer_removed' => 'Timer Removed',
		
	//ip locker
	'ip_locker_disabled' => 'Ip Blocking Disabled!',
	'ip_locker_enabled' => 'Ip Blocking Enabled!',
	'ip_address_added' => 'IP Address Added!',
	'ip_address_updated' => 'IP Address Updated!',
	'cant_update_ip_address' => 'Couldn\'t update Ip Address...', 
	'ip_locker_enabled_message' => 'The MojiTrac administrator has enabled IP restrictions, so only authorized people can access the site. In order to continue you\'ll need to contact the Administrator and have them allow your IP address.',
	'ip_locker_enabled_allow_self_message' => 'The MojiTrac administrator has enabled IP restrictions, so only authorized people can access the site. In order to continue you\'ll need to verify your account ownership to allow requests from your location.',	
	'ip_allow_verify_sent' => 'Email Sent! Check your email to continue.',
	'ip_allow_bad_code' => 'Woops! Invalid code ya got there...',	
	'ip_allow_code_access_sucess' => 'Your IP Address has been whitelisted!',
	'cant_remove_own_ip' => 'You can\'t remove your current IP Address.',
		
	//invoices
	'invoice_updated' => 'Invoice Updated!',
	'invoice_removed' => 'Invoice Removed!',
	'cant_update_invoice' => 'Couldn\'t update invoice...',
	'invoice_added' => 'Invoice Added!',
		
	//emails
	'email_subject_task_status_change' => 'Task status changed',
	'email_subject_task_priority_change' => 'Task priority changed',
	'email_subject_task_assigned' => 'Task assigned to you',
	'email_subject_project_team_remove' => 'Removed from project team',
	'email_subject_project_team_add' => 'Added to project team',	
	'daily_task_reminder_email_subject' => 'Daily Task Reminder',
	'email_subject_file_add' => 'File Uploaded',
	'user_registration_email_subject' => 'New MojiTrac Account Created!',
	'email_subject_file_revision_add' => 'File Revision Uploaded',
	'email_subject_ip_self_allow' => 'MojiTrac IP Allow Request',
		
	'sent_by_moji' => 'Sent By: MojiTrac',
	
);