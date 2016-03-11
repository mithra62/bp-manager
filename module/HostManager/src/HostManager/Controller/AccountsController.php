<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Controller/AccountsController.php
 */
namespace HostManager\Controller;

use Application\Controller\AbstractController;

/**
 * HostManager - Accounts Controller
 *
 * Handles account routing
 *
 * @package HostManager\Accounts
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/Controller/AccountsController.php
 */
class AccountsController extends AbstractController
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function signupAction()
    {
        if ($this->identity) {
            $login = $this->getServiceLocator()->get('Application\Model\Login');
            $login->logout($this->getSessionStorage(), $this->getAuthService());
        }
        
        $form = $this->getServiceLocator()->get('HostManager\Form\SignupForm');
        $status = $this->params()->fromRoute('status');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $account = $this->getServiceLocator()->get('HostManager\Model\Accounts');
            $user = $this->getServiceLocator()->get('Application\Model\Users');
            $hash = $this->getServiceLocator()->get('Application\Model\Hash');
            $company = $this->getServiceLocator()->get('PM\Model\Companies');
            $setting = $this->getServiceLocator()->get('Application\Model\Settings');
            $option = $this->getServiceLocator()->get('PM\Model\Options');
            
            $form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $account_data = $account->createAccount($data, $user, $company, $hash, $setting, $option);
                if ($account_data) {
                    return $this->redirect()->toRoute('hosted-accounts/signup', array(
                        'status' => 'complete'
                    ));
                }
            }
        }
        
        $view = array();
        $view['messages'] = $this->flashMessenger()->getMessages();
        $view['form'] = $form;
        $view['status'] = $status;
        return $view;
    }

    public function confirmAction()
    {
        $code = $this->params()->fromRoute('confirm_code');
        $user = $this->getServiceLocator()->get('HostManager\Model\Users');
        $invite = $this->getServiceLocator()->get('HostManager\Model\Account\Invites');
        
        $invite_data = $invite->getInvite(array(
            'verification_hash' => $code
        ));
        if (! $invite_data) {
            return $this->redirect()->toRoute('login');
        }
        
        if ($invite->approveCode($code)) {
            $this->flashMessenger()->addMessage($this->translate('invite_accepted', 'hm'));
            return $this->redirect()->toRoute('login');
        }
    }

    public function indexAction()
    {
        return $this->redirect()->toRoute('hosted-accounts/signup');
    }
}