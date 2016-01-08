<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/UserDataEvent.php
 */

namespace PM\Event;

use Base\Event\BaseEvent;

/**
 * PM - User Data Event
 * 
 * Injects the needed PM user data into the MojiTrac user data system
 *
 * @package 	PM\Settings
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/UserDataEvent.php
 */
class UserDataEvent extends BaseEvent
{	
	/**
	 * The default settings to append to the Moji settings array
	 * @var array
	 */	
	private $default_settings = array(
		'noti_assigned_task' => '1',
		'noti_status_task' => '1',
		'noti_priority_task' => '1',
		'noti_daily_task_reminder' => '1',
		'noti_add_proj_team' => '1',
		'noti_remove_proj_team' => '1',
		'noti_file_uploaded' => '1',
		'noti_file_revision_uploaded' => '1',
		'timer_data' => '0',
		'daily_reminder_schedule' => '9', //hour of day email will be sent
		'_daily_reminder_schedule_last_sent' => '',
		'task_reminder_upcoming_days' => '30'
	);
	
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'user_data.defaults.set.pre' => 'modUserDataDefaults'
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
     * Merges our settings array into the MojiTrac Settings array 
     * @param \Zend\EventManager\Event $event
     * @return array
     */
	public function modUserDataDefaults(\Zend\EventManager\Event $event)
	{
		$defaults = $event->getParam('defaults');
		$defaults = array_merge($defaults, $this->default_settings);
		$event->setParam('defaults', $defaults);
		return $defaults;
		
	}
}