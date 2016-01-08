<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Traits/Controller.php
 */

namespace Application\Traits;

/**
 * Application - Controller Trait
 *
 * Contains the global goodies for the PM module
 *
 * @package 	MojiTrac\Traits
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Traits/Controller.php
 */
trait Controller 
{
	/**
	 * Start up the IP Blocker
	 */
	protected function _initIpBlocker()
	{
		if(!empty($this->settings['enable_ip']) && $this->settings['enable_ip'] == '1')
		{
			$ip = $this->getServiceLocator()->get('PM\Model\Ips');
			if(!$ip->isAllowed($this->getRequest()->getServer()->get('REMOTE_ADDR')))
			{	
				$good_controller = 'PM\Controller\Ips';
				$good_actions = array('blocked', 'allowSelf');
				$controller = $this->getEvent()->getRouteMatch()->getParam('controller', 'index');
				$action = $this->getEvent()->getRouteMatch()->getParam('action', 'index');
				
				//check if we even have a chance of bypassing things
				if(!$this->perm->check($this->identity, 'self_allow_ip'))
				{
					if($controller != $good_controller || !in_array($action, $good_actions))
					{
						return $this->redirect()->toRoute('ips/blocked');
					}
				}
				else
				{
					if($controller != $good_controller || ($controller == $good_controller && !in_array($action, $good_actions)))
					{
						return $this->redirect()->toRoute('ips/self-allow');
					}
				}
			}
		}
	}
	
	/**
	 * Start up the preferences and settings overrides
	 */
	protected function _initPrefs()
	{
		$user = $this->getServiceLocator()->get('PM\Model\Users');
		$this->prefs = $user->user_data->getUsersData($this->identity);
		foreach($this->settings AS $key => $value)
		{
			if(isset($this->prefs[$key]) && $this->prefs[$key] != '')
			{
				$this->settings[$key] = $this->prefs[$key];
			}
			else
			{
				$this->prefs[$key] = $this->settings[$key];
			}
		}
	}
}