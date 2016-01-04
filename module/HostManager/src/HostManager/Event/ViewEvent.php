<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Event/ViewEvent.php
 */

namespace HostManager\Event;

use Base\Event\BaseEvent;

/**
 * HostManager - View Event
 * 
 * Injects the view event
 *
 * @package 	HostManager\View
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Event/ViewEvent.php
 */
class ViewEvent extends BaseEvent
{	
	/**
	 * The user ID to check against
	 * @var int
	 */
	private $identity = null;
	
	/**
	 * The User object
	 * @var \Api\Model\Users
	 */
	private $user = null;
	
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'view.render.account' => 'modAccountView'
    );
    
    /**
     * @ignore
     * @param unknown $identity
     * @param \PM\Model\Users $user
     */
    public function __construct($identity, \Api\Model\Users $user, \HostManager\Model\Accounts $account)
    {
    	parent::__construct();
    	$this->identity = $identity;
    	$this->user = $user;
    	$this->account = $account;
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
     * Injects additional views to be executed
     * @param \Zend\EventManager\Event $event
     * @return array
     */
	public function modAccountView(\Zend\EventManager\Event $event)
	{
		$partials = $event->getParam('partials');
		$accounts = $this->account->getUserAccounts(array('user_id' => $this->identity));
		if($accounts && is_array($accounts) && count($accounts) > 1)
		{
			$partials[] = 'host-manager/accounts/partials/user_accounts';
			$event->setParam('partials', $partials);
			return $partials;
		}
		
		return $partials;
	}
}