<?php
namespace Cp\Controller;

use Zend\View\Model\ViewModel;
use Cp\Controller\AbstractCpController;

class IndexController extends AbstractCpController
{   
    public function indexAction()
    {
        return new ViewModel();
    }
}

