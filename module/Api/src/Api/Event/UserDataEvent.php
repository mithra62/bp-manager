<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Event/UserDataEvent.php
 */

namespace Api\Event;

use Base\Event\BaseEvent;

/**
 * Api - User Data Event
 * 
 * Injects the needed PM user data into the MojiTrac user data system
 *
 * @package 	Api\Settings
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/Api/Event/UserDataEvent.php
 */
class UserDataEvent extends BaseEvent
{	
	/**
	 * The default settings to append to the Moji settings array
	 * @var array
	 */	
	private $default_settings = array(
		'rest_api_secret' => ''
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