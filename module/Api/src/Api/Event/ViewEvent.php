<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Event/ViewEvent.php
 */
namespace Api\Event;

use Base\Event\BaseEvent;

/**
 * Api - View Event
 *
 * Injects the view event
 *
 * @package Api\View
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Event/ViewEvent.php
 */
class ViewEvent extends BaseEvent
{

    /**
     * The user ID to check against
     * 
     * @var int
     */
    private $identity = null;

    /**
     * The User object
     * 
     * @var \Api\Model\Users
     */
    private $user = null;

    /**
     * The hooks used for the Event
     * 
     * @var array
     */
    private $hooks = array(
        'view.render.account' => 'modAccountView'
    );

    /**
     *
     * @ignore
     *
     * @param unknown $identity            
     * @param \PM\Model\Users $user            
     */
    public function __construct($identity, \Api\Model\Users $user)
    {
        parent::__construct();
        $this->identity = $identity;
        $this->user = $user;
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
     * Injects additional views to be executed
     * 
     * @param \Zend\EventManager\Event $event            
     * @return array
     */
    public function modAccountView(\Zend\EventManager\Event $event)
    {
        $partials = $event->getParam('partials');
        
        // we only want to set the view if the user is allowed to access the REST API
        if ($this->user->roles->perm->check($this->identity, 'access_rest_api')) {
            $partials[] = 'api/account/key';
            $event->setParam('partials', $partials);
        }
        
        return $partials;
    }
}