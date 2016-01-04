<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Notifications.php
 */

namespace PM\Model;

use Zend\Mail;
use Application\Model\AbstractModel;


 /**
 * PM - Notifications Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Notifications.php
 */
class Notifications extends Zend\Mail 
{	
	public function __construct(){
		
		parent::__construct();
	}
	
	/**
	 * Sends the email upon new account creation
	 * @param array $user_data
	 * @return bool
	 */
	public function sendUserAdd($user_data, $changed)
	{
		$subject = 'Your new MojiTrac account';
		$msg_html = '';
		$msg_html .= $this->prepareUserAddEmailBody($user_data, $changed);
		$this->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
		
		return $this->sendMail($subject, $msg_html, $msg_txt = FALSE);		
	}
	
	/**
	 * Sends the email when a user is added to a project team
	 * @param int $user_id
	 * @param array $project_data
	 * @return bool
	 */
	public function addToProjectTeam($user_id, array $project_data)
	{
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		if($user->checkPreference($user_id, 'noti_add_proj_team', '1') == '0')
		{
			return true;
		}

		$user_data = $user->getUserById($user_id);

		$subject = 'Added to project team: '.$project_data['name'];
		$msg_html = 'You have been added to the project team for: '.$this->makeLink($project_data['name'], $project_data['id'], 'project').'<br /><br />';
		$msg_html .= $this->prepareProjectEmailBody($project_data);
		$this->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
		
		return $this->sendMail($subject, $msg_html, $msg_txt = FALSE);		
	}
	
	/**
	 * Sends the email when a user is removed from a project team
	 * @param int $user_id
	 * @param array $project_data
	 * @return bool
	 */
	public function removeFromProjectTeam($user_id, array $project_data)
	{
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		if($user->checkPreference($user_id, 'noti_remove_proj_team', '1') == '0')
		{
			return true;
		}
		$user_data = $user->getUserById($user_id);

		$subject = 'Removed from project team: '.$project_data['name'];
		$msg_html = 'You have been removed from the project team for: '.$project_data['name'].'<br /><br />';
		$msg_html .= $this->prepareProjectEmailBody($project_data);
		$this->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
		
		return $this->sendMail($subject, $msg_html, $msg_txt = FALSE);		
	}	
	
	/**
	 * Sends the task assignment email
	 * @param array $task_data
	 * @return bool
	 */
	public function sendTaskAssignment(array $task_data)
	{
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		if($user->checkPreference($task_data['assigned_to'], 'noti_assigned_task', '1') == '0')
		{
			return true;
		}
				
		$user_data = $user->getUserById($task_data['assigned_to']);
		if(!$user_data)
		{
			return FALSE;
		}
		
		$subject = 'New Task: '.$task_data['name'];
		$msg_html = 'A task has been assigned to you: '.$this->makeLink($task_data['name'], $task_data['id'], 'task').'<br /><br />';
		$msg_html .= $this->prepareTaskEmailBody($task_data);
		
		$this->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
		
		return $this->sendMail($subject, $msg_html, $msg_txt = FALSE);
	}
	
	/**
	 * Sends the task status change email
	 * @param array $task_data
	 * @return bool
	 */
	public function sendTaskStatusChange(array $task_data)
	{
		$subject = 'Task status changed: '.$task_data['name'];
		$msg_html = 'The status of the following task has changed:<br /><br />';
		$msg_html .= $this->prepareTaskEmailBody($task_data);
		
		//get everyone on the team
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$team = $project->getProjectTeamMembers($task_data['project_id']);
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		foreach($team AS $member)
		{
			if($user->checkPreference($member['user_id'], 'noti_status_task', '1') == '0')
			{
				continue;
			}
			
			$this->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
		}
		
		$this->addHeader("X-Priority", $task_data['priority']);
		$this->sendMail($subject, $msg_html, $msg_txt = FALSE);
	}
	
	/**
	 * Sends the task priority change email
	 * @param array $task_data
	 * @return bool
	 */	
	public function sendTaskPriorityChange(array $task_data)
	{
		$subject = 'Task priority changed: '.$task_data['name'];
		$msg_html = 'The priority of the following task has changed:<br /><br />';
		$msg_html .= $this->prepareTaskEmailBody($task_data);
		
		//get everyone on the team
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$team = $project->getProjectTeamMembers($task_data['project_id']);
		foreach($team AS $member)
		{
			if($user->checkPreference($member['user_id'], 'noti_priority_task', '1') == '0')
			{
				continue;
			}			
			$this->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
		}
		$this->sendMail($subject, $msg_html, $msg_txt = FALSE);		
	}
	
	/**
	 * Sends the task date change email
	 * @param array $task_data
	 */
	public function sendTaskEndDateChange(array $task_data)
	{
		$subject = 'Task due date changed: '.$task_data['name'];
		$msg_html = 'The due date of the following task has changed:<br /><br />';
		$msg_html .= $this->prepareTaskEmailBody($task_data);
		
		//get everyone on the team
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$team = $project->getProjectTeamMembers($task_data['project_id']);
		foreach($team AS $member)
		{
			if($user->checkPreference($member['user_id'], 'noti_assigned_task', '1') == '0')
			{
				continue;
			}				
			$this->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
		}
		$this->sendMail($subject, $msg_html, $msg_txt = FALSE);			
	}
	
	/**
	 * Sends Daily Task Reminder Email
	 * @param array $user
	 * @param array $tasks
	 */
	public function sendDailyTaskReminder(array $user, array $tasks)
	{
		$subject = 'Daily Task Reminder';
		$head = 'Hello '.$user['first_name'].', <br /><br />This is an automatic email to remind you of tasks that are assigned to you but not completed yet. Please login into the project management system and review your assigned tasks.<br />';
		$msg_html = $this->prepareDailyTaskReminderEmailBody($tasks);
		if(!$msg_html)
		{
			return FALSE;
		}
		
		$this->addTo($user['email'], $user['first_name'].' '.$user['last_name']);
		$this->sendMail($subject, $head.$msg_html, $msg_txt = FALSE);	
	}
	
	/**
	 * Sends the file upload to the project team
	 * @param array $file_data
	 * @return bool
	 */	
	public function sendFileAdd(array $file_data, array $file_info)
	{	
		if(isset($file_data['project']))
		{		
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$file_data['project_data'] = $project->getProjectById($file_data['project']);
		}
		
		if(is_array($file_data['project_data']))
		{
			if(isset($file_data['task']))
			{		
				$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
				$file_data['task_data'] = $task->getTaskById($file_data['task']);
			}		
			
			$subject = 'File Uploaded: '.$file_data['name'];
			$msg_html = 'A new file was uploaded: '.$file_data['name'].'<br /><br />';			
			$msg_html .= $this->prepareFileAddEmailBody($file_data, $file_info);
					
			$team = $project->getProjectTeamMembers($file_data['project_data']['id']);
			$user = new PM_Model_Users(new PM_Model_DbTable_Users);
			foreach($team AS $member)
			{
				if($user->checkPreference($member['user_id'], 'noti_file_uploaded', '1') == '0')
				{
					continue;
				}				
				$this->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
			}
			$this->sendMail($subject, $msg_html, $msg_txt = FALSE);				
		}	
	}
	
	/**
	 * Sends the file revision notification to the project team
	 * @param array $file_data
	 * @return bool
	 */	
	public function sendRevisionAdd($id)
	{	
		$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$rev_data = $file->getRevision($id);
		if(!$rev_data || !is_array($rev_data))
		{
			return FALSE;
		}
		
		$rev_data['file_data'] = $file->getFileById($rev_data['file_id']);
		if(isset($rev_data['file_data']['project_id']))
		{		
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$rev_data['file_data']['project_data'] = $project->getProjectById($rev_data['file_data']['project_id']);
		}
		
		if(is_array($rev_data['file_data']['project_data']))
		{
			if(isset($rev_data['file_data']['task_id']))
			{		
				$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
				$rev_data['file_data']['task_data'] = $task->getTaskById($rev_data['file_data']['task_id']);
			}
			
			$subject = 'File Revision Uploaded: '.$rev_data['file_data']['name'];
			$msg_html = 'A new file revision was uploaded: '.$rev_data['file_data']['name'].'<br /><br />';			
			$msg_html .= $this->prepareRevisionAddEmailBody($rev_data);
					
			$team = $project->getProjectTeamMembers($rev_data['file_data']['project_data']['id']);
			$user = new PM_Model_Users(new PM_Model_DbTable_Users);
			foreach($team AS $member)
			{
				if($user->checkPreference($member['user_id'], 'noti_file_revision_uploaded', '1') == '0')
				{
					continue;
				}					
				$this->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
			}
			$this->sendMail($subject, $msg_html, $msg_txt = FALSE);				
		}
	}	
	
	public function prepareRevisionAddEmailBody(array $rev_data)
	{
		$msg_html = 'Name: '.$rev_data['file_data']['name'].'<br />';
		if(isset($rev_data['file_data']['project_data']) && is_array($rev_data['file_data']['project_data']))
		{
			$msg_html .= 'Project: '.$this->makeLink($rev_data['file_data']['project_data']['name'], $rev_data['file_data']['project_data']['id'], 'project').'<br />';
		}

		if(isset($rev_data['file_data']['task_data']) && is_array($rev_data['file_data']['task_data']))
		{
			$msg_html .= 'Task: '.$this->makeLink($rev_data['file_data']['task_data']['name'], $rev_data['file_data']['task_data']['id'], 'task').'<br />';
		}
		
		$msg_html .= 'Status: '.PM_Model_Options_Files::translateStatusId($rev_data['status']).'<br />';
		
		$msg_html .= 'File Type: '.$rev_data['mime_type'].'<br />';
		$msg_html .= 'Uploaded By: '.$rev_data['uploader_first_name'].' '.$rev_data['uploader_last_name'].'<br /><br />';
		
		if($file_data['description'])
		{
			$msg_html .= 'Description<br />'.nl2br($rev_data['description']).'<br /><br />';
		}
		
		$msg_html .= $this->makeLink('Download', $rev_data['id'], 'download');
		$msg_html .= " || ".$this->makeLink('View File', $rev_data['file_id'], 'file');
		return $msg_html;		
	}
		
	public function prepareFileAddEmailBody(array $file_data, array $file_info)
	{
		$msg_html = 'Name: '.$file_data['name'].'<br />';
		if(isset($file_data['project_data']) && is_array($file_data['project_data']))
		{
			$msg_html .= 'Project: '.$this->makeLink($file_data['project_data']['name'], $file_data['project_data']['id'], 'project').'<br />';
		}

		if(isset($file_data['task_data']) && is_array($file_data['task_data']))
		{
			$msg_html .= 'Task: '.$this->makeLink($file_data['task_data']['name'], $file_data['task_data']['id'], 'task').'<br />';
		}
		
		$msg_html .= 'Status: '.PM_Model_Options_Files::translateStatusId($file_data['status']).'<br />';
		$msg_html .= 'File Type: '.$file_info['type'].'<br />';
		
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$user_data = $user->getUserById($file_data['uploaded_by']);
		$msg_html .= 'Uploaded By: '.$user_data['first_name'].' '.$user_data['last_name'].'<br />';
		
		if($file_data['description'])
		{
			$msg_html .= 'Description<br />'.nl2br($file_data['description']).'<br />';
		}
		
		$msg_html .= $this->makeLink('Download', $file_info['revision_id'], 'download');
		$msg_html .= " || ".$this->makeLink('View File', $file_data['file_id'], 'file');
		return $msg_html;		
	}
	
	public function prepareDailyTaskReminderEmailBody(array $tasks)
	{
		$lang['overdue'] = '<br />The following tasks are OVERDUE:<br />';
		$lang['today'] = '<br />The following tasks are DUE TODAY:<br />';
		$lang['tomorrow'] = '<br />The following tasks are DUE TOMORROW:<br />';
		$lang['within_week'] = '<br />The following tasks are WITHIN A WEEK:<br />';
		$lang['upcoming'] = '<br />The following tasks are in the coming week(s):<br />';
		foreach($lang AS $type => $str)
		{
			if(array_key_exists($type, $tasks) && is_array($tasks[$type]))
			{
				if(count($tasks[$type]) >= 1)
				{
					foreach($tasks[$type] AS $task)
					{
						$lang[$type] .= "<a href='".$this->web_url."/pm/tasks/view/id/".$task['id']."'>".$task['name']."</a> (".LambLib_Controller_Action_Helper_Utilities::relative_datetime($task['end_date']).")<br>";
					}
					continue;
				}
			}
			unset($lang[$type]);
		}
		
		return (is_array($lang) ? implode('', $lang) : FALSE);
	}
	
	public function prepareUserAddEmailBody(array $user_data, $changed = FALSE)
	{
		$msg_html = 'Hello '.$user_data['first_name'];
		$msg_html .= '<br /><br />';
		
		if($changed)
		{
			$msg_html .= 'Your MojiTrac account was recently set update to use the below email address.';
		}
		else
		{
			$msg_html .= 'A new account was created for you on MojiTrac:';
		}
		$msg_html .= '<br />If you don\'t yet know your password please visit the <a href="'.$this->web_url.'/forgot-password">Forgot Password</a> page to set one up.';
		$msg_html .= '<br /><br />';
		$msg_html .= 'Email: '.$user_data['email'].'<br />';
		$msg_html .= '<br /><br />';
		$msg_html .= 'You can log into your MojiTrac account by going to:<br />';
		$msg_html .= '<a href="'.$this->web_url.'/login">'.$this->web_url.'/login</a>';
		return $msg_html;
	}
	
	public function prepareProjectEmailBody(array $project_data)
	{
		$msg_html = 'Project: '.$this->makeLink($project_data['name'], $project_data['id'], 'project').'<br />';
		$msg_html .= 'Company: '.$project_data['company_name'].'<br />';
		//$msg_html .= 'Expected Duration: '.$project_data['duration'].' hours<br />';
		//$msg_html .= 'Due: '.Zend_View_Helper_RelativeDate::RelativeDate($project_data['end_date']).'<br />';
		$msg_html .= 'Type: '.PM_Model_Options_Projects::translateTypeId($project_data['type']).'<br />';
		$msg_html .= 'Priority: '.PM_Model_Options_Projects::translatePriorityId($project_data['priority']).'<br />';
		$msg_html .= 'Status: '.PM_Model_Options_Projects::translateStatusId($project_data['status']).'<br /><br />';
		
		if($project_data['description'])
		{
			$msg_html .= 'Description<br />'.nl2br($project_data['description']).'<br />';
		}
		return $msg_html;		
	}
	
	/**
	 * Prepares the task info portion of the email
	 * @param array $task_data
	 * @return string
	 */
	public function prepareTaskEmailBody(array $task_data)
	{
		$msg_html = 'Task: '.$this->makeLink($task_data['name'], $task_data['id'], 'task').'<br />';
		if($task_data['duration'] == '')
		{
			$task_data['duration'] = 0;
		}
		
		$msg_html .= 'Expected Duration: '.$task_data['duration'].' hours<br />';
		if($task_data['start_date'] && $task_data['start_date'] != '0000-00-00 00:00:00')
		{
			$msg_html .= 'Start: '.$this->utils->formatDate($task_data['start_date'], 'F j, Y g:i A').'<br />';
		}
			
		if($task_data['end_date'] && $task_data['end_date'] != '0000-00-00 00:00:00')
		{
			$msg_html .= 'Due: '.$this->utils->formatDate($task_data['end_date'], 'F j, Y g:i A').'<br />';
		}
		
		if(!isset($task_data['company_name']) || $task_data['company_name'] == '' || !isset($task_data['project_name']) || $task_data['project_name'] == '')
		{
			$proj = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$proj_data = $proj->getProjectById($task_data['project_id']);
			$task_data['project_name'] = $proj_data['name'];
		}
		
		if(!isset($task_data['company_name']) || $task_data['company_name'] == '')
		{
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$company_data = $company->getCompanyById($proj_data['company_id']);
			$task_data['company_name'] = $company_data['name'];
			$task_data['company_id'] = $company_data['id'];
		}		
		
		$msg_html .= 'Company: '.$this->makeLink($task_data['company_name'], $task_data['company_id'], 'company').'<br />';
		$msg_html .= 'Project: '.$this->makeLink($task_data['project_name'], $task_data['project_id'], 'project').'<br />';
		$msg_html .= 'Type: '.PM_Model_Options_Projects::translateTypeId($task_data['type']).'<br />';
		$msg_html .= 'Priority: '.PM_Model_Options_Projects::translatePriorityId($task_data['priority']).'<br />';
		$msg_html .= 'Progress: '.$task_data['progress'].'%<br />';
		$msg_html .= 'Status: '.PM_Model_Options_Projects::translateStatusId($task_data['status']).'<br /><br />';
		
		$msg_html .= 'Description<br />'.$this->makeHtml($task_data['description']).'<br /><br />';
		if(array_key_exists('assign_comment', $task_data) && $task_data['assign_comment'] != '')
		{
			$msg_html .= 'Assignment Comment<br />'.$this->makeHtml($task_data['assign_comment']).'<br />';
		}
		return $msg_html;
	}
	
	/**
	 * Sends the email
	 * @param string $to_name
	 * @param string $to_email
	 * @param string $subject
	 * @param string $msg_html
	 * @param string $msg_txt
	 * @return bool
	 */
	private function sendMail($subject, $msg_html, $msg_txt = FALSE)
	{
		if($msg_txt)
		{
			$this->setBodyText($msg_txt);
		}
		
		$msg_html = $msg_html.$this->_footer();
		
		$this->setBodyHtml($msg_html);
		$this->setSubject($subject);
		return $this->send($this->transport);		
	}
	
	private function _footer()
	{
		$link = '<a href="/pm/settings/prefs">Moji</a>';
		return '<br /><br />Sent By: MojiTrac<br />Want to stop these emails? You can manage your notifcations within '.$link.' too!';
	}
}