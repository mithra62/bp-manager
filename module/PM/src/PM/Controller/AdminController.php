<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/AdminController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Admin Controller
 *
 * Routes the Administration Panel requests
 *
 * @package 	Administration
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/AdminController.php
 */
class AdminController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        parent::check_permission('admin_access');
		$this->layout()->setVariable('active_nav', 'admin');
		$this->layout()->setVariable('sub_menu', 'admin');

		return $e;
	}
    
    public function indexAction()
    {

    }

    /**
     * Handles the system global settings.
     */
    public function settingsAction()
    {
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
        $this->layout()->setVariable('active_sub', 'global');
        $this->layout()->setVariable('layout_style','right');
            	
    	$setting = $this->getServiceLocator()->get('Application\Model\Settings');
		$form = $this->getServiceLocator()->get('PM\Form\SettingsForm');
        
        $settings = $setting->getSettings();
        $form->setData($settings);
        $view['settings'] = $this->settings;
        
       	if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
    		$formData = $formData->toArray();
			if($setting->updateSettings($formData))
			{
		    	$this->flashMessenger()->addMessage($this->translate('settings_updated', 'pm')); 
				return $this->redirect()->toRoute('admin/settings');	
			}
		}
		
		$view['form'] = $form;
		return $view;
    }
    
    public function optionsAction()
    {
    	
    }
}