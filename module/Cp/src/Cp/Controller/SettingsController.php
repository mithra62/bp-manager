<?php
namespace Cp\Controller;

use Cp\Controller\AbstractCpController;

class SettingsController extends AbstractCpController
{   
    /**
     * (non-PHPdoc)
     * @see \Application\Controller\AbstractController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        if (!$this->getIdentity()) {
            return $this->redirect()->toRoute('login');
        }
    
        return parent::onDispatch($e);
    }
    
    public function indexAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\SettingsForm');
        $form = $form->getGeneralForm();
        $request = $this->getRequest();
        
        $setting = $this->getServiceLocator()->get('Application\Model\Settings');
        $form->setData($setting->getSettings());
        if ($request->isPost()) {
            $formData = $request->getPost();
            $form->setInputFilter($setting->getGeneralInputFilter());
            $form->setData($formData);
            if ($form->isValid()) {
                if($setting->updateSettings($formData->toArray())){
                    $this->flashMessenger()->addSuccessMessage($this->translate('settings_updated', 'app'));
                    return $this->redirect()->toRoute('system_settings');
                }                
            }
            else 
            {
                $form->setData($formData);
            }
        }
        
        //Application\Form\SettingsForm

        $view = array(
            'active_sidebar' => 'system_settings',
            'form' => $form
        );
        return $view;
    }
}

