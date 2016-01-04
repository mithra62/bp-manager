<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Manager/src/Manager/Controller/CliController.php
 */

namespace HostManager\Controller;

use Application\Controller\AbstractController;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\Exception\RuntimeException;

/**
 * HostManager - Command Line Controller
 *
 * Handles the HostManager module Console requests
 *
 * @package 	HostManager\Console
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Manager/src/Manager/Controller/CliController.php
 */
class CliController extends AbstractController
{	
	
	/**
	 * The Cron execution action
	 */
	public function cronAction()
	{
		$console = $this->getServiceLocator()->get('Console');
		$cron = $this->getServiceLocator()->get('Crons');
		$cron->setServiceLocator($this->getServiceLocator());
		$cron->run($console);
	}
	
	/**
	 * Console command to archive tasks 
	 * @return string
	 */
    public function archiveTasksAction()
    {
    	$days = $this->params()->fromRoute('days', 7);
    	$status = $this->params()->fromRoute('status', 6);
    	$verbose = $this->params()->fromRoute('verbose');
    	
    	$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    	$return = $task->autoArchive($days, $status);  
    	if($verbose)
    	{
    		return $return;
    	}
    } 

    /**
     * Sends the Daily Task Reminder email
     * @return string
     */
	public function dailyTaskReminderAction()
	{
    	$member_id = $this->params()->fromRoute('member_id');
    	$email = $this->params()->fromRoute('email');
    	$verbose = $this->params()->fromRoute('verbose');
    	$future_days = $this->params()->fromRoute('future_days', 30);
    	
    	return $this->sendTaskReminder($member_id, $email, $verbose, $future_days);
	}
	
	/**
	 * Sends the Task Reminder email
	 * @param string $member_id
	 * @param string $email
	 * @param string $verbose
	 * @param number $future_days
	 * @return string
	 */
	public function sendTaskReminder($member_id = FALSE, $email = FALSE, $verbose = FALSE, $future_days = 30)
	{
		$user = $this->getServiceLocator()->get('PM\Model\Users');
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		 
		if($member_id || $email)
		{
			$user_data = ($member_id ? $user->getUserById($member_id) : $user->getUserByEmail($email));
			if(!$user_data)
			{
				return 'User not found';
			}
		
			$user_data = array($user_data);
		}
		else
		{
			$user_data = $user->getAllUsers('d');
		}

		$this->console = $this->getServiceLocator()->get('Console');
		if (!$this->console instanceof Console) {
			throw new RuntimeException('Cannot obtain console adapter. Are we running in a console?');
		}
				
		if($verbose)
		{
			$this->console->clear();
			$this->console->writeLine('Sending Task Reminder Email to '.count($user_data).' users...');
			$this->console->writeLine();
		}
				
		foreach($user_data AS $member)
		{
			if($user->checkPreference($member['id'], 'noti_daily_task_reminder', '1') == '0')
			{
				if($verbose)
				{
					$this->console->writeLine('Skipping '.$member['email'].' for preference reasons...');
				}
				
				continue;
			}
		
			$user_tasks = $user->getAssignedTasks($member['id'], $future_days);
			if( !$user_tasks )
			{
				$this->console->writeLine('Skipping '.$member['email'].' since there aren\'t any tasks to remind for...');
				continue;
			}
			
			if($verbose)
			{
				$this->console->writeLine('Sending to '.$member['email'].'...');
			}
			
			$mail = $this->getServiceLocator()->get('Application\Model\Mail');
			$mail->setTranslationDomain('pm');
			$this->email_view_path = $mail->getModulePath(__DIR__).'/view/emails';
			$mail->setTo($member['email'], $member['first_name'].' '.$member['last_name']);
			$mail->setViewDir($this->email_view_path);
			$mail->setEmailView('task-reminder', array('user_data' => $member, 'tasks' => $user_tasks));
			$mail->setSubject('daily_task_reminder_email_subject');
			$mail->send();
			
			if($verbose)
			{
				$this->console->writeLine('Sent to '.$member['email'].'!');
				$this->console->writeLine('');
			}
		}
		
		return 'done';
	}
}
