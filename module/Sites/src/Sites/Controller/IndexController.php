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
        if(!$sites_data)
        {
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
        
    }


}

