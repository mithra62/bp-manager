<?php
/**
 * Sites - Language file
 * 
 * Contains all the variables for localization
 *
 * @package		Localization
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Sites/language/en_US.php
 */
return array(
    'site_required_to_begin' => 'You must have at least 1 site configured to continue...',
    'sites' => 'Sites',
    'add_site' => 'Add Site',
    'edit_site' => 'Edit Site',
    'view_site' => 'View Site',
    'remove_site' => 'Remove Site',
    'site_added' => 'Site Added',
    'site_updated' => 'Site Updated',
    'site_removed' => 'Site Removed',
    'manage_sites' => 'Manage Sites',
    'existing_sites' => 'Existing Sites',
    'site_name' => 'Site Name',
    'platform' => 'Platform',
    'dashboard' => 'Dashboard',
    'backup_database' => 'Backup Database',
    'backup_files' => 'Backup Files',
    'settings' => 'Settings',
    
    'api_endpoint_url' => 'API Endpoint URL',
    'api_key' => 'API Key',
    'api_secret' => 'API Secret',
    
    'api_endpoint_url_required' => 'An API Endpoint URL is required',
    'api_key_required' => 'An API Key is required',
    'api_secret_required' => 'An API Secret is required',
    'create_site_exist_error' => 'That API is already being monitored',
    'api_access_invlaid' => 'For some reason we can\'t access your site anymore; update your connection detais to continue.',
    
    
    
    
    'general_bp_settings_menu' => 'General',
    'db_bp_settings_menu' => 'Database Backup',
    'files_bp_settings_menu' => 'File Backup',
    'cf_bp_settings_menu' => 'Cloud Files',
    'ftp_bp_settings_menu' => 'FTP',
    's3_bp_settings_menu' => 'Amazon S3',
    'gcs_bp_settings_menu' => 'Google Cloud',
    'cron_bp_settings_menu' => 'Cron Backup',
    'integrity_agent_bp_settings_menu' => 'Integrity Agent',
    'local_storage_bp_settings_menu' => 'Local Storage',
    'storage_location_bp_settings_menu' => 'Storage Locations',
    'license_bp_settings_menu' => 'License Details',
    'storage_bp_settings_menu' => 'Storage Locations',
    'rest_api_bp_settings_menu' => 'Rest API',
    'api_bp_settings_menu' => 'Rest API',
    
    
    
    
    
    
    
    
    
    'backup_pro_module_name' => 'Backup Pro 3',
    'backup_pro_module_description' => 'Interface to create database and file backups of your site. ',
    'start_backup' => 'Start Backup',
    'status' => 'Status',
    'backup_files' => 'Backup Files',
    'backup_database' => 'Backup Database',
    'can_not_backup' => 'Can\'t backup...',
    'no_backups' => 'No Backups',
    'backup_delete_failure' => 'Backup Remove Failed!',
    'backups' => 'Backups',
    'action_can_not_be_undone' => 'Be careful, this can\'t be undone...',
    'database_backups' => 'Database Backups',
    'backup_db' => 'Backup Database',
    'db_backup_created' => 'Database Backup Created',
    'file_backup_created' => 'File Backup Created',
    'database_backups' => 'Database Backups',
    'backup_files' => 'Backup Files',
    'file_backups' => 'File Backups',
    'delete_backup' => 'Delete Backups',
    'delete' => 'Delete',
    'restore' => 'Restore',
    'db_backup_not_found' => 'Database Download Not Found',
    'file_backup_failure' => 'Couldn\'t Create File Backup',
    'db_backup_failure' => 'Couldn\'t Create Database Backup',
    'backups_not_found' => 'Backup(s) Not Found',
    'delete_backups' => 'Delete Backups',
    'delete_backup_confirm' => 'Are you sure you want to remove the below backups?',
    'restore_db_question' => 'Are you sure you want to restore the below database?',
    'database_restored' => 'Database Restored!',
    'backup_file_location' => 'File Backup Location',
    'backup_file_locations' => 'File Backup Locations',
    'backup_file_location_instructions' => 'Put simply; what do you want to include for the file backup? Put the full path, one per line that you want to backup. ',
    'working_directory' => 'Working Directory',
    'working_directory_instructions' => 'Where do you want to store the backup meta details? This directory will contain the details of all your backups so choose wisely and securely. Remember to make this directory writable by your webserver so chmod it to either 0666 or 0777.',
    'configure_backups' => 'Backup Settings',
    'configure_cron' => 'Configure Cron',
    'enable_cron' => 'Enable Cron',
    'cron_control' => 'Cron Control',
    'cron_notify_emails' => 'Notification Emails',
    'cron_notify_emails_instructions' => 'The email addresses who should recieve a notification upon successful Cron execution.',
    'cron_attach_backups' => 'Attach Backups To Email',
    'cron_attach_backups_instructions' => 'By default Backup Pro will send a link to download the email but if you\'d like to have the backup files sent as an attachment with the notification email we can do that too. ',
    'auto_threshold_instructions' => 'To keep things sane Backup Pro can watch the space used and respond accoringly by removing older backups to make space for newer backups.',
    'auto_threshold' => 'Auto Prune Threshold',
    'auto_threshold_custom' => 'Auto Prune Threshold Custom',
    'auto_threshold_custom_instructions' => 'Enter the number, in bytes, that you want to limit backups to. ',
    
    'allowed_access_levels' => 'Allowed Access Levels',
    'allowed_access_levels_instructions' => 'Backup Pro will initially only allow Super Admins access the settings, regardless of who can access the module, but if you need to allow other groups select them from the list.',
    'settings_updated' => 'Settings Updated',
    'settings_update_fail' => 'Couldn\'t Update Settings',
    
    'cron_command_instructions' => 'Use the below commands for your Cron based on the type of backup you\'d like to automate. ',
    'cron_control_instructions' => 'To make sure requests to the Cron functionality is secured you have to include a random query paramater to each request. Initially, Backup Pro creates this for you but if you\'d like to change it do so here',
    'exclude_paths' => 'Exclude Paths',
    'exclude_paths_instructions' => 'By default Backup Pro will backup everything within your site\'s document root but for some sites that just won\'t work. If you want to exclude anything from the backup put the full path to the document or file here, one per line. ',
    
    'files_dir_not_writable' => 'Files backup directory is not writable. Make sure the permissions for "#files_dir#" are set to 0666 or 0777.',
    'files_dir_missing' => 'Files backup directory is missing. Make sure "#files_dir#" exists and is writable.',
    'db_dir_not_writable' => 'Database backup directory is not writable. Make sure the permissions for "#db_dir#" are set to 0666 or 0777.',
    'db_dir_missing' => 'Database backup directory is missing. Make sure "#db_dir#" exists and is writable.',
    'database' => 'Database',
    'type' => 'Type',
    'restore_db' => 'Restore Database',
    'backups_deleted' => 'Backups Deleted',
    'back_dir_not_writable' => 'The backup directory isn\'t writable!',
    'module_instructions' => 'Backup Pro is an advanced backup management module for EE 2.0 that allows administrators the ability to
						  backup and restore their site\'s database as well as backing up the entire file system. Both the
						  files and database backups are compressed to save space and available for download.',
    
    						  'cron_txt_message' => '',
    
    						  'file_backup' => 'File Backup',
    						  'db_backup' => 'Database Backup',
    						  'console_file_backup' => 'File Backup (Console)',
    						  'console_db_backup' => 'File Database (Console)',
    						  'combined' => 'Combined Backup (both file and database in one run)',
    
    						  'log_database_backup' => 'Database backup taken.',
    						  'log_file_backup' => 'File backup taken.',
    						  'log_backup_downloaded' => 'Backup downloaded.',
    						  'log_backup_deleted' => 'Backups deleted.',
    						  'log_settings_updated' => 'Backup Pro settings updated',
    						  'restore_in_progress' => 'Restore Running...',
    						  'restore_in_progress_instructions' => '<strong>DO NOT DO THE FOLLOWING UNTIL THE RESTORE IS COMPLETE:</strong><br />
    1. Close your browser<br />
    2. Reload this page<br />
    3. Navigate away from this page<br />',
    
        'backup_in_progress_instructions' => '<strong>DO NOT DO THE FOLLOWING UNTIL THE BACKUP IS COMPLETE:</strong><br />
    1. Close your browser<br />
    2. Reload this page<br />
    3. Navigate away from this page<br />
',
    'backup_in_progress' => 'Backup Running...',
    'backup_progress_bar_start' => 'Backup Starting...',
    'backup_progress_bar_table_start' => 'Starting backup of table: ',
    'backup_progress_bar_table_stop' => 'Completed backup of table: ',
    'backup_progress_bar_database_stop' => 'Completed database backup.',
    'backup_progress_bar_start_file_exclude' => 'Starting file exclusion list...',
    'backup_progress_bar_stop_file_exclude' => 'Completed file exclusion list...',
    'backup_progress_bar_start_file_list' => 'Starting file generation list...',
    'backup_progress_bar_stop_file_list' => 'Completed file generation list...',
    'backup_progress_bar_create_archive' => 'Creating the archive...',
    'backup_progress_bar_start_s3' => 'Starting transfer to S3 (this may take a minute)...',
    'backup_progress_bar_stop_s3' => 'Completed transfer to S3...',
    'backup_progress_bar_start_ftp' => 'Starting FTP transfer to remote server (this may take a minute)...',
    'backup_progress_bar_stop_ftp' => 'Completed FTP transfer to remote server',
    'backup_progress_bar_start_cf' => 'Starting transfer to Rackspace Cloud (this may take a minute)...',
    'backup_progress_bar_stop_cf' => 'Completed transfer to Rackspace Cloud...',
    'invalid_license_number' => 'Your license number is invalid. Please <a href="#config_url#">enter your valid license</a> or <a href="https://mithra62.com/projects/view/backup-pro">buy a license</a>.',
    
    'backup_progress_bar_stop' => 'Backup Completed!',
    
    'max_db_backups' => 'Maximum Database Backups',
    'max_db_backups_instructions' => 'Enter the maximum amount of database backups you want to store locally. Note that only local backups (remote and local) will be removed. Enter 0 to disable.',
    'max_file_backups' => 'Maximum File Backups',
    'max_file_backups_instructions' => 'Enter the maximum amount of file backups you want to store locally. Note that only local backups (remote and local) will be removed. Enter 0 to disable.',
    
    'date_format' => 'Date Format',
    'date_format_instructions' => 'The date format you want to use when displaying backups. Note that the format should conform to the <a href="http://php.net/manual/en/function.date.php" target="_blank">PHP date format</a>.',
    
    'nav_backup_pro' => 'Backup Pro',
    'nav_backup_db' => 'Backup Database',
    'nav_backup_files' => 'Backup Files',
    'nav_dashboard' => 'Dashboard',
    'nav_backup_pro_settings' => 'Settings',
    'db_backup_method' => 'Database Backup Method',
    'db_backup_method_instructions' => 'Depending on how your system is setup the default mysqldump method may not work. Essentially, if you have a the "system" command disabled on your PHP server you should use the PHP method but if you\'re having performance issues you should use MySQLDUMP.',
    'db_restore_method' => 'Database Restore Method',
    'db_restore_method_instructions' => 'The Database restore method to use. MySQL requires access to the "system" PHP function and the "mysql" system command. Note that this is dependant on the backup method used. PHP restore can only restore PHP backups but MySQL can restore either backup method. This is handled gracefully for backwards compatibility.',
    'mysqlcli_command' => 'MySQL Command',
    'mysqlcli_command_instructions' => 'If you want to cusomize the command used to execute MySQL just update the below field. Be sure NOT to enter any credentials! ',
    
    'no_backups_exist' => 'No backups exist yet.',
    'no_database_backups' => 'No database backups exist yet.',
    'no_file_backups' => 'No file backups exist yet.',
    'would_you_like_to_backup_now' => 'Would you like to take a backup now?',
    'would_you_like_to_backup_database_now' => 'Would you like to take a database backup now?',
    'would_you_like_to_backup_files_now' => 'Would you like to take a file backup now?',
    
    'config_db' => 'Configure Database Backups',
    'config_files' => 'Configure File Backups',
    
    'backup_state_unstable' => 'A backup hasn\'t been taken in over 6 months. You should take a backup ASAP...',
    
    // dashboard
    'home_bp_dashboard_menu' => 'Dashboard',
    'files_bp_dashboard_menu' => 'File Backups',
    'db_bp_dashboard_menu' => 'Database Backups',
    'recent_backups' => 'Recent Backups',
    'database_backup' => 'DB Backup',
    'total_backups' => 'Total Backups',
    'total_space_used' => 'Total Space Used',
    'last_backup_taken' => 'Last Backup Taken',
    'total_space_available' => 'Total Space Available',
    'first_backup_taken' => 'First Backup Taken',
    'na' => 'N/A',
    'no_backups_exist_yet' => 'No backups have been taken yet; you should create one ASAP. ',
    
    // general settings
    'relative_time' => 'Relative Time',
    'relative_time_instructions' => 'If enabled, dates in the CP will be displayed using human readable format instead of strict dates/times.',
    'dashboard_recent_total' => 'Dashboard Recent Backup Count',
    'dashboard_recent_total_instructions' => 'How many backups should be displayed on the Dashboard under the Recent Backups section?',
    
    // file settings
    'file_backup_alert_threshold' => 'File Backup Alert Frequency',
    'file_backup_alert_threshold_instructions' => 'How many days are backups supposed to be ran? If a bacukp hasn\'t been ran in as many days as set here a notification will be sent alerting the system administrators. Enter 0 to disable.',
    
    // notes
    'click_to_add_note' => 'Click to add note...',
    'note' => 'Note',
    
    'no_db_backups_exist_yet' => 'No database backups exist yet ',
    'no_file_backups_exist_yet' => 'No file backups exist yet ',
    'unlimited' => 'Unlimited',
    'taken' => 'Taken',
    'remote_status' => 'Remote Status',
    'md5_hash' => 'MD5 Hash',
    
    // breadcrumbs
    'settings_breadcrumb_general' => 'General',
    'settings_breadcrumb_db' => 'Database Backup',
    'settings_breadcrumb_files' => 'File Backup',
    'settings_breadcrumb_cron' => 'Cron Backup',
    'settings_breadcrumb_integrity_agent' => 'Integrity Agent',
    'settings_breadcrumb_api' => 'Rest AP',
    'settings_breadcrumb_license' => 'License Details',
    'settings_breadcrumb_storage' => 'Storage Locations',
    
    // integrity agent
    'verify_backup_stability' => 'Verify Backup Stability',
    'console_verify_backup_stability' => 'Verify Backup Stability (Console)',
    'integrity_agent_cron' => 'Integrity Agent Cron Command',
    'configure_integrity_agent_verification' => 'Configure Verification Options',
    'db_verification_db_name' => 'Verification Temp Database',
    'db_verification_db_name_instructions' => 'To ensure your database backups are stable Backup Pro can import your backups into a temporary database. Ensure the database user for the site has full MySQL privledges for the input one.',
    'configure_integrity_agent_backup_missed_schedule' => 'Configure Missed Backup Email',
    'default_backup_missed_schedule_notify_email_subject' => '{{ site_name }} - Backup State Notification',
    'default_backup_missed_schedule_notify_email_message' => '{{%FILTERS}}
    
Hello,<br /><br />
    
A {{ backup_type }} backup hasn\'t been completed on {{ site_name }} since {{ last_backup_date | m62.relative_time }}. A {{ backup_type }} backup is expected to run every {{ backup_frequency }} day(s) so something is clearly wrong; you should investigate ASAP<br /><br />
    
{{ site_name }}<br />
{{ site_url }}<br /><br />
    
Please don\'t respond to this email; all emails are automatically deleted.',
    'backup_missed_schedule_notify_emails' => 'Notification Emails',
    'backup_missed_schedule_notify_emails_instructions' => 'The email addresses for who should recieve a notification upon when the backup schedule isn\'t followed.',
    'backup_missed_schedule_notify_email_mailtype' => 'Email Format',
    'backup_missed_schedule_notify_email_mailtype_instructions' => 'Type of mail email message the Missed Backup Schedule email should be sent in. If you send HTML email you must send it as a complete web page. Make sure you don\'t have any relative links or relative image paths otherwise they will not work.',
    'backup_missed_schedule_notify_email_subject' => 'Missed Backup Schedule Email Subject',
    'backup_missed_schedule_notify_email_subject_instructions' => 'The subject you want the Missed Backup Schedule email to have. You can use global template tags but nothing fancy.',
    'backup_missed_schedule_notify_email_message' => 'Missed Backup Schedule Email Message',
    'backup_missed_schedule_notify_email_message_instructions' => 'The email message that gets sent when a backup finishes. You can use global template tags only.',
    'backup_missed_schedule_notify_email_interval' => 'Notification Interval',
    'backup_missed_schedule_notify_email_interval_instructions' => 'How much time, in hours, between when an email should be sent.',
    'total_verifications_per_execution' => 'Total Verifications Per Execution',
    'total_verifications_per_execution_instructions' => 'Depending on your server configuration you may want to limit how many backups Backup Pro verifies at any given time.',
    'check_backup_state_cp_login' => 'Check Backup State on CP Login',
    'check_backup_state_cp_login_instructions' => 'If you\'d like Backup Pro to verify the state of your backup schedule on Control Panel login check the box. Note that this only verify you have backups as determined by the Backup Alert Frequency.',
    
    // cron
    'configure_cron_email_attachment' => 'Configure Email Attachments',
    'configure_cron_notification' => 'Configure Email Notification',
    'default_cron_subject' => '{{ site_name }} - Backup Pro Cron Notification ({{ backup_type }})',
    'cron_notify_email_subject' => 'Cron Complete Notification Email Subject',
    'cron_notify_email_subject_instructions' => 'The subject you want the Cron Completion Notification email to have. You can use global template tags but nothing fancy.',
    'cron_notify_email_message' => 'Cron Complete Notification Email Message',
    'cron_notify_email_message_instructions' => 'The email message that gets sent when a backup finishes. You can use global template tags only.',
    'cron_notify_email_mailtype' => 'Email Format',
    'cron_notify_email_mailtype_instructions' => 'Type of mail email message the Backup Completion email should be sent in. If you send HTML email you must send it as a complete web page. Make sure you don\'t have any relative links or relative image paths otherwise they will not work.',
    'default_cron_message' => '{{%FILTERS}}
    
Hello,<br /><br />
    
Your backup has ran successfully.<br /><br />
    
Backup Type: {{ backup_details.backup_type }}<br />
Total Items: {{ backup_details.item_count }}<br />
Archive Filesize: {{ backup_details.compressed_size | m62.file_size }}<br />
Extracted Filsize: {{ backup_details.uncompressed_size | m62.file_size }}<br />
Memory Used: {{ backup_details.max_memory | m62.file_size }}<br />
Filename: {{ backup_details.file_name }}<br /><br />
    
{{ site_name }}<br />
{{ site_url }}<br /><br />
    
Please don\'t respond to this email; all emails are automatically deleted.',
    'backup_type' => 'Backup Type',
    'cron_commands' => 'Cron Commands',
    'test' => 'Test',
    
    'nav_view_backups' => 'View Backups',
    'total_items' => 'Total Items',
    'raw_file_size' => 'Raw File Size',
    'uncompressed_sql_size' => 'Uncompressed SQL Filesize',
    'total_tables' => 'Total Tables',
    'verified' => 'Verified',
    'time_taken' => 'Time Taken',
    'max_memory' => 'Maximum Memory',
    
    'added_date' => 'Added Date',
    'created_date' => 'Created Date',
    
    'restore_double_speak' => 'Unless you took a seperate backup and then restore that one if something goes wrong here I guess... Yeah, that could actually work... hmmm...',
    'allow_duplicates' => 'Allow Duplicate Backups',
    'allow_duplicates_instructions' => 'If an exact duplicate of a backup already exists (through md5 hash check) you\'re basically wasting space so Backup Pro will remove old duplicates. If you want to keep duplicates though just enable this.',
    'working_dir_not_setup' => 'Working Directory must be setup before backups can be taken',
    'setting' => 'Setting',
    'no_storage_locations_setup' => 'No Storage Locations have been setup yet.',
    'no_backup_file_location' => 'No File Backup Locations have been configured. ',
    'invalid_working_directory' => 'Invalid Working Directory.',
    'database_restore_fail' => 'Database Restore Failed...',
    'cron_query_key' => 'Cron Query Key',
    'cron_query_key_instructions' => 'For security, Backup Pro allows customization of the URL used for Cron commands so malicious users can\'t force Cron executions. Any calls to Cron URls MUST have the value for the `backup_pro` parameter.',
    'backup' => 'Backup',
    'manage_backups' => 'Manage Backups',
    'pre_backup_setting_issue_blurb' => 'You\'re going to need to fix the below configuration errors before you can start taking backups',
    
    'check_working_dir' => 'Check Working Directory',
    'enter_license' => 'Enter License',
    'purchase_a_license' => 'Purchase a License',
    'setup_storage_location' => 'Setup Storage Location',
    
    'regex_file_exclude' => 'Regular Expressions on Exclude?',
    'regex_file_exclude_instructions' => 'If you like, Backup Pro allows for regular expression use for defining what files to exclude. Be careful though! This can increase your file backup time depending on your setup.',
    'uh_oh_there_are_issues' => 'Uh-oh... There are some issues...',
    'fix_console_errors_instructions' => 'You\'ll have to log into your site and configure Backup Pro to remove the above errors before you can continue. ',
    'extras' => 'Extras',
    'advanced_options' => 'Advanced Options',
    'path_to_prefix_example' => '/path/to/prefix',
    'action_can_not_be_undone' => 'Be careful, this can\'t be undone...',
    
    
    'storage_location_name' => 'Storage Location Name',
    'add_storage_location' => 'Add Storage Location',
    'edit_storage_location' => 'Edit Storage Location',
    'new_storage_location' => 'New Storage Location',
    'remove_storage_location' => 'Remove Storage Location',
    'remove_remote_files' => 'Remove Stored Backups?',
    'remove_remote_files_instructions' => 'Should any backups stored on the remote location be removed along when the Storage Location is removed from the system? This is ill advised.',
    'existing_storage_locations' => 'Existing Storage Locations',
    'no_storage_locations_created_yet' => 'No Storage Locations Created Yet',
    'storage_location_name_instructions' => 'Just a designation so you know what you\'re looking at when you\'re looking at it.',
    'storage_location_file_use' => 'File Backup Use',
    'storage_location_file_use_instructions' => 'Should file backups be stored here?',
    'storage_location_db_use' => 'Database Backup Use',
    'storage_location_db_use_instructions' => 'Should database backups be stored here?',
    'storage_location_include_prune' => 'Automated Archive Pruning?',
    'storage_location_include_prune_instructions' => 'Should backups stored here be removed when the system limits are reached?',
    'storage_location_added' => 'Storage Location Added!',
    'storage_location_updated' => 'Storage Location Updated!',
    'invalid_storage_id' => 'Invalid Storage ID',
    'no_storage_locations_setup' => 'No Storage Locations have been setup yet!',
    'min_storage_location_needs' => 'You must have at least 1 Storage Location!',
    'storage_location_removed' => 'Storage Location Removed',
    'storage_location_status' => 'Active',
    'storage_location_status_instructions' => 'Should this Storage Location be used?',
    'delete_storage_confirm' => 'Are you sure you want to remove the below Storage Location?',    
    
    '' => ''
)
;