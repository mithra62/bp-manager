<?php

namespace Sites\Controller;

class IndexController extends AbstractSitesController
{

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
        return $view;
    }
    
    public function addAction()
    {
        if (! $this->perm->check($this->identity, 'add_sites')) {
            return $this->redirect()->toRoute('sites');
        }
        
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $site_form = $this->getServiceLocator()->get('Sites\Form\SiteForm');
        
        $view['form'] = $site_form;
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $formData = $request->getPost();
            $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
            $inputFilter = $site->getInputFilter($translate);
            $site_form->setInputFilter($inputFilter);
            $site_form->setData($request->getPost());
            if ($site_form->isValid($formData)) {
                $user_id = $id = $site->addCpUser($formData->toArray(), $hash, $this->getServiceLocator()->get('Application\Model\Mail'));
                if ($user_id) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('user_added', 'app'));
                    return $this->redirect()->toRoute('manage_users/view', array(
                        'user_id' => $id
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
        $view['active_sidebar'] = 'manage_users';
        return $view;
    }


}

