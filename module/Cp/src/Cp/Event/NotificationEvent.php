<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/NotificationEvent.php
 */

namespace PM\Event;

use Base\Event\BaseEvent;
use Application\Model\Mail;
use PM\Model\Users;
use PM\Model\Projects;
use PM\Model\Tasks;

 /**
 * PM - Notification Events
 *
 * @package 	Events
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/NotificationEvent.php
 */
class NotificationEvent extends BaseEvent
{
    /**
     * User Identity
     * @var int
     */
    public $identity = false;
    
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'user.add.post' => 'sendUserAdd',
    	'task.update.pre' => 'sendTaskUpdate',
    	'task.assign.pre' => 'sendTaskAssign',
    	'project.removeteammember.pre' => 'sendRemoveFromProjectTeam',
    	'project.addteam.post' => 'sendAddProjectTeam',
    	'file.add.post' => 'sendFileAdd',
    	'file.revision.add.post' => 'sendFileRevisionAdd',
    );
    
    /**
     * The Notification Event
     * @param Mail $mail
     * @param Users $users
     * @param Projects $project
     * @param Tasks $task
     * @param string $identity
     */
    public function __construct( Mail $mail, Users $users, Projects $project, Tasks $task, $identity = null)
    {
        $this->mail = $mail;
        $this->identity = $identity;
        $this->user = $users;
        $this->project = $project;
        $this->task = $task;
        
        $this->email_view_path = $this->mail->getModulePath(__DIR__).'/view/emails';
    }

    /**
     * Registers the Event with ZF and our Application Model
     * @param \Zend\EventManager\SharedEventManager $ev
     */
    public function register( \Zend\EventManager\SharedEventManager $ev)
    {
    	foreach($this->hooks AS $key => $value)
    	{
    		$ev->attach('Base\Model\BaseModel', $key, array($this, $value));
    	}
    }
    
    /**
     * Sends the user registration notification
     * @param \Zend\EventManager\Event $event
     */
    public function sendUserAdd(\Zend\EventManager\Event $event)
    {
    	$data = $event->getParam('data');
    	$user_id = $event->getParam('user_id');
    	$this->mail->addTo($data['email'], $data['first_name'].' '.$data['last_name']);
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('user-registration', array('user_data' => $data, 'user_id' => $user_id));
    	$this->mail->setTranslationDomain('pm');
    	$this->mail->setSubject('user_registration_email_subject');
    	$this->mail->send();    	
    }
    

    /**
     * Sends the Task Status Change email notifications
     * @param int $task_id
     * @param array $new_data
     * @param array $old_data
     */
    public function sendTaskStatusChange($task_id, array $new_data, array $old_data)
    {
    	$team = $this->project->getProjectTeamMembers($new_data['project_id']);
    	$project_data = $this->project->getProjectById($new_data['project_id']);
    	$sending = FALSE;
    	foreach($team AS $member)
    	{
    		if($this->user->checkPreference($member['user_id'], 'noti_status_task', '1') == '0')
    		{
    			continue;
    		}
    		
    		$sending = TRUE;
    		$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
    	}   

    	if( !$sending )
    	{
    		return; //no emails were added to send to so bounce out
    	}
    	
    	$view_data = array(
    		'task_data' => $new_data, 
    		'task_id' => $task_id, 
    		'project_data' => $project_data
    	);
    	
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('task-status-change', $view_data);
    	$this->mail->setSubject($this->mail->translator->translate('email_subject_task_status_change', 'pm').': '.$new_data['name']);
    	$this->mail->send();
    }

    /**
     * Sends the Task Priority Change email notifications
     * @param int $task_id
     * @param array $new_data
     * @param array $old_data
     */
    public function sendTaskPriorityChange($task_id, array $new_data, array $old_data)
    {
    	$team = $this->project->getProjectTeamMembers($new_data['project_id']);
    	$project_data = $this->project->getProjectById($new_data['project_id']);
    	$sending = FALSE;
    	foreach($team AS $member)
    	{
    		if($this->user->checkPreference($member['user_id'], 'noti_priority_task', '1') == '0')
    		{
    			continue;
    		}
    
    		$sending = TRUE;
    		$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
    	}
    
    	if( !$sending )
    	{
    		return; //no emails were added to send to so bounce out
    	}
    	 
    	$view_data = array(
    		'task_data' => $new_data,
    		'task_id' => $task_id,
    		'project_data' => $project_data
    	);
    	 
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('task-priority-change', $view_data);
    	$this->mail->setSubject($this->mail->translator->translate('email_subject_task_priority_change', 'pm').': '.$new_data['name']);
    	$this->mail->send();
    } 

    /**
     * Sends the Task End Date Update email notifications
     * @param int $task_id
     * @param array $new_data
     * @param array $old_data
     */
    public function sendTaskEndDateChange($task_id, array $new_data, array $old_data)
    {
    	$team = $this->project->getProjectTeamMembers($new_data['project_id']);
    	$project_data = $this->project->getProjectById($new_data['project_id']);
    	$sending = FALSE;
    	foreach($team AS $member)
    	{
    		if($this->user->checkPreference($member['user_id'], 'noti_priority_task', '1') == '0')
    		{
    			continue;
    		}
    
    		$sending = TRUE;
    		$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
    	}
    
    	if( !$sending )
    	{
    		return; //no emails were added to send to so bounce out
    	}
    
    	$view_data = array(
    		'task_data' => $new_data,
    		'task_id' => $task_id,
    		'project_data' => $project_data
    	);
    
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('task-priority-change', $view_data);
    	$this->mail->setSubject($this->mail->translator->translate('email_subject_task_priority_change', 'pm').': '.$new_data['name']);
    	$this->mail->send($mail->transport);
    }    
    
    /**
     * Sends the emails for when a task is modified
     * @param \Zend\EventManager\Event $event
     */
    public function sendTaskUpdate(\Zend\EventManager\Event $event)
    {
    	$task_id = $event->getParam('task_id');
    	$new_data = $event->getParam('data');
    	$task_data = $this->task->getTaskById($task_id);
    	if($new_data['status'] != $task_data['status'] && ($new_data['priority'] == $task_data['priority']))
    	{
    		$this->sendTaskStatusChange($task_id, $new_data, $task_data);
    	}
    	else if($new_data['priority'] != $task_data['priority'])
    	{
    		$this->sendTaskPriorityChange($task_id, $new_data, $task_data);
    	}
    	
    	if($new_data['end_date'] != $task_data['end_date'])
    	{
    		//$noti = new PM_Model_Notifications;
    		//$noti->sendTaskEndDateChange($task_data);
    		//echo "sendTaskEndDateChange";
    		//exit;
    	}    	
    }
    
    /**
     * Sends the email when a task is assigned to someone
     * @param \Zend\EventManager\Event $event
     */
    public function sendTaskAssign(\Zend\EventManager\Event $event)
    {
    	$task_id = $event->getParam('task_id');
    	$assigned_to = $event->getParam('assigned_to');
    	if($assigned_to == '0' || $assigned_to == $this->identity)
    	{
    		return; //assigned to nobody (or so current user) so bounce
    	}
    	
    	if($this->user->checkPreference($assigned_to, 'noti_assigned_task', '1') != '0')
    	{
    		$user_data = $this->user->getUserById($assigned_to);
    		$task_data = $this->task->getTaskById($task_id);
    		$project_data = $this->project->getProjectById($task_data['project_id']);
    		$this->mail->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
    		$this->mail->setViewDir($this->email_view_path);
    		
    		$view_data = array(
    			'assigned_to' => $assigned_to,
    			'task_id' => $task_id,
    			'user_data' => $user_data,
    			'task_data' => $task_data,
    			'project_data' => $project_data
    		);
    		    		
    		$this->mail->setEmailView('task-assigned', $view_data);
    		$this->mail->setSubject($this->mail->translator->translate('email_subject_task_assigned', 'pm').': '.$task_data['name']);
    		$this->mail->send($mail->transport);    		
    	}
    }
    
    /**
     * Sends the email for when a user is removed from a project team
     * @param \Zend\EventManager\Event $event
     */
    public function sendRemoveFromProjectTeam(\Zend\EventManager\Event $event)
    {
    	$user_id = $event->getParam('user_id');
    	$project_id = $event->getParam('project_id');
        if($this->user->checkPreference($user_id, 'noti_remove_proj_team', '1') != '0')
    	{
    		$user_data = $this->user->getUserById($user_id);
    		$project_data = $this->project->getProjectById($project_id);
    		$this->mail->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
    		$this->mail->setViewDir($this->email_view_path);
    		
    		$view_data = array(
    			'user_id' => $user_id,
    			'project_id' => $project_id,
    			'user_data' => $user_data,
    			'project_data' => $project_data
    		);
    		    		
    		$this->mail->setEmailView('project-team-remove', $view_data);
    		$this->mail->setSubject($this->mail->translator->translate('email_subject_project_team_remove', 'pm').': '.$project_data['name']);
    		$this->mail->send($mail->transport);
    	}
    }
    
    /**
     * Sends the email for a user being added to a project team
     * @param \Zend\EventManager\Event $event
     */
    public function sendAddProjectTeam(\Zend\EventManager\Event $event)
    {
    	$user_id = $event->getParam('id');
    	$project_id = $event->getParam('project');
    	if($this->user->checkPreference($user_id, 'noti_add_proj_team', '1') != '0')
    	{
    		$user_data = $this->user->getUserById($user_id);
    		$project_data = $this->project->getProjectById($project_id);
    		$this->mail->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
    		$this->mail->setViewDir($this->email_view_path);
    	
    		$view_data = array(
    			'user_id' => $user_id,
    			'project_id' => $project_id,
    			'user_data' => $user_data,
    			'project_data' => $project_data
    		);
    	
    		$this->mail->setEmailView('project-team-add', $view_data);
    		$this->mail->setSubject($this->mail->translator->translate('email_subject_project_team_add', 'pm').': '.$project_data['name']);
    		$this->mail->send($mail->transport);
    	}    	
    }
    
    /**
     * Sends the email when a file is uploaded
     * @param \Zend\EventManager\Event $event
     */
    public function sendFileAdd(\Zend\EventManager\Event $event)
    {
		$file_id = $event->getParam('file_id');
		$file = $event->getTarget();
		$file_data = $file->getFileById($file_id);
		$revision_data = $file->revision->getFileRevisions($file_id);
		if(isset($revision_data['0']))
		{
			$revision_data = $revision_data['0']; //we only want that first revision for this
		}
		else
		{
			//no revision so something's off; bounce
			return;
		}
		
		$task_data = $project_data = false;
		if($file_data['project_id'] != '0')
		{
			$project_data = $this->project->getProjectById($file_data['project_id']);
			if(isset($file_data['task']))
			{
				$task_data = $this->task->getTaskById($file_data['task']);
			}

			//ok; now let's grab the project team members
			$team = $this->project->getProjectTeamMembers($file_data['project_id']);
			$sending = FALSE;
			foreach($team AS $member)
			{
				if($this->user->checkPreference($member['user_id'], 'noti_file_uploaded', '1') == '0')
				{
					continue;
				}	

				$sending = TRUE;
				$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
			} 

	    	if( $sending )
	    	{
	    		$this->mail->setViewDir($this->email_view_path);
	    		 
	    		$view_data = array(
	    			'file_data' => $file_data,
	    			'project_data' => $project_data,
	    			'task_data' => $task_data,
	    			'revision_data' => $revision_data
	    		);
	    		 
	    		$this->mail->setEmailView('file-add', $view_data);
	    		$this->mail->setSubject($this->mail->translator->translate('email_subject_file_add', 'pm').': '.$file_data['name']);
	    		$this->mail->send($mail->transport);
	    	}
		} 	
    }
    
    /**
     * Sends the email when a file revision is uploaded
     * @param \Zend\EventManager\Event $event
     */    
    public function sendFileRevisionAdd(\Zend\EventManager\Event $event)
    {
    	$revision_id = $event->getParam('revision_id');
    	$file = $event->getTarget();
    	$revision_data = $file->revision->getRevision($revision_id);
    	$file_data = $file->getFileById($revision_data['file_id']);
    	
    	$task_data = $project_data = false;
    	if($file_data['project_id'] != '0')
    	{
    		$project_data = $this->project->getProjectById($file_data['project_id']);
    		if(isset($file_data['task']))
    		{
    			$task_data = $this->task->getTaskById($file_data['task']);
    		}
    	
    		//ok; now let's grab the project team members
    		$team = $this->project->getProjectTeamMembers($file_data['project_id']);
    		$sending = FALSE;
    		foreach($team AS $member)
    		{
    			if($this->user->checkPreference($member['user_id'], 'noti_file_uploaded', '1') == '0')
    			{
    				continue;
    			}
    	
    			$sending = TRUE;
    			$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
    		}
    	
    		if( $sending )
    		{
    			$this->mail->setViewDir($this->email_view_path);
    	
    			$view_data = array(
    				'file_data' => $file_data,
    				'project_data' => $project_data,
    				'task_data' => $task_data,
    				'revision_data' => $revision_data
    			);
    	
    			$this->mail->setEmailView('file-revision-add', $view_data);
    			$this->mail->setSubject($this->mail->translator->translate('email_subject_file_revision_add', 'pm').': '.$file_data['name']);
    			$this->mail->send($mail->transport);
    		}
    	}    	
    }
}