<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/TasksController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Tasks Controller
 *
 * Routes the Tasks requests
 *
 * @package 	Tasks
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Controller/TasksController.php
*/
class TasksController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch($e);
        parent::check_permission('view_tasks');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('sub_menu', 'projects');
        $this->layout()->setVariable('active_nav', 'projects');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		return $e;      
	}
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{
		$project_id = $this->params()->fromRoute('project_id');
		if(!$project_id)
		{
		    return $this->redirect()->toRoute('pm');
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('pm');			
		}
		
		$project_data = $project->getProjectById($project_id);
		if(!$project_data)
		{
			return $this->redirect()->toRoute('projects');
		}
		
		$tasks = $this->getServiceLocator()->get('PM\Model\Tasks');
		//$this->view->active_sub = $view;
	    $view['tasks'] = $tasks->getTasksByProjectId($project_id);
	    $view['project_data'] = $project_data;	

	    return $view;

	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('task_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('tasks');
		}
		
		$view = array();
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task_data = $task->getTaskById($id);
		if($task_data['assigned_to'] == $this->identity)
		{
			$view['assigned_to'] = TRUE;
		}
		
		$view['task'] = $task_data;
		if (!$task_data) 
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if(!$this->perm->check($this->identity, 'view_tasks'))
		{
			return $this->redirect()->toRoute('projects/view', array('project_id' => $task_data['project_id']));
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('pm');				
		}		
		
		$view['project_data'] = $project->getProjectById($task_data['project_id']);
		$view['assignment_history'] = $task->getTaskAssignments($id);
		if($this->perm->check($this->identity, 'view_files'))
		{
			$file = $this->getServiceLocator()->get('PM\Model\Files');
			$view['files'] = $file->getFilesByTaskId($id);
		}

		if($this->perm->check($this->identity, 'view_time'))
		{
			$times = $this->getServiceLocator()->get('PM\Model\Times');
			$view['times'] = $times->getTimesByTaskId($id);
			$view['hours'] = $times->getTotalTimesByTaskId($id);
		}
		
		$bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$view['bookmarks'] = $bookmarks->getBookmarksByTaskId($id);	

		$notes = $this->getServiceLocator()->get('PM\Model\Notes');
		$view['notes'] = $notes->getNotesByTaskId($id);
		$view['id'] = $id;

		$this->layout()->setVariable('active_sub', $view['project_data']['status']);
		return $view;
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('task_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('pm');
		}
		
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task_data = $task->getTaskById($id);
		if (!$task_data) 
		{
			return $this->redirect()->toRoute('pm');
		}

		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
		}
				
        $view['id'] = $id;
        $view['project_data'] = $project->getProjectById($task_data['project_id']);
        
        $task_data = $task->parseTaskDates($task_data);
        
		$form = $this->getServiceLocator()->get('PM\Form\TaskForm');
		$form->setup($task_data['project_id']);
	    if($task_data['start_date'] == '0000-00-00')
        {
        	$task_data['start_date'] = '';
        }
        
        if($task_data['end_date'] == '0000-00-00')
        {
        	$task_data['end_date'] = '';
        }
        
        $form->setData($task_data);	
        
        $view['form'] = $form;
        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $formData['project_id'] = $task_data['project_id'];
            $formData['project_name'] = $task_data['project_name'];
            
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($task->getInputFilter());
            $form->setData($formData);
                        
            if ($form->isValid($formData)) 
            {
                $formData['creator'] = $this->identity;
            	if($task->updateTask($formData->toArray(), $id))
	            {	
	            	//$task->updateCompanyId($id, FALSE, $formData['project_id']);	            	
			    	$this->flashMessenger()->addMessage($this->translate('task_updated', 'pm'));
					return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
            	} 
            	else 
            	{
            		$view['errors'] = array($this->translate('cant_update_task', 'pm'));
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
	    
	    $view['task_data'] = $task_data;
        $this->layout()->setVariable('layout_style', 'left');
		return $view;
	}
	
	/**
	 * Task Add Page
	 * @return void
	 */
	public function addAction()
	{
		
		$project = $this->params()->fromRoute('project_id');
		if(!$project)
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if($project)
		{
			$projects = $this->getServiceLocator()->get('PM\Model\Projects');		
			$view['project_data'] = $projects->getProjectById($project);
			if(!$view['project_data'])
			{
				return $this->redirect()->toRoute('pm');			
			}
		}
				
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$form = $this->getServiceLocator()->get('PM\Form\TaskForm');
		$form->setup($project);
		$form->setData(
			array(
				'status' => $this->settings['default_task_status'],
				'type' => $this->settings['default_task_type'],
				'priority' => $this->settings['default_task_priority'],
			)
		);
		
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($task->getInputFilter());
    		$form->setData($formData);    		
			if ($form->isValid($formData)) 
			{
				$formData['creator'] = $this->identity;
				$task_id = $task->addTask($formData->toArray());
				if($task_id)
				{					
					$project = $this->getServiceLocator()->get('PM\Model\Projects');
					$project->updateProjectTaskCount($formData['project_id']);
					$project_data = $project->getProjectById($formData['project_id']);
					$task->updateCompanyId($task_id, $project_data['company_id']);
 
			    	$this->flashMessenger()->addMessage($this->translate('task_added', 'pm'));
					return $this->redirect()->toRoute('tasks/view', array('task_id' => $task_id));
				}
			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
			}
		 }

		$this->layout()->setVariable('active_sub', $view['project_data']['status']);
		$view['form'] = $form;
        $this->layout()->setVariable('layout_style', 'left');
		return $view;
	}
	
	public function removeAction()
	{
		
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('task_id');
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('pm');
    	}
    	
    	$task_data = $task->getTaskById($id);
    	$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
		}
			    	
    	$view['task'] = $task_data;
    	if(!$view['task'])
    	{
			return $this->redirect()->toRoute('pm');
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
					return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
				}
				
	    	   	if($task->removeTask($id))
	    		{	
					$this->flashMessenger()->addMessage($this->translate('task_removed', 'pm')); 
					return $this->redirect()->toRoute('projects/view', array('project_id' => $task_data['project_id']));
	    		}
			}
    	}
    	
    	$view['file_count'] = $task->getFileCount($id);
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}	
}