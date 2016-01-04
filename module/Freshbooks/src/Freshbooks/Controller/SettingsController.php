<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/Freshbooks/Controller/SettingsController.php
 */

namespace Freshbooks\Controller;

use PM\Controller\AbstractPmController;

/**
 * Freshbooks - Settings Controller
 *
 * Routes the Settings requests
 *
 * @package 	Freshbooks\Settings
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Freshbooks/src/Freshbooks/Controller/SettingsController.php
 */
class SettingsController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(\Zend\Mvc\MvcEvent $e)
	{
		$e = parent::onDispatch($e);
		$this->layout()->setVariable('active_nav', 'admin');	
		return $e;
	}
		
    public function indexAction()
    {
       
		$this->layout()->setVariable('layout_style', 'left');
		
		return array();
    }
    
    /**
     * Controller action for linking MojiTrac up to a Freshbooks account
     */
    public function linkAccountAction()
    {
		$credentials = $this->getServiceLocator()->get('Freshbooks\Model\Credentials');
		$form = $this->getServiceLocator()->get('Freshbooks\Form\CredentialsForm');
		$form->setData(
			array(
				'freshbooks_api_url' => $this->settings['freshbooks_api_url'],
				'freshbooks_auth_token' => $this->settings['freshbooks_auth_token']
			)
		);
		
		$request = $this->getRequest();
		if ($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			$form->setInputFilter($credentials->getInputFilter());
			$form->setData($data);
			if ($form->isValid())
			{
    			$setting = $this->getServiceLocator()->get('Application\Model\Settings');
				$data = $form->getData();
				if($setting->updateSettings($data))
				{
			    	$this->flashMessenger()->addMessage('Freshbooks credentials updated!');
					return $this->redirect()->toRoute('freshbooks');	
				}
			}	

		}

		$this->layout()->setVariable('layout_style', 'left');
		$view = array();
		$view['form'] = $form;
		$view['form_action'] = $this->getRequest()->getRequestUri();
		return $this->ajaxOutput($view);
    }
}
