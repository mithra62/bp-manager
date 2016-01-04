<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/ContactsController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Contacts Controller
 *
 * Routes the contacts requests
 *
 * @package 	Companies\Contacts
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/ContactsController.php
 */
class ContactsController extends AbstractPmController
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
     * Main Page
     * @return void
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
	 * Contact View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('contact_id');
		if (!$id) {
			return $this->redirect()->toRoute('companies');
		}
		
		$contact = $this->getServiceLocator()->get('PM\Model\Contacts');
		$view['contact'] = $contact->getContactById($id);
		if(!$view['contact'])
		{
			return $this->redirect()->toRoute('contacts');
		}
		
		$view['id'] = $id;
		
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Contact Edit Page
	 * @return void
	 */
	public function editAction()
	{
	    if(!$this->perm->check($this->identity, 'manage_company_contacts')) {
        	return $this->redirect()->toRoute('contacts');
        }
        		
		$id = $this->params()->fromRoute('contact_id');
		if (!$id) {
			return $this->redirect()->toRoute('contacts');
		}
		
		$contact = $this->getServiceLocator()->get('PM\Model\Contacts');
		$contact_data = $contact->getContactById($id);
		if(!$contact_data)
		{
			return $this->redirect()->toRoute('contacts');
		}

		$form = $this->getServiceLocator()->get('PM\Form\ContactForm');
        $form->setData($contact->getContactById($id));	
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($contact->getInputFilter());  
            $form->setData($request->getPost());
            if ($form->isValid($formData)) 
            {
            	if($contact->updateContact($formData->toArray(), $id))
	            {	
			    	$this->flashMessenger()->addMessage($this->translate('contact_updated', 'pm'));
					return $this->redirect()->toRoute('contacts/view', array('contact_id' => $id));
					        		
            	} 
            	else 
            	{
            		$view['errors'] = array($this->translate('cant_update_contact', 'pm'));
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
	    $view['contact_data'] = $contact_data;
		$this->layout()->setVariable('layout_style', 'right');
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Contact Add Page
	 * @return void
	 */
	public function addAction()
	{

		if(!$this->perm->check($this->identity, 'manage_company_contacts'))
        {
        	return $this->redirect()->toRoute('contacts');
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
		
		$contact = $this->getServiceLocator()->get('PM\Model\Contacts');
		
		$form = $this->getServiceLocator()->get('PM\Form\ContactForm');
        $request = $this->getRequest();
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($contact->getInputFilter());
    		$form->setData($request->getPost());
    		    		
			if ($form->isValid($formData)) {
				$formData['creator'] = $this->identity;
				$contact_id = $contact->addContact($formData->toArray());
				if($contact_id){
			    	$this->flashMessenger()->addMessage($this->translate('contact_added', 'pm'));
					return $this->redirect()->toRoute('contacts/view', array('contact_id' => $contact_id));
				} else {	
					$view['errors'] = array($this->translate('something_went_wrong', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
				}
				
			} else {
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
			}

		}

		$this->layout()->setVariable('active_sub', $company_data['type']);
		$this->layout()->setVariable('layout_style', 'left');
		$view['addAction'] = TRUE;
		$view['company_data'] = $company_data;
		$view['form'] = $form;
		$view['id'] = $company_id;
		return $this->ajaxOutput($view);
	}
	
	public function removeAction()
	{
		if(!$this->perm->check($this->identity, 'manage_company_contacts'))
        {
        	return $this->redirect()->toRoute('contacts');
        }
        		
		$contacts = $this->getServiceLocator()->get('PM\Model\Contacts');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		
		$id = $this->params()->fromRoute('contact_id');
		$confirm = $this->params()->fromPost('confirm');
		$fail = $this->params()->fromPost('fail');
		
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('contacts');
    	}
    	
    	$view['contact_data'] = $contacts->getContactById($id);
    	if(!$view['contact_data'])
    	{
			return $this->redirect()->toRoute('contacts');
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
					return $this->redirect()->toRoute('contacts/view', array('contact_id' => $id));
				}
				
	    	   	if($contacts->removeContact($id))
	    		{	
					$this->flashMessenger()->addMessage($this->translate('contact_removed', 'pm'));
					return $this->redirect()->toRoute('companies/view', array('company_id' => $view['contact_data']['company_id']));
	    		}
			}
    	}
    	
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
}