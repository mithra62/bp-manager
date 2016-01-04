<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/ProjectsController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Projects Controller
 *
 * Routes the Project requests
 *
 * @package 	Projects
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/ProjectsController.php
*/
class ProjectsController extends AbstractPmController
{

	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        parent::check_permission('view_projects');
        //$this->layout()->setVariable('layout_style', 'single');
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
		$company_id = $this->params()->fromRoute('company_id');
		if($company_id)
		{
			$company = $this->getServiceLocator()->get('PM\Model\Companies'); 
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				$company_id = $company_data = FALSE;
			}
		}
		
	    $projects = $this->getServiceLocator()->get('PM\Model\Projects'); 
		if($company_id)
		{
       		$this->layout()->setVariable('active_sub', 'projects');
       		$this->layout()->setVariable('active_nav', 'companies');
       		$this->layout()->setVariable('sub_menu', 'company');
        	$view['company_id'] = $company_id;
			$view['projects'] = $projects->getProjectsByCompanyId($company_id);
        	$view['company_data'] = $company_data;
        	$view['project_filter'] = '';
			
		}
		else
		{
			if($this->perm->check($this->identity, 'manage_projects'))
	        {
	        	$view['projects'] = $projects->getAllProjects(FALSE);
	        }
	        else
	        {
				$user = $this->getServiceLocator()->get('Application\Model\Users');
				$view['projects'] = $user->getAssignedProjects($this->identity);	    		
	        }
		}
		
		return $view;
	}
	
	/**
	 * Project View Page
	 * @return void
	 */
	public function viewAction()
	{	
		$id = $this->params()->fromRoute('project_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('projects');
		}
		
		$view = array();
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$view['project'] = $project->getProjectById($id);
		if(!$view['project'])
		{
			return $this->redirect()->toRoute('projects');	
		}
		
		$proj_team = $project->getProjectTeamMembers($id);
		$on_team = $project->isUserOnProjectTeam($this->identity, $id, $proj_team);
		if(!$on_team && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('projects');				
		}
	
		$view['proj_team'] = $proj_team;
		$view['user_is_on_team'] = $on_team;
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$times = $this->getServiceLocator()->get('PM\Model\Times');

		if($this->perm->check($this->identity, 'view_tasks'))
		{		
			$view['tasks'] = $task->getTasksByProjectId($id, null, array('status' => 6));
		}

		if($this->perm->check($this->identity, 'view_files'))
		{		
			$view['files'] = $file->getFilesByProjectId($id);
		}
		
		if($this->perm->check($this->identity, 'view_time'))
		{	
			$not = array('bill_status' => 'paid');
			$view['times'] = $times->getTimesByProjectId($id, null, $not);
			$view['hours'] = $times->getTotalTimesByProjectId($id);
			$view['estimated_time'] = $task->getProjectEstimatedTime($id);
		}		

		$bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$view['bookmarks'] = $bookmarks->getBookmarksByProjectId($id);	

		$notes = $this->getServiceLocator()->get('PM\Model\Notes');
		$view['notes'] = $notes->getNotesByProjectId($id);	

		$this->layout()->setVariable('active_sub', $view['project']['status']);
		$view['identity'] = $this->identity;
		$view['layout_style'] = 'single';
		$this->layout()->setVariable('layout_style', 'single');
		$view['id'] = $id;
		return $view;
	}
	
	/**
	 * Project Edit Page
	 * @return void
	 */
	public function editAction()
	{
        		
		$id = $this->params()->fromRoute('project_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('projects');
		}		
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$form = $this->getServiceLocator()->get('PM\Form\ProjectForm');
		
		$project_data = $project->getProjectById($id);
		if (!$project_data) 
		{
			return $this->redirect()->toRoute('projects');
		}
		
		if(!$this->perm->check($this->identity, 'manage_projects'))
        {
        	return $this->redirect()->toRoute('projects/view', array('project_id'=>$id));
        }		
	
        $view['id'] = $id;
        
        if($project_data['start_date'] == '0000-00-00')
        {
        	$project_data['start_date'] = '';
        }
        
        if($project_data['end_date'] == '0000-00-00')
        {
        	$project_data['end_date'] = '';
        }
        
        $request = $this->getRequest();
        $form->setData($project_data);
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($project->getInputFilter());  
            $form->setData($request->getPost());
            if ($form->isValid($formData)) 
            {
            	$file = $this->getServiceLocator()->get('PM\Model\Files');
            	if($project->updateProject($formData->toArray(), $id, $file))
	            {
					$this->flashMessenger()->addMessage($this->translate('project_updated', 'pm')); 
					return $this->redirect()->toRoute('projects/view', array('project_id' => $id));      		
            	} 

            	$view['errors'] = array($this->translate('cant_update_project', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
            	$form->setData($formData);
            } 
            else 
            {
            	$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
                $form->setData($formData);
            }
	    }
	    
	    $view['form'] = $form;
	    $view['project_data'] = $project_data;
        $view['layout_style'] = 'right';
        $view['sidebar'] = 'dashboard';	
        $this->layout()->setVariable('layout_style', 'left');
		$this->layout()->setVariable('active_sub', $project_data['status']);
        return $view;
	}
	
	/**
	 * Project Add Page
	 * @return void
	 */
	public function addAction()
	{
		$company_id = $this->params()->fromRoute('company_id');
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$form = $this->getServiceLocator()->get('PM\Form\ProjectForm');
		
		$form->setData(
			array(
				'status' => $this->settings['default_project_status'],
				'type' => $this->settings['default_project_type'],
				'priority' => $this->settings['default_project_priority'],
			)
		);
        
        if($company_id)
        {
        	$form->setData(array('company_id' => $company_id));
        }        
		
        $request = $this->getRequest();
		if ($request->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($project->getInputFilter());
    		$form->setData($request->getPost());
    		    		
			if ($form->isValid($formData)) 
			{
				$formData['creator'] = $this->identity;
				$id = $project->addProject($formData->toArray());
				if($id)
				{
					$project->addProjectTeamMember($this->identity, $id);
					if(is_numeric($formData['company_id']))
					{
						$company = $this->getServiceLocator()->get('PM\Model\Companies');
						$company->updateCompanyProjectCount($formData['company_id']);
					}
					
					$this->flashMessenger()->addMessage($this->translate('project_added', 'pm'));
			    	return $this->redirect()->toRoute('projects/view', array('project_id' => $id));
				}
			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
			}
		 }
		
        $this->layout()->setVariable('layout_style', 'left');
        $view['sidebar'] = 'dashboard';
		$view['form'] = $form;
		return $view;
	}
	
	public function removeAction()
	{
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('project_id');
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('projects');
    	}
    	
    	$project_data = $project->getProjectById($id);
    	$view['project'] = $project_data;
    	if(!$view['project'])
    	{
			return $this->redirect()->toRoute('projects');
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
					return $this->redirect()->toRoute('projects/view', array('project_id' => $id));
				}
				
	    	   	if($project->removeProject($id))
	    		{	
	    			$project->removeProjectTeam($id);
					$this->flashMessenger()->addMessage($this->translate('project_removed', 'pm'));
					$this->redirect()->toRoute('projects');
	    		}
			}
    	}
    	
    	$view['task_count'] = $project->getTaskCount($id);
    	$view['file_count'] = $project->getFileCount($id);
		$view['id'] = $id;  
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
	
	/**
	 * The Manage Team Page
	 * @return void
	 */
	public function manageTeamAction()
	{
		
		$id = $this->params()->fromRoute('project_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('projects');
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$project_data = $project->getProjectById($id);
		if(!$project_data)
		{
			return $this->redirect()->toRoute('projects');
		}
		
		$proj_team = $project->getProjectTeamMemberIds($id);
		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			$errors = FALSE;
			if(array_key_exists('proj_member', $formData))
			{
				foreach($formData['proj_member'] AS $key => $value) //add users to the team
				{
					if(!in_array($key, $proj_team)) //user is not on the team yet; add them
					{
						$project->addProjectTeamMember($key, $id);
					}
				}
			}
			
			if(array_key_exists('proj_member', $formData))
			{
				foreach($proj_team AS $removed)
				{	
					if(!array_key_exists($removed, $formData['proj_member']))
					{	
						$project->removeProjectTeamMember($removed, $id);
					}
				}
			}
			
			if(!$errors)
			{
		        $this->flashMessenger()->addMessage($this->translate('project_team_modified', 'pm'));
		        return $this->redirect()->toRoute('projects/view', array('project_id' => $id));
			}
		}
		
		$view['id'] = $id;
		$view['project'] = $project_data;

		$view['proj_team'] = $proj_team;
		$users = $this->getServiceLocator()->get('PM\Model\Users');
		$view['users'] = $users->getAllUsers('d');
		
		return $view;
	}
}