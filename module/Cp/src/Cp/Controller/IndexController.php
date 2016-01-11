<?php
namespace Cp\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cp\Controller\AbstractCpController;

class IndexController extends AbstractCpController
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
    
        $this->layout()->setVariable('active_nav', 'admin');
        parent::onDispatch($e);
    }
    
    public function indexAction()
    {
        return new ViewModel();
    }
}

