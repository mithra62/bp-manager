<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/CompaniesController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Companies Controller
 *
 * Routes the company requests
 *
 * @package 	Companies
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/CompaniesController.php
 */
class CompaniesController extends AbstractPmController
{	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		parent::check_permission('view_companies');
		//$this->layout()->setVariable('layout_style', 'single');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('sub_menu', 'companies');
		$this->layout()->setVariable('active_nav', 'companies');
		$this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Companies::types());
		$this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
	
		return $e;
	}	
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{		
		//$param = $this->_getParam("view",FALSE);
		$param = $this->params()->fromRoute('company_id');
		$view['active_sub'] = $param;
		$view['company_filter'] = $param;		
		if($this->perm->check($this->identity, 'manage_companies'))
		{
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$companies = $company->getAllCompanies($view);
			$view['companies'] = $companies;
		}
		else
		{
			$user = $this->getServiceLocator()->get('PM\Model\Users'); 
			$view['companies'] = $user->getAssignedProjectCompanies($this->identity);
		}
		
		return $view;
	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('company_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('companies');	
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$view['company'] = $company->getCompanyById($id);
		if(!$view['company'])
		{
			return $this->redirect()->toRoute('companies');
		}
		
		if($this->perm->check($this->identity, 'view_projects'))
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$view['projects'] = $project->getProjectsByCompanyId($id, FALSE);
		}
		
		if($this->perm->check($this->identity, 'view_tasks'))
		{		
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$view['tasks'] = $task->getTasksByCompanyId($id);
		}

		if($this->perm->check($this->identity, 'view_files'))
		{
			$file = $this->getServiceLocator()->get('PM\Model\Files');
			$view['files'] = $file->getFilesByCompanyId($id);
		}
		
		if($this->perm->check($this->identity, 'view_company_contacts'))
		{		
			$contacts = $this->getServiceLocator()->get('PM\Model\Contacts');
			$view['contacts'] = $contacts->getContactsByCompanyId($id);
		}
		
		if($this->perm->check($this->identity, 'view_time'))
		{		
			$times = $this->getServiceLocator()->get('PM\Model\Times');
			$not = array('bill_status' => 'paid');
			$view['times'] = $times->getTimesByCompanyId($id, null, $not);
			$view['hours'] = $times->getTotalTimesByCompanyId($id);
		}
		
		if($this->perm->check($this->identity, 'view_invoices'))
		{		
			$invoices = $this->getServiceLocator()->get('PM\Model\Invoices');
			$view['invoices'] = $invoices->getInvoicesByCompanyId($id);
		}

		$bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$view['bookmarks'] = $bookmarks->getBookmarksByCompanyId($id);
		
		$notes = $this->getServiceLocator()->get('PM\Model\Notes');

		$this->layout()->setVariable('active_sub', $view['company']['type']);
		$view['notes'] = $notes->getNotesByCompanyId($id);
		$view['sub_menu'] = 'company';
		$view['layout_style'] = 'single';
		$view['active_sub'] = $view['company']['type'];
		$view['id'] = $view['company_id'] = $id;
		
		return $view;
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	return $this->redirect()->toRoute('companies');
        }
        		
		$id = $this->params()->fromRoute('company_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$form = $this->getServiceLocator()->get('PM\Form\CompanyForm');
        
		$view = array();
        $view['id'] = $id;
        
        $company_data = $company->getCompanyById($id);
        $request = $this->getRequest();
        $form->setData($company_data);        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($company->getInputFilter());  
            $form->setData($request->getPost());
            
            if ($form->isValid($formData)) 
            {        
            	if($company->updateCompany($formData->toArray(), $id))
	            {	
			    	$this->flashMessenger()->addMessage($this->translate('company_updated', 'pm'));
			    	return $this->redirect()->toRoute('companies/view', array('company_id' => $id));
					        		
            	} else {
            		$view['errors'] = array($this->translate('cant_update_company', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
            		$form->setData($formData);
            	}
                
            } else {
            	$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
                $form->setData($formData);
            }
            
	    }
	    
	    $view['company_data'] = $company_data;
	    if($company_data['type'] == '1' || $company_data['type'] == '6')
	    {
	    	//Zend_Registry::set('pm_activity_filter', array('company_id' => $id));
	    }

	    $this->layout()->setVariable('active_sub', $company_data['type']);
	    $view['form'] = $form;
        $view['sidebar'] = 'dashboard';		
		$this->layout()->setVariable('layout_style', 'left'); 
		return $view;   	
	}
	
	/**
	 * Company Add Page
	 * @return void
	 */
	public function addAction()
	{
	    if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	return $this->redirect()->toRoute('companies');
        }
        		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$form = $this->getServiceLocator()->get('PM\Form\CompanyForm');
		$defaults = array(
				'currency_code' => 'USD',
				'type' => $this->settings['default_company_type'],
				'client_language' => $this->settings['default_company_client_language'],
				'currency_code' => $this->settings['default_company_currency_code'],
				'default_hourly_rate' => $this->settings['default_company_hourly_rate']
		);
		$form->setData($defaults);
		$request = $this->getRequest();
		if ($request->isPost()) 
		{
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($company->getInputFilter());  
            $form->setData($request->getPost());
            
			if ($form->isValid($formData)) 
			{
				
				$formData['owner'] = $this->identity;
				$company_id = $company->addCompany($formData->toArray());
				if($company_id)
				{
			    	$this->flashMessenger()->addMessage($this->translate('company_added', 'pm'));
					return $this->redirect()->toRoute('companies/view', array('company_id' => $company_id));
				} 
				else 
				{	
					$view['errors'] = array($this->translate('something_went_wrong', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
				}
			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
			}

		 }
		
        $view['layout_style'] = 'right';
        $view['sidebar'] = 'dashboard';
		$view['form'] = $form;
		$this->layout()->setVariable('layout_style', 'left');
		return $view;
	}
	
	public function removeAction()
	{
		if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	return $this->redirect()->toRoute('companies');
        }
        
		$companies = $this->getServiceLocator()->get('PM\Model\Companies');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		
		$id = $this->params()->fromRoute('company_id');
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('companies');
    	}
    	
    	$view = array();
    	$view['company_data'] = $companies->getCompanyById($id);
    	if(!$view['company_data'])
    	{
			return $this->redirect()->toRoute('companies');
    	}

	    $request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid())
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('companies/view', array('company_id' => $id));
				}
				
	    	   	if($companies->removeCompany($id))
	    		{	
					$this->flashMessenger()->addMessage($this->translate('company_removed', 'pm'));
					return $this->redirect()->toRoute('companies');
	    		} 
			}
    	}

    	$this->layout()->setVariable('active_sub', $view['company_data']['type']);
    	$view['project_count'] = $companies->getProjectCount($id);
    	$view['task_count'] = $companies->getTaskCount($id);
    	$view['file_count'] = $companies->getFileCount($id);
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
	
	public function mapAction()
	{
		if(!$this->perm->check($this->identity, 'view_companies'))
		{
			return $this->redirect()->toRoute('pm');			
		}	
				
		$id = $this->params()->fromRoute('company_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$company_data = $company->getCompanyById($id);
		if(!$company_data)
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$view['company'] = $company_data;
		return $this->ajaxOutput($view);
	}
}