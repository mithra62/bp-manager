<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Accplication/Controller/AccountController.php
 */

namespace Application\Controller;

use Application\Controller\AbstractController;
use Zend\View\Model\ViewModel;

/**
 * Application - Login Class
 *
 * Handles user account routing 
 *
 * @package 	Users\Login
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Accplication/Controller/AccountController.php
 */

class AccountController extends AbstractController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function registerAction()
    {
    	$form = $this->getServiceLocator()->get('Application\Form\UsersForm');
    	$form = $form->registrationForm();
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{	
    	    $formData = $request->getPost();
    		$user = $this->getServiceLocator()->get('Application\Model\Users');
            $hash = $this->getServiceLocator()->get('Application\Model\Hash');
			$form->setInputFilter($user->getRegistrationInputFilter());
			$form->setData($formData);
			if ($form->isValid()) 
			{
                echo 'f';
                exit;
			}
			else
			{
			    $form->setData($formData);
			}
    	}
    	
    	$view = array();
        $view['form'] = $form;
        return $view;
    }
    
    public function editAction()
    {
        return new ViewModel();
    }
    
    public function changePasswordAction()
    {
        return new ViewModel();
    }
}

