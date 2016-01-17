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
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $section = $this->params()->fromRoute('section', 'general');
        $form = $this->getForm($section);
        $request = $this->getRequest();
        
        $setting = $this->getServiceLocator()->get('Application\Model\Settings');
        $form->setData($setting->getSettings());
        if ($request->isPost()) {
            $formData = $request->getPost();
            $form->setInputFilter($this->getInputFilter($section, $setting));
            $form->setData($formData);
            if ($form->isValid()) {
                if($setting->updateSettings($formData->toArray())){
                    $this->flashMessenger()->addSuccessMessage($this->translate('settings_updated', 'app'));
                    return $this->redirect()->toRoute('system_settings', array('section' => $section));
                }                
            }
            else 
            {
                $form->setData($formData);
            }
        }
        
        $view = array(
            'active_sidebar' => 'system_settings',
            'section' => $section,
            'form' => $form
        );
        return $view;
    }
    
    /**
     * Returns the Settings form for the section we're working with
     * @param string $section
     * @return Application\Form\SettingsForm
     */
    protected function getForm($section)
    {
        $form = $this->getServiceLocator()->get('Application\Form\SettingsForm');
        switch($section)
        {   
            case 'mail':
                $form = $form->getMailForm();
            break; 
            
            case 'general':
            default:
                $form = $form->getGeneralForm();
            break;
        }
        
        return $form;
    }
    
    protected function getInputFilter($section, \Application\Model\Settings $setting)
    {
        switch($section)
        {
            case 'mail':
                $filter = $setting->getMailInputFilter();
            break;
                
            case 'general':
            default:
                $filter = $setting->getGeneralInputFilter();
            break;
        }
        
        return $filter;
    }
}

