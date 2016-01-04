<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Event/NotificationEvent.php
 */

namespace HostManager\Event;

use PM\Event\NotificationEvent AS PMNotificationEvent;
use Application\Model\Mail;
use PM\Model\Users;
use PM\Model\Projects;
use PM\Model\Tasks;

/**
 * HostManager - Notification Events
 *
 * @package 	Events
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Event/NotificationEvent.php
 */
class NotificationEvent extends PMNotificationEvent
{   
	public function __construct( Mail $mail, Users $users, Projects $project, Tasks $task, $identity = null)
	{
		parent::__construct($mail, $users, $project, $task, $identity);
	}
	
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
    	'task.update.pre' => 'sendTaskUpdate',
    	'task.assign.pre' => 'sendTaskAssign',
    	'project.removeteammember.pre' => 'sendRemoveFromProjectTeam',
    	'project.addteam.post' => 'sendAddProjectTeam',
    	'file.add.post' => 'sendFileAdd',
    	'invite.add.post' => 'sendInviteAdd',
    	'account.add.post' => 'sendAccountAdd',
    	'file.revision.add.post' => 'sendFileRevisionAdd',
    );

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
     * We disable this so we don't send on user creation
     * @param \Zend\EventManager\Event $event
     */
    public function sendUserAdd(\Zend\EventManager\Event $event)
    {
    	  	
    }
    
    /**
     * Send an invite email to the user 
     * @param \Zend\EventManager\Event $event
     */
    public function sendInviteAdd(\Zend\EventManager\Event $event)
    {
		$this->email_view_path = $this->mail->getModulePath(__DIR__).'/view/emails';
    	$invite_id = $event->getParam('invite_id');
    	$invite = $event->getTarget();
    	$invite_data = $invite->getInvite(array('ai.id' => $invite_id));
    	$invite_url = $this->mail->web_url.'/invite/confirm/'.$invite_data['verification_hash'];
    	
    	$this->mail->addTo($invite_data['email'], $invite_data['first_name'].' '.$invite_data['last_name']);
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('account-invite', array('invite_data' => $invite_data, 'invite_url' => $invite_url));
    	$this->mail->setTranslationDomain('hm');
    	$this->mail->setSubject('account_invite_email_subject');
    	$this->mail->send();
    }
    
    /**
     * Send an account creation email to the user 
     * @param \Zend\EventManager\Event $event
     */
    public function sendAccountAdd(\Zend\EventManager\Event $event)
    {
		$this->email_view_path = $this->mail->getModulePath(__DIR__).'/view/emails';
    	$account_id = $event->getParam('account_id');
    	$user_id = $event->getParam('user_id');
    	
    	$account = $event->getTarget();
    	$account_data = $account->getAccount(array('a.id' => $account_id));
    	$account_url = $account->createAccountUrl($account_id);
    	
    	$user_data = $this->user->getUserById($user_id);
    	if( $user_data )
    	{
	    	$this->mail->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
	    	$this->mail->setViewDir($this->email_view_path);
	    	$this->mail->setEmailView('account-create', array('user_data' => $user_data, 'account_data' => $account_data, 'account_url' => $account_url));
	    	$this->mail->setTranslationDomain('hm');
	    	$this->mail->setSubject('account_create_email_subject');
	    	$this->mail->send();
    	}
    }
}