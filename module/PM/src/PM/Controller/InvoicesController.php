<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/InvoicesController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Invoices Controller
 *
 * Routes the invoices requests
 *
 * @package 	Companies\Invoices
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/InvoicesController.php
 */
class InvoicesController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        parent::check_permission('view_company_contacts');
		$this->layout()->setVariable('sub_menu', 'companies');
		$this->layout()->setVariable('active_nav', 'companies');
		$this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Companies::types());        
	}
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
	public function indexAction()
	{
		
	    $contacts = $this->getServiceLocator()->get('PM\Model\Contacts');
		//$view = $this->_getParam("view",FALSE);
		$view['company'] = FALSE;
		$company_id = $this->params()->fromRoute('company_id');
		if($company_id)
		{
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$view['company'] = $company->getCompanyById($company_id);
			if(!$view['company'])
			{
				$company_id = FALSE;
			}
		}
		
		if($company_id) {
			$view['contacts'] = $contacts->getContactsByCompanyId($company_id);
		} else {
			$view['contacts'] = $contacts->getAllContacts($view);
		}
		
		return $view;
	}
	
	/**
	 * Invoice View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('invoice_id');
		if (!$id) {
			return $this->redirect()->toRoute('companies');
		}
		
		$invoice = $this->getServiceLocator()->get('PM\Model\Invoices');
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$view['invoice_data'] = $invoice->getInvoiceById($id);
		$view['company_data'] = $company->getCompanyById($view['invoice_data']['company_id']);
		if(!$view['invoice_data'] || !$view['company_data']) {
			return $this->redirect()->toRoute('companies');
		}
		
		$view['id'] = $id;
		return $view;
	}
	
	/**
	 * Invoice Edit Page
	 * @return void
	 */
	public function editAction()
	{
	    if(!$this->perm->check($this->identity, 'manage_invoices')) {
        	return $this->redirect()->toRoute('companies');
        }
        		
		$id = $this->params()->fromRoute('invoice_id');
		if (!$id) {
			return $this->redirect()->toRoute('companies');
		}
		
		$invoice = $this->getServiceLocator()->get('PM\Model\Invoices');
		$invoice_data = $invoice->getInvoiceById($id);
		if(!$invoice_data)
		{
			return $this->redirect()->toRoute('companies');
		}

		$form = $this->getServiceLocator()->get('PM\Form\InvoiceForm');
        $form->setData($invoice->getInvoiceById($id));	
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($invoice->getInputFilter());  
            $form->setData($request->getPost());
            if ($form->isValid($formData)) 
            {
            	if($invoice->updateInvoice($formData->toArray(), $id))
	            {	
			    	$this->flashMessenger()->addMessage($this->translate('invoice_updated', 'pm')); 
					return $this->redirect()->toRoute('invoices/view', array('invoices_id' => $id));
					        		
            	} 
            	else 
            	{
            		$view['errors'] = array($this->translate('cant_update_invoice', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
            		$form->setData($formData);
            	}
                
            } 
            else 
            {
            	$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
                $form->setData($formData);
            }
            
	    }
	    
	    $view['id'] = $id;
	    $view['form'] = $form;	    
	    
	    $view['invoice_data'] = $invoice_data;
		$this->layout()->setVariable('layout_style', 'right'); 
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Invoice Add Page
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <\Zend\View\Model\ViewModel, boolean, array>
	 */
	public function addAction()
	{
		if(!$this->perm->check($this->identity, 'manage_invoices'))
        {
        	return $this->redirect()->toRoute('invoices');
        }
        		
		$company_id = $this->params()->fromRoute('company_id');
		if(!$company_id)
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$company_data = $company->getCompanyById($company_id);
		if(!$company_data)
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$invoice = $this->getServiceLocator()->get('PM\Model\Invoices');
		$time = $this->getServiceLocator()->get('PM\Model\Times');
		$form = $this->getServiceLocator()->get('PM\Form\InvoiceForm');
		
		$defaults = array(
			'date' => date('Y-m-d'),
			'invoice_number' => $invoice->getNextInvoiceNumber()
		);
		$form->setData($defaults);
        $request = $this->getRequest();
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($invoice->getInputFilter());
    		$form->setData($request->getPost());
			if ($form->isValid($formData)) {
				$formData['creator'] = $this->identity;
				$data = $formData->toArray();
				$line_items = $invoice->lineItem->parseItems($data);
				$invoice_id = $invoice->addInvoice($company_id, $data, $line_items);
				if($invoice_id){
			    	$this->flashMessenger()->addMessage($this->translate('invoice_added', 'pm'));
					return $this->redirect()->toRoute('invoices/view', array('invoice_id' => $invoice_id));
				} else {	
					$view['errors'] = array($this->translate('something_went_wrong', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
				}
				
			} else {
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
			}

		}
		$view['addAction'] = TRUE;
		$view['company_data'] = $company_data;

		$this->layout()->setVariable('active_sub', $company_data['type']);
		$this->layout()->setVariable('layout_style', 'left');
		$view['form'] = $form;
		$view['id'] = $company_id;
		$view['form_action'] = $this->getRequest()->getRequestUri();
		
		$where = array('bill_status' => '', 'billable' => '1');
		$view['time_data']  = $time->getTimesByCompanyId($company_id, $where);
		return $this->ajaxOutput($view);
	}
	
	public function removeAction()
	{
		if(!$this->perm->check($this->identity, 'manage_invoices'))
        {
        	return $this->redirect()->toRoute('companies');
        }
        		
		$invoice = $this->getServiceLocator()->get('PM\Model\Invoices');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('invoice_id');
		
		$view['invoice_data'] = $invoice->getInvoiceById($id);
		if(!$view['invoice_data'])
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
					return $this->redirect()->toRoute('invoices/view', array('invoice_id' => $id));
				}
				
	    	   	if($invoice->removeInvoice($id))
	    		{	
					$this->flashMessenger()->addMessage($this->translate('invoice_removed', 'pm'));
					return $this->redirect()->toRoute('companies/view', array('company_id' => $view['invoice_data']['company_id']));
	    		}
			}
    	}
    	
		$view['title'] = "Delete Invoice: ". $this->view->invoice_data['invoice_number'];
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
}