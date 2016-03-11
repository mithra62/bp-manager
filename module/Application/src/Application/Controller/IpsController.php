<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Controller/IpsController.php
 */
namespace Application\Controller;

use Application\Controller\AbstractController;

/**
 * PM - Ips Controller
 *
 * Routes the IP Blocker requests
 *
 * @package IpLocker
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Controller/IpsController.php
 */
class IpsController extends AbstractController
{
    public function blockedAction()
    {}

    public function allowSelfAction()
    {
        $ip = $this->getServiceLocator()->get('PM\Model\Ips');
        $form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $user = $this->getServiceLocator()->get('PM\Model\Users');
                $mail = $this->getServiceLocator()->get('Application\Model\Mail');
                $form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
                $hash = $this->getServiceLocator()->get('Application\Model\Hash');
                $user_data = $user->getUserById($this->identity);
                if ($ip->allowSelf($request->getServer()
                    ->get('REMOTE_ADDR'), $user_data, $mail, $hash)) {
                    $this->flashMessenger()->addMessage($this->translate('ip_allow_verify_sent', 'pm'));
                    return $this->redirect()->toRoute('ips/self-allow');
                }
            }
        }
        $view = array();
        $view['form'] = $form;
        return $view;
    }

    public function verifyCodeAction()
    {
        $code = $this->params()->fromRoute('verify_code');
        $ip = $this->getServiceLocator()->get('PM\Model\Ips');
        
        $ip_data = $ip->getIp(array(
            'confirm_key' => $code
        ));
        if (! $ip_data) {
            $this->flashMessenger()->addErrorMessage($this->translate('ip_allow_bad_code', 'pm'));
            return $this->redirect()->toRoute('ips/self-allow');
        }
        
        if ($ip->allowCodeAccess($code)) {
            $this->flashMessenger()->addMessage($this->translate('ip_allow_code_access_sucess', 'pm'));
            return $this->redirect()->toRoute('pm');
        }
        
        $this->flashMessenger()->addMessage($this->translate('ip_allow_code_access_fail', 'pm'));
        return $this->redirect()->toRoute('ips/self-allow');
    }
}