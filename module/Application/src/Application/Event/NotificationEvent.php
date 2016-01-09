<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Event/NotificationEvent.php
 */
namespace Application\Event;

use Base\Event\BaseEvent;
use Application\Model\Mail;
use Application\Model\Users;

/**
 * Application - Notification Events
 *
 * @package Events
 * @author Eric Lamb
 * @filesource ./module/Application/src/Application/Event/NotificationEvent.php
 */
class NotificationEvent extends BaseEvent
{

    /**
     * User Identity
     * 
     * @var int
     */
    public $identity = false;

    /**
     * The hooks used for the Event
     * 
     * @var array
     */
    private $hooks = array(
        'user.add.post' => 'sendUserAdd'
    );

    /**
     * The Notification Event
     * 
     * @param Mail $mail            
     * @param Users $users            
     * @param Projects $project            
     * @param Tasks $task            
     * @param string $identity            
     */
    public function __construct(Mail $mail, Users $users, $identity = null)
    {
        $this->mail = $mail;
        $this->identity = $identity;
        $this->user = $users;
        
        $this->email_view_path = $this->mail->getModulePath(__DIR__) . '/view/emails';
    }

    /**
     * Registers the Event with ZF and our Application Model
     * 
     * @param \Zend\EventManager\SharedEventManager $ev            
     */
    public function register(\Zend\EventManager\SharedEventManager $ev)
    {
        foreach ($this->hooks as $key => $value) {
            $ev->attach('Base\Model\BaseModel', $key, array(
                $this,
                $value
            ));
        }
    }

    /**
     * Sends the user registration notification
     * 
     * @param \Zend\EventManager\Event $event            
     */
    public function sendUserAdd(\Zend\EventManager\Event $event)
    {
        $data = $event->getParam('data');
        $user_id = $event->getParam('user_id');
        $this->mail->addTo($data['email']);
        $this->mail->setViewDir($this->email_view_path);
        $this->mail->setEmailView('user-registration', array(
            'user_data' => $data,
            'user_id' => $user_id
        ));
        
        $this->mail->setSubject('user_registration_email_subject');
        $this->mail->send();
    }
}