<?php

namespace Sites\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractSitesController
{

    public function indexAction()
    {
        if (! $this->perm->check($this->identity, 'view_sites')) {
            return $this->redirect()->toRoute('home');
        }
        
        return new ViewModel();
    }


}

