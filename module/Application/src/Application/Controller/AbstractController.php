<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Controllers/AbstractController.php
 */
namespace Application\Controller;

use Base\Controller\BaseController;
use Application\Traits\Controller;

/**
 * Application - AbstractController Controller
 *
 * @package MojiTrac\Controller
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Controllers/AbstractController.php
 */
abstract class AbstractController extends BaseController
{
    use Controller;
    
    protected $admin_only = false;
    
    /**
     * (non-PHPdoc)
     * 
     * @see \Base\Controller\BaseController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        //setup system settings
        $settings = $this->getServiceLocator()->get('Application\Model\Settings');
        $this->settings = $settings->getSettings();
        $this->layout()->setVariable('settings', $this->settings);
        
        //setup translations
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))->setFallbackLocale('en_US');
        
        if( $this->getIdentity() )
        {
            $user = $this->getServiceLocator()->get('Application\Model\Users');
            $user_data = $user->user_data->getUsersData($this->identity);
            $user->setTimezone($user_data['timezone']);
            
            $this->perm = $this->getServiceLocator()->get('Application\Model\User\Permissions');
            
            $this->_initPrefs();
        }
        
        if( $this->admin_only )
        {
            if( !$this->checkPermission('admin_access') )
            {
                return $this->redirect()->toRoute('home');
            }
        }
        
        $this->_initIpBlocker();
        return parent::onDispatch($e);
    }

    /**
     * Global Logout Action
     * 
     * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
     */
    public function logoutAction()
    {
        $login = $this->getServiceLocator()->get('Application\Model\User\Login');
        $login->logout($this->getSessionStorage(), $this->getAuthService());
        
        $translate = $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('_');
        $this->flashmessenger()->addSuccessMessage($translate('youve_been_logged_out', 'app'));
        return $this->redirect()->toRoute('login');
    }
    
    /**
     * Provides oversight on permission dependant requsts
     *
     * @param string $permission
     * @param string $url
     */
    public function checkPermission($permission, $url = FALSE)
    {
        if ($this->getIdentity() && $this->perm->check($this->identity, $permission)) {
            return true;
        }
    }
}