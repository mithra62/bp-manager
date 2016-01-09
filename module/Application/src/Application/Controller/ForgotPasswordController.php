<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Controller/ForgotPasswordController.php
 */
namespace Application\Controller;

use Application\Controller\AbstractController;

/**
 * Application - Forgot Password Controller Class
 *
 * Handles forgot password logic and routing
 *
 * @package Users\Login\ForgotPassword
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Controller/ForgotPasswordController.php
 */
class ForgotPasswordController extends AbstractController
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Application\Controller\AbstractController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        return parent::onDispatch($e);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $fp = $this->getServiceLocator()->get('Application\Model\ForgotPassword');
        $form = $this->getServiceLocator()->get('Application\Model\ForgotPasswordForm');
        $request = $this->getRequest();
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($fp->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $mail = $this->getServiceLocator()->get('Application\Model\Mail');
                $hash = $this->getServiceLocator()->get('Application\Model\Hash');
                if ($fp->sendEmail($mail, $hash, $formData['email'])) {
                    $this->flashMessenger()->addMessage($this->translate('check_your_emmail', 'app'));
                    return $this->redirect()->toRoute('forgot-password');
                }
            }
        }
        
        $view = array();
        $view['messages'] = $this->flashMessenger()->getMessages();
        $view['form'] = $form;
        return $view;
    }

    /**
     * Action to reset a password
     *
     * Takes the `hash` route parameter, verifies it, and if successful should allow a user to change their password
     *
     * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <object, multitype:, \Application\Form\PasswordForm>
     */
    public function resetAction()
    {
        $hash = $this->params()->fromRoute('hash');
        if (! $hash) {
            return $this->redirect()->toRoute('forgot-password');
        }
        
        $fp = $this->getServiceLocator()->get('Application\Model\ForgotPassword');
        $user_data = $fp->users->getUserByPwHash($hash);
        if (! $user_data) {
            return $this->redirect()->toRoute('forgot-password');
        }
        
        $form = $this->getServiceLocator()->get('Application\Form\PasswordForm');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($fp->users->getPasswordInputFilter($this->identity, $hash, false));
            $form->setData($formData);
            if ($form->isValid($formData)) {
                $formData = $formData->toArray();
                if ($fp->users->changePassword($user_data['id'], $formData['new_password'])) {
                    $this->flashMessenger()->addMessage($this->translate('password_has_reset', 'app'));
                    return $this->redirect()->toRoute('login');
                }
            }
        }
        
        $view['form'] = $form;
        return $view;
    }
}