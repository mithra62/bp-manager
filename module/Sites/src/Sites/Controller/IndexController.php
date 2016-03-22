<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/IndexController.php
 */
 
namespace Sites\Controller;

/**
 * Sites - Sites Index Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
class IndexController extends AbstractSitesController
{
    /**
     * The actions that will require site_id processing
     * @var array
     */
    protected $bypass_id = array('index', 'add');
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        if (! $this->perm->check($this->identity, 'view_sites')) {
            return $this->redirect()->toRoute('home');
        }
        
        $order = $this->getRequest()->getQuery('order', false);
        $order_dir = $this->getRequest()->getQuery('order_dir', false);
        $limit = $this->getRequest()->getQuery('limit', 10);
        $page = $this->getRequest()->getQuery('page', 1);
        
        $sites = $this->getServiceLocator()->get('Sites\Model\Sites');
        $sites_data = $sites->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAllSites();
        if(!$sites_data) {
            $this->flashMessenger()->addMessage($this->translate('site_required_to_begin', 'sites'));
            return $this->redirect()->toRoute('sites/add');
        }
        
        $view = array(
            'section' => 'view_sites',
            'active_sidebar' => 'manage_sites',
            'sites' => $sites_data,
            'order' => $order,
            'order_dir' => $order_dir,
            'limit' => $limit,
            'page' => $page,
            'total_pages' => $users->total_pages,
            'total_results' => $users->total_results
        );
        
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
                $site_id = $id = $site->addSite($formData->toArray(), $hash);
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

