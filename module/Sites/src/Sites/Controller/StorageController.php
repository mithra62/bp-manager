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
        $storage_details = $storage->getData();
        $resources = $storage->getResources();
        
        $view = array();
        $view['can_remove'] = true;
        $view['storage_locations'] = array();
        if( $storage_details['total_locations'] <= 1 )
        {
            $view['can_remove'] = false;
            $view['storage_locations'] = $resources['storage'];
        }
        
        $view['section'] = 'view_sites';
        $view['active_sidebar'] = 'manage_sites';
        return $view;
    }
    
    /**
     * Adds a site
     * @return \Zend\Http\Response|\Sites\Form\SiteForm
     */
    public function addAction()
    {
        if (! $this->perm->check($this->identity, 'manage_sites')) {
            return $this->redirect()->toRoute('sites');
        }
        
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $site_form = $this->getServiceLocator()->get('Sites\Form\SiteForm');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        
        $view = array();
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
            return $this->redirect()->toRoute('sites');
        }
        
        $view = array();
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $id = $this->params()->fromRoute('site_id');
        if (! $id) {
            return $this->redirect()->toRoute('sites');
        }
        
        $view['site_data'] = $site->getSiteById($id);
        
        if (! $view['site_data']) {
            return $this->redirect()->toRoute('sites');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $formData = $formData->toArray();
                if ($site->removeSite($id)) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('site_removed', 'sites'));
                    return $this->redirect()->toRoute('sites');
                }
            }
        }
        
        $view['id'] = $id;
        $view['form'] = $form;
        $view['section'] = 'view_sites';
        $view['active_sidebar'] = 'manage_sites';
        return $this->ajaxOutput($view);
    }
}
