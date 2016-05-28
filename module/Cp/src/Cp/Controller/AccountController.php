<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/AccountController.php
 */
namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Account Controller
 *
 * Routes the user account requests
 *
 * @package Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Controller/AccountController.php
 */
class AccountController extends AbstractPmController
{

    /**
     * (non-PHPdoc)
     * 
     * @see \PM\Controller\AbstractPmController::onDispatch()
     * @ignore
     *
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $e = parent::onDispatch($e);
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('sub_menu', 'account');
        $this->layout()->setVariable('active_nav', 'account');
        $this->layout()->setVariable('uri', $this->getRequest()
            ->getRequestUri());
        
        return $e;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $view['sub_menu'] = 'settings';
        return $view;
    }

    /**
     * Handles modifying a password
     * 
     * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <\Zend\View\Model\ViewModel, boolean, array>
     */
    public function passwordAction()
    {
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $form = $this->getServiceLocator()->get('Application\Form\PasswordForm');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $form = $form->confirmField();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($user->getPasswordInputFilter($this->identity, $hash));
            $form->setData($formData);
            if ($form->isValid($formData)) {
                if ($user->changePassword($this->identity, $formData['new_password'])) {
                    $this->flashMessenger()->addMessage($this->translate('password_changed', 'pm'));
                    return $this->redirect()->toRoute('account/password');
                }
            }
        }
        
        $this->layout()->setVariable('layout_style', 'right');
        $this->layout()->setVariable('active_sub', 'password');
        $this->layout()->setVariable('sub_menu', 'settings');
        $view['form'] = $form;
        return $view;
    }

    /**
     * Manage user preferences action
     * 
     * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <\Zend\View\Model\ViewModel, boolean, array>
     */
    public function prefsAction()
    {
        $view = array();
        $view['sub_menu'] = 'settings';
        $view['active_sub'] = 'prefs';
        $view['layout_style'] = 'right';
        $view['sidebar'] = 'dashboard';
        
        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('PM\Form\PrefsForm');
        if ($request->isPost()) {
            $ud = $this->getServiceLocator()->get('Application\Model\User\Data');
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($ud->getInputFilter());
            $form->setData($formData);
            if ($form->isValid($formData)) {
                if ($ud->updateUserData($formData->toArray(), $this->identity)) {
                    $this->flashMessenger()->addMessage($this->translate('prefs_updated', 'pm'));
                    return $this->redirect()->toRoute('account/prefs');
                }
            }
        }
        
        $form->setData($this->prefs);
        $view['form'] = $form;
        $view['layout_style'] = 'right';
        $this->layout()->setVariable('layout_style', 'right');
        $this->layout()->setVariable('active_sub', 'prefs');
        return $view;
    }
}