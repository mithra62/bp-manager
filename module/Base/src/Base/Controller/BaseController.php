<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Base/src/Base/Controller/BaseController.php
 */
namespace Base\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Base\Traits\Controller as ControllerTrait;

/**
 * Base - Controller
 *
 * Contains all the global logic for Controllers
 * <br /><strong>The Base Controller should be the parent of any Controllers within the system</strong>
 *
 * @package BackupProServer\Controller
 * @author Eric Lamb <eric@mithra62.com>
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
     * 
     * @var Array
     */
    public $config = array();

    /**
     * Sets up the Controller defaults
     * 
     * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->config = $this->getServiceLocator()->get('Config');
        $this->identity = $this->getServiceLocator()
            ->get('AuthService')
            ->getIdentity();
        return parent::onDispatch($e);
    }

    /**
     * Wraps up Ajax capable Action returns
     * 
     * @param array $view            
     * @return \Zend\View\Model\ViewModel|boolean
     */
    public function ajaxOutput(array $view = array())
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $result = new ViewModel();
            $result->setTerminal(true);
            $view['ajax_mode'] = true;
            $result->setVariables($view);
            return $result;
        }
        
        return $view;
    }
    
    public function getIdentity()
    {
        if( $this->identity == '' )
        {
            $this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
        }
        
        return $this->identity;
    }
    
    /**
     * Takes an array and returns an empty key => value set
     *
     * @param array $data
     * @return array
     */
    public function returnEmpty(array $data)
    {
        $return = array();
        foreach ($data as $key => $value) {
            $return[$key] = '';
        }
    
        return $return;
    }
}