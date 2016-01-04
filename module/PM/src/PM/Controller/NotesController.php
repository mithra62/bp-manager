<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link			http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/NotesController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Notes Controller
 *
 * Routes the Notes requests
 *
 * @package 	Notes
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/NotesController.php
*/
class NotesController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		//$this->layout()->setVariable('layout_style', 'single');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('sub_menu', 'tasks');
		$this->layout()->setVariable('active_nav', 'bookmarks');
		$this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
		$this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');
	
		return $e;
	}	
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{
	    $note = $this->getServiceLocator()->get('PM\Model\Notes');
	    $id = $this->params()->fromRoute('id');
	    $type = $this->params()->fromRoute('type');
	    $company_id = $project_id = $task_id = false;
	     
	    if($type == 'company')
	    {
	    	$company_id = $id;
	    	$company = $this->getServiceLocator()->get('PM\Model\Companies');
	    	$company_data = $company->getCompanyById($company_id);
	    	if(!$company_data)
	    	{
	    		return $this->redirect()->toRoute('companies');
	    	}
	    		
	    	$view['company'] = $company_data;
	    	$note_data = $note->getNotesByCompanyId($company_id);
	    }
	    
	    if($type == 'project')
	    {
	    	$project_id = $id;
	    	$project = $this->getServiceLocator()->get('PM\Model\Projects');
	    	$project_data = $project->getProjectById($project_id);
	    	if(!$project_data)
	    	{
	    		return $this->redirect()->toRoute('projects');
	    	}
	    	$view['project'] = $project_data;
	    	$note_data = $note->getNotesByProjectId($project_id);
	    }
	    
	    if($type == 'task')
	    {
	    	$task_id = $id;
	    	$task = $this->getServiceLocator()->get('PM\Model\Tasks');
	    	$task_data = $task->getTaskById($task_id);
	    	if(!$task_data)
	    	{
	    		return $this->residrect()->toRoute('tasks');
	    	}
	    		
	    	$view['task'] = $task_data;
	    	$note_data = $note->getNotesByTaskId($task_id);
	    }		
    	
    	if(!$company_id && !$project_id && !$task_id)
    	{
    		$view = $this->_getParam("view",FALSE);
    		$note_data = $note->getAllNotes($view);
    	}
    	
		//$this->view->active_sub = $view;
		$view['notes'] = $note_data;
		$view['id'] = $id;
		
		return $view;
	}
	
	/**
	 * Note View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('note_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		$note = $this->getServiceLocator()->get('PM\Model\Notes');
		$note_data = $note->getNoteById($id);
		$view['note'] = $note_data;
		if (!$note_data) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		if($note_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $note_data['project_id']))
			{
	        	return $this->redirect()->toRoute('pm');				
			}
			
			$view['project'] = $project->getProjectById($note_data['project_id'], array('id', 'name'));
		}
		
		if($note_data['task_id'])
		{
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$view['task'] = $task->getTaskById($note_data['task_id'], array('id', 'name'));
		}		
		
		if($note_data['company_id'] && $note_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('pm');			
			}
			
			$company = $this->getServiceLocator()->get('PM\Model\Company');
			$view['company'] = $company->getCompanyById($note_data['company_id'], array('id', 'name'));
		}		

		$view['id'] = $id;
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Note Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('note_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		$note = $this->getServiceLocator()->get('PM\Model\Notes');
		$note_data = $note->getNoteById($id);
		if (!$note_data) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		if($note_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $note_data['project_id']))
			{
	        	return $this->redirect()->toRoute('pm');			
			}			
		}
		
		if($note_data['company_id'] && $note_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('pm');				
			}			
		}		
		
		$form = $this->getServiceLocator()->get('PM\Form\NoteForm');
        $view['id'] = $id;
        $form->setData($note_data);
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($note->getInputFilter());
            $form->setData($formData);
            if ($form->isValid($formData)) 
            {
            	$formData = $formData->toArray();
            	$formData['task'] = $note_data['task_id'];
            	$formData['company'] = $note_data['company_id'];
            	$formData['project'] = $note_data['project_id'];
            	if($note->updateNote($formData, $id))
	            {	
			    	$this->flashMessenger()->addMessage($this->translate('note_updated', 'pm'));
					return $this->redirect()->toRoute('notes/view', array('note_id' => $id));
					        		
            	} 
            	else 
            	{
            		$view['errors'] = array('Couldn\'t update note...');
					$this->layout()->setVariable('errors', $view['errors']);
            		$form->setData($formData);
            	}
                
            } 
            else 
            {
            	$view['errors'] = array('Please fix the errors below.');
				$this->layout()->setVariable('errors', $view['errors']);
                $form->setData($formData);
            }
            
	    }
	    
	    $view['form'] = $form;  
		$view['form_action'] = $this->getRequest()->getRequestUri();
		//$this->view->headTitle('Edit Note', 'PREPEND');  
        $this->layout()->setVariable('layout_style', 'right');
        $this->layout()->setVariable('sidebar', 'dashboard');

		return $this->ajaxOutput($view);
	}
	
	/**
	 * Note Add Page
	 * @return void
	 */
	public function addAction()
	{
	    $id = $this->params()->fromRoute('id');
	    $type = $this->params()->fromRoute('type');
	    $view = array();
	    $company_id = $project_id = $task_id = false;
	    		
		if($type == 'company') 
		{
			$company_id = $id;
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				return $this->redirect()->toRoute('companies');
			}
			
			$view['company'] = $company_data;
		}

		if($type == 'project') 
		{
		    $project_id = $id;
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
			    return $this->redirect()->toRoute('projects');
			}
			$view['project'] = $project_data;
		}

		if($type == 'task') 
		{
		    $task_id = $id;
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				return $this->residrect()->toRoute('tasks');
			}
			
			$view['task'] = $task_data;
		}	
    			
		$note = $this->getServiceLocator()->get('PM\Model\Notes');
		$form = $this->getServiceLocator()->get('PM\Form\NoteForm');
		if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($note->getInputFilter());
    		$form->setData($formData);    		
			if ($form->isValid($formData)) 
			{	
				if(isset($project_data))
				{
					$formData['company'] = $project_data['company_id'];
				}
				
				if(isset($task_data))
				{
					$project = $this->getServiceLocator()->get('PM\Model\Projects');
					$formData['project'] = $task_data['project_id'];
					$temp = $project->getCompanyIdById($task_data['project_id']);
					$formData['company'] = $temp['company_id'];
				}				
				
				$note_id = $note->addNote($formData->toArray(), $this->identity);
				if($note_id)
				{
			    	$this->flashMessenger()->addMessage($this->translate('note_added', 'pm'));
					return $this->redirect()->toRoute('notes/view', array('note_id' => $note_id));
				}
				
				$view['errors'] = array('Please fix the errors below.');
				$this->layout()->setVariable('errors', $view['errors']);
				
			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
				$this->layout()->setVariable('errors', $view['errors']);
			}

		 }

		$this->layout()->setVariable('layout_style', 'right');
        //$this->view->headTitle('Add Note', 'PREPEND');

		$view['form'] = $form;
		$view['form_action'] = $this->getRequest()->getRequestUri();
		return $this->ajaxOutput($view);
	}
	
	public function removeAction()
	{   		
		$notes = $this->getServiceLocator()->get('PM\Model\Notes');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('note_id');
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('pm');
    	}
    	
    	$note_data = $notes->getNoteById($id);
    	$view['note'] = $note_data;
    	if(!$view['note'])
    	{
			return $this->redirect()->toRoute('pm');
    	}
    	
    	
		if($note_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $note_data['project_id']))
			{
	        	return $this->redirect()->toRoute('pm');				
			}			
		}
		
		if($note_data['company_id'] && $note_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('pm');				
			}			
		}
		
		if($note_data['task_id'] && $note_data['task_id'] == '0') 
		{
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$task_data = $task->getTaskById($note_data['task_id']);
			if(!$task_data)
			{
				return $this->redirect()->toRoute('pm');
			}
			
			$view['task'] = $task_data;
		}
    	
	    $request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('notes/view', array('note_id' => $id));
				}
				
	    	   	if($notes->removeNote($id))
	    		{	
					$formData['task'] = $note_data['task_id'];
					$formData['company'] = $note_data['company_id'];
					$formData['project'] = $note_data['project_id'];			
					$this->flashMessenger()->addMessage($this->translate('note_removed', 'pm'));
					if($note_data['task_id'] > 0)
					{
						return $this->redirect()->toRoute('tasks/view', array('task_id' => $note_data['task_id']));
					}
					
	    			if($note_data['project_id'] > 0)
					{
						return $this->redirect()->toRoute('projects/view', array('project_id' => $note_data['project_id']));
					}
	
	    			if($note_data['company_id'] > 0)
					{
						return $this->redirect()->toRoute('companies/view', array('company_id' => $note_data['company_id']));
					}				
					
					return $this->redirect()->toRoute('notes');
					
	    		}
			} 
    	}
    	
		//$this->view->headTitle('Delete Note: '. $this->view->note['subject'], 'PREPEND');
		$view['id'] = $id;
		$view['form'] = $form;
		$view['form_action'] = $this->getRequest()->getRequestUri();
		return $this->ajaxOutput($view);
	}	
}