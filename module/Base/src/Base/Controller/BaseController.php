<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Controller/BaseController.php
 */

namespace Base\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Base\Traits\Controller AS ControllerTrait;

/**
 * Base - Controller
 * 
 * Contains all the global logic for Controllers
 * <br /><strong>The Base Controller should be the parent of any Controllers within the system</strong>
 *
 * @package 	MojiTrac\Controller
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Base/src/Base/Controller/BaseController.php
 */
abstract class BaseController extends AbstractActionController
{
	/**
	 * Grab the global trait goodness...
	 */
	use ControllerTrait;
	
	/**
	 * ZF Config
	 * Contains the entire compiled configuration 
	 * @var Array
	 */
	public $config = array();
	
	/**
	 * Sets up the Controller defaults
	 * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->config = $this->getServiceLocator()->get('Config');	
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		return parent::onDispatch( $e );
	}
	
	/**
	 * Wraps up Ajax capable Action returns 
	 * @param array $view
	 * @return \Zend\View\Model\ViewModel|boolean
	 */
	public function ajaxOutput(array $view = array())
	{
	    if ($this->getRequest()->isXmlHttpRequest())
	    {
	    	$result = new ViewModel();
	    	$result->setTerminal(true);
	    	$view['ajax_mode'] = true;
	    	$result->setVariables($view);
	    	return $result;
	    }

	    return $view;
	}
}