<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/Freshbooks/Event/ViewEvent.php
 */

namespace Freshbooks\Event;

use Base\Event\BaseEvent;

/**
 * Freshbooks - View Event
 * 
 * Injects the needed Freshbooks settings into the MojiTrac settings system
 *
 * @package 	Freshbooks\View
 * @author		Eric Lamb
 * @filesource 	./module/Freshbooks/src/Freshbooks/Event/ViewEvent.php
 */
class ViewEvent extends BaseEvent
{	
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'view.render.admin' => 'modAdminView'
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
	public function modAdminView(\Zend\EventManager\Event $event)
	{
		$partials = $event->getParam('partials');
		$partials[] = 'freshbooks/admin/admin-options';
		$event->setParam('partials', $partials);
		return $partials;
		
	}
}