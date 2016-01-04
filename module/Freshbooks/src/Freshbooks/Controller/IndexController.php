<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/IndexController.php
 */

namespace Freshbooks\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Index Controller
 *
 * Routes the Home requests
 *
 * @package 	Dashboard
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/IndexController.php
 */
class IndexController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(\Zend\Mvc\MvcEvent $e)
	{
		$e = parent::onDispatch($e);
		$this->layout()->setVariable('active_nav', 'admin');	
		return $e;
	}
		
    public function indexAction()
    {
 
    }
    
    public function infoAction()
    {
    	phpinfo();
    	exit;
    }
}
