<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
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
 * @package Users\Login
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Accplication/Controller/AccountController.php
 */
class AccountController extends AbstractController
{
    /**
     * (non-PHPdoc)
     * @see \Application\Controller\AbstractController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if (!$this->getIdentity() && $this->params()->fromRoute('action') != 'register') {
            return $this->redirect()->toRoute('login');
        }
        
        $this->layout()->setVariable('active_nav', 'account');
        parent::onDispatch($e);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    public function registerAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\UsersForm');
        $form = $form->registrationForm();
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $formData = $request->getPost();
            $user = $this->getServiceLocator()->get('Application\Model\Users');
            $hash = $this->getServiceLocator()->get('Application\Model\Hash');
            $roles = $this->getServiceLocator()->get('Application\Model\User\Roles');
            $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
            
            $form->setInputFilter($user->getRegistrationInputFilter($translate));
            $form->setData($formData);
            if ($form->isValid()) {
                $data = $formData->toArray();
                $data['user_roles'] = $this->settings['default_user_groups'];
                $user_id = $id = $user->addUser($data, $hash, $roles);
                if ($user_id) {
                    $this->flashMessenger()->addMessage($this->translate('account_created', 'app'));
                    return $this->redirect()->toRoute('login');
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'pm')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                }
            } else {
                $form->setData($formData);
            }
        }
        
        $view = array();
        $view['form'] = $form;
        return $view;
    }

    public function changePasswordAction()
    {
    	$user = $this->getServiceLocator()->get('Application\Model\Users');
		$form = $this->getServiceLocator()->get('Application\Form\PasswordForm');
		$hash = $this->getServiceLocator()->get('Application\Model\Hash');
		$form = $form->confirmField();
		$request = $this->getRequest();
        if ($request->isPost()) {
            $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($user->getPasswordInputFilter($this->getIdentity(), $hash, true, $translate));
			$form->setData($formData);
			if ($form->isValid($formData)) {
				if($user->changePassword($this->identity, $formData['new_password'])){
			    	$this->flashMessenger()->addMessage($this->translate('password_has_reset', 'app'));
					return $this->redirect()->toRoute('account/change_password');		
				}
			}   
		} 
		
		$view = array();
		$view['form'] = $form;
        $view['active_sidebar'] = 'password';
        return $view;
    }

    public function preferencesAction()
    {
        $view = array();
        $view['active_sidebar'] = 'preferences';
        
        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('Application\Form\PrefsForm');
        if ($request->isPost()) {
            $ud = $this->getServiceLocator()->get('Application\Model\User\Data');
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($ud->getInputFilter());
            $form->setData($formData);
            if ($form->isValid($formData)) {
                if($ud->updateUserData($formData->toArray(), $this->getIdentity())) {
                    $this->flashMessenger()->addMessage($this->translate('preferences_updated', 'app'));
                    return $this->redirect()->toRoute('account/preferences');
                }
            }
        }
        
        $form->setData($this->prefs);
        $view['form'] = $form;
        return $view;
    }

    public function emailSettingsAction()
    {
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $form = $this->getServiceLocator()->get('Application\Form\EmailForm');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $form = $form->confirmPasswordField();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
            $form->setInputFilter($user->getEmailInputFilter($this->identity, $hash, $translate));
            $form->setData($formData);
            if ($form->isValid($formData)) {
                if($user->changeEmail($this->getIdentity(), $formData['new_email'])){
                    $this->flashMessenger()->addMessage($this->translate('email_has_changed', 'app'));
                    return $this->redirect()->toRoute('account/email_settings');
                }
            }
        }
        
        $view = array();
        $view['form'] = $form;
        $view['active_sidebar'] = 'email';
        return $view;
    }
    
    public function verifyEmailAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData))
            {
                $user = $this->getServiceLocator()->get('Application\Model\Users');
                $hash = $this->getServiceLocator()->get('Application\Model\Hash');   
                $mail = $this->getServiceLocator()->get('Application\Model\Mail');   
                $user->sendVerifyEmail($this->getIdentity(), $mail, $hash);
                
                $user_data = $user->getUserById($this->getIdentity());
                $this->flashMessenger()->addMessage(sprintf($this->translate('verify_email_sent', 'app'), $user_data['email']));
                return $this->redirect()->toRoute('account/verify_email');
                
            }
        }
        
        $view = array();
        $view['form'] = $form;
        $this->layout()->setVariable('disable_email_verify_message', true);
        return $view;
    }
    
    public function verifyEmailConfirmAction()
    {
        $hash = $this->params()->fromRoute('hash');
        if (! $hash) {
            return $this->redirect()->toRoute('account');
        }
        
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $user_data = $user->getUserByVerifyHash($hash);
        if (! $user_data) {
            return $this->redirect()->toRoute('account');
        }  
        
        if( $user->verifyEmailHash($hash) )
        {
            $this->flashMessenger()->addMessage(sprintf($this->translate('verify_email_successful', 'app'), $user_data['email']));
        }
        
        return $this->redirect()->toRoute('account');
    }
}

