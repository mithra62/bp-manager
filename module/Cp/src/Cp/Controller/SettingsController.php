<?php
namespace Cp\Controller;

use Zend\View\Model\ViewModel;
use Cp\Controller\AbstractCpController;

class SettingsController extends AbstractCpController
{   
    /**
     * (non-PHPdoc)
     * @see \Application\Controller\AbstractController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if (!$this->getIdentity()) {
            return $this->redirect()->toRoute('login');
        }
    
        $this->layout()->setVariable('active_sidebar', 'system_settings');
        return parent::onDispatch($e);
    }
    
    public function indexAction()
    {
        $view = array('active_sidebar' => 'system_settings');
        return $view;
    }
}

