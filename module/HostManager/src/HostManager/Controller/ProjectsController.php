<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Controller/ProjectsController.php
*/

namespace HostManager\Controller;

use PM\Controller\ProjectsController AS PmProjectsController;

/**
 * HostManager - Projects Controller
 *
 * Routes the Project requests
 *
 * @package 	HostManager\Projects
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Controller/ProjectsController.php
*/
class ProjectsController extends PmProjectsController
{	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch($e);
		$this->layout('layout/pm');
		return $e;
	}
		
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\ProjectsController::manageTeamAction()
	 */
	public function manageTeamAction()
	{
		$id = $this->params()->fromRoute('project_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('projects');
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$account = $this->getServiceLocator()->get('HostManager\Model\Accounts');
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
		$users = $this->getServiceLocator()->get('HostManager\Model\Users');
		$view['users'] = $users->getAccountUsers();
		
		return $view;
	}
}