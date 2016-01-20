<?php
/**
 * Application - Language file
 * 
 * Contains all the variables for localization
 *
 * @package		Localization
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/language/en_US.php
 */
return array(
    // login
    'login_instructions' => 'To log in please enter your credentials below.',
    'login_welcome_message' => 'Welcome!',
    'invalid_credials_try_again' => 'Invalid Credentials! Please Try Again',
    'login_successful' => 'Login Successful!',
    
    //global header
    'toggle_navigation' => 'Toggle navigation',
    'home' => 'Home',
    'about' => 'About',
    'admin' => 'Admin',
    'logout' => 'Logout',
    'login' => 'Login',
    'contact' => 'Contact',
    
    //user account
    'account' => 'Account',
    'email_settings' => 'Email Settings',
    'change_password' => 'Change Password',
    'preferences' => 'Preferences',
    'account_created' => 'Account Created',
    
    // logout
    'youve_been_logged_out' => 'You\'ve been logged out',
    
    // global
    'submit' => 'Submit',
    'email' => 'Email',
    'password' => 'Password',
    'sign_in' => 'Sign in',
    'register' => 'Register',
    'login' => 'Login',
    'settings' => 'Settings',
    
    // forgot password
    'forgot_password' => 'Forgot Password',
    'forgot_password_email_subject' => 'Forgot Password',
    'check_your_emmail' => 'Please check your email',
    'forgot_password_email' => 'Hello, <br /><br />
		
			To reset your password for your account, click the link below:<br /><br />

			%2$s<br /><br />
			
			Copy and paste the URL in a new browser window if you can\'t click on it. Please keep in mind that the link will only work for 24 hours; after that it will be inactive. 
			If you didn\'t request to reset your password you don\'t need to take any further action and can safely disregard this email.<br /><br />
			
			:)<br /><br />
			
			Please don\'t respond to this email; all emails are automatically deleted.			
	',
    'forgot_password_instructions' => 'Enter your email to reset your password.',
    'back_to_login' => 'Back to Login',
    
    // change password
    'rest_password' => 'Reset Password',
    'old_password' => 'Old Password',
    'new_password' => 'New Password',
    'confirm_password' => 'Confirm Password',
    'password_has_reset' => 'Your password hass been reset!',
    
    // js validation messages
    'js_email_validation_message' => 'Please enter an email address',
    'js_password_validation_message' => 'Please enter a password',
    'required' => 'Required',
    
    // reset password
    'reset_password_instructions' => 'Create your new password here',
    'user_registration_email_subject' => 'New Account Created!',
    
    // verify email
    'verify_email' => 'Verify Email',
    'verify_send_conf_email' => 'Send Confirmation Email',
    'verify_email_required_html' => 'You have to confirm your email address (<a href="%2$s">%1$s</a>) to access all the features.',
    'verify_email_sent' => 'An email has been sent to %1s to verify your account; you\'ll need to follow the instructions to continue. ',
    'verify_email_instructions' => 'Once you click the below button an email will be sent to your registered email address (%1s) with instructions on how to proceed.',
    'verify_email_email_html_body' => 'Hello, <br /><br />
		
			To reset your password for your account, click the link below:<br /><br />

			%2$s<br /><br />
			
			Copy and paste the URL in a new browser window if you can\'t click on it. Please keep in mind that the link will only work for 24 hours; after that it will be inactive. 
			If you didn\'t request to reset your password you don\'t need to take any further action and can safely disregard this email.<br /><br />
			
			:)<br /><br />
			
    ',
    'verify_email_email_subject' => 'Verify Email',
    'verify_email_successful' => 'The email address %1s has been verified',
    
    //email settings
    'current_email' => 'Current Email',
    'change_email' => 'Change Email',
    'new_email' => 'New Email',
    'email_has_changed' => 'Your email address has been changed',
    
    //preferences
    'timezone' => 'Timezone',
    'locale' => 'Locale',
    'enable_rel_time' => 'Enable Relative Time',
    'update_preferences' => 'Update Preferences',
    'preferences_updated' => 'Preferences Updated',
    
    //general settings
    'general_settings_header' => 'General Settings',
    'site_name' => 'Site Name',
    'site_url' => 'Site URL',
    'settings_updated' => 'Settings Updated',
    
    //mail settings
    'mail_settings_header' => 'Mail Settings',
    'mail_reply_to_email' => 'Reply to Email',
    'mail_reply_to_name' => 'Reply to Name',
    'mail_sender_email' => 'Sender Email',
    'mail_sender_name' => 'Sender Name',
    'mail_from_email' => 'From Email',
    'mail_from_name' => 'From Name',
    
    //user manage
    'manage_users' => 'Manage Users',
    'add_user' => 'Add User',
    'users' => 'Users',
    'roles' => 'Roles',
    'view_users' => 'View Users',
    'edit_user' => 'Edit User',
    'view_user' => 'View User',
    'delete_user' => 'Delete User',
    'user_cant_remove_self' => 'You can\'t remove yourself',
    'remove_user_question' => 'Are you sure you want to remove <a href="%2$s">%1$s</a>? This can not be undone...',
    'user_removed' => 'User Removed',
    'send_verification_email' => 'Send Verification Email',
    'user_roles' => 'User Roles',
    'auto_verify' => 'Auto Verify Email',
    'user_added' => 'User Added',
    'user_updated' => 'User Updated',
    
    //user role manage
    'view_user_roles' => 'View User Roles',
    'add_user_role' => 'Add User Role',
    
    //ip locker
	'ip_locker_disabled' => 'Ip Blocking Disabled!',
    'enable_ip_locker_question' => 'Are you sure you want to enable the Ip Locker?',
    'disable_ip_locker_question' => 'Are you sure you want to disable the Ip Locker?',
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
    'add_ip_address' => 'Add IP Address',
    'view_ips' => 'View IP Addresses',
    'ip_address' => 'IP Address',
    'description' => 'Description',
    'enable_ip_locker' => 'Enable IP Locker',
    'disable_ip_locker' => 'Disable IP Locker',
    'edit_ip_address' => 'Edit IP Address',
    'view_ip_address' => 'View IP Address',
    
);