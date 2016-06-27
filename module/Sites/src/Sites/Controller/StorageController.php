<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/StorageController.php
 */
 
namespace Sites\Controller;

/**
 * Sites - Sites Storage Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
class StorageController extends AbstractSitesController
{
   
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $storage = $this->site->getApi()->getStorageLocations($this->site_data);
        
        $view = array();
        $view['can_remove'] = true;
        $view['storage_locations'] = $storage;
        if( count($view['storage_locations']) <= 1 )
        {
            $view['can_remove'] = false;
        }
        
        $options = $this->site->getApi()->getOptions($this->site_data);
        $view['available_storage_drivers'] = $options['available_storage_drivers'];
        $view['section'] = 'storage_locations';
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
    
    /**
     * Adds a site
     * @return \Zend\Http\Response|\Sites\Form\SiteForm
     */
    public function addAction()
    {
        $options = $this->site->getApi()->getOptions($this->site_data);
        $view['available_storage_drivers'] = $options['available_storage_drivers'];
        
        $site_form = $this->getServiceLocator()->get('Sites\Form\StorageForm');
        $view = $options = $this->site->getApi()->getOptions($this->site_data);
        $view['form_errors'] = $this->returnEmpty($this->site_data['settings']);
        
        $view['form'] = $site_form;
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $formData = $request->getPost();
            $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
            $inputFilter = $site->getInputFilter($translate);
            $site_form->setInputFilter($inputFilter);
            $site_form->setData($request->getPost());
            if ($site_form->isValid($formData)) {
                $data = $formData->toArray();
                $data['owner_id'] = $this->getIdentity();
                $site_id = $id = $site->addSite($data, $hash);
                if ($site_id) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('site_added', 'sites'));
                    return $this->redirect()->toRoute('sites/view', array(
                        'site_id' => $id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'app')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'app')
                );
                $this->layout()->setVariable('errors', $view['errors']);
                $site_form->setData($formData);
            }
        }
        
        $view['section'] = 'view_sites';
        $view['active_sidebar'] = 'manage_sites';
        return $view;
    }
    
    /**
     * Update a site action
     * @return \Zend\Http\Response|multitype:
     */
    public function editAction()
    {
        if (! $this->perm->check($this->identity, 'manage_sites')) {
            return $this->redirect()->toRoute('sites');
        }
        
        $id = $this->params()->fromRoute('site_id');
        if (! $id ) {
            return $this->redirect()->toRoute('sites');
        }

        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $site_data = $site->getSiteById($id, $hash);
        if (! $site_data ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $site_form = $this->getServiceLocator()->get('Sites\Form\SiteForm');
        $site_form->setData($site_data);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost();
            $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
            $site_form->setInputFilter($inputFilter = $site->getInputFilter($translate, $id));
            $site_form->setData($request->getPost());
            if ($site_form->isValid($formData)) {
                $formData = $formData->toArray();
                if ($site->updateSite($id, $formData, $hash)) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('site_updated', 'sites'));
                    return $this->redirect()->toRoute('sites/view', array(
                        'site_id' => $id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'app')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                    $site_form->setData($formData);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'app')
                );
                $this->layout()->setVariable('errors', $view['errors']);
                $site_form->setData($formData);
            }
        }        
        
        $view = array();
        $view['form'] = $site_form;  
        $view['section'] = 'view_sites';
        $view['active_sidebar'] = 'manage_sites';
        $view['site_data'] = $site_data;
        return $view;        
    }

    /**
     * Vew a site
     */
    public function viewAction()
    {
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
    }

    public function removeAction()
    {
        if (! $this->perm->check($this->identity, 'manage_sites')) {
            //return $this->redirect()->toRoute('site_storage');
        }
        
        $storage = $this->site->getApi()->getStorageLocations($this->site_data);
        if( count($storage) <= 1 ) {
            ee()->session->set_flashdata('message_error', $this->services['lang']->__('min_storage_location_needs'));
            $this->platform->redirect(ee('CP/URL', 'addons/settings/backup_pro/view_storage'));
            return $this->redirect()->toRoute('site_storage');
        }
        
        $storage_id = $this->params()->fromRoute('storage_id');
        if (! $storage_id) {
            return $this->redirect()->toRoute('site_storage');
        }
        
        $storage_location = $this->site->getApi()->getStorageLocation($this->site_data, $storage_id);
        if( !$storage_location ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $view = array();
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                
                $formData = $formData->toArray();
                if ($this->site->getApi()->deleteStorageLocation($this->site_data, $storage_id)) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('site_removed', 'sites'));
                    return $this->redirect()->toRoute('site_storage');
                }
            }
        }

        $options = $this->site->getApi()->getOptions($this->site_data);
        $view['available_storage_drivers'] = $options['available_storage_drivers'];
        $view['storage_id'] = $storage_id;
        $view['form'] = $form;
        $view['section'] = 'storage_locations';
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $view['storage_location'] = $storage_location;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $this->ajaxOutput($view);
    }
}

