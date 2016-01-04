<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/ProjectsController.php
 */

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * Api - Projects Controller
 *
 * Projects REST API Controller
 *
 * @package 	Projects\Rest
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Api/src/Api/Controller/ProjectsController.php
 */
class ProjectsController extends AbstractRestfulJsonController
{
	/**
	 * Maps the available HTTP verbs we support for groups of data
	 * @var array
	 */
	protected $collectionOptions = array(
		'GET', 'POST', 'OPTIONS'
	);
	
	/**
	 * Maps the available HTTP verbs for single items
	 * @var array
	 */
	protected $resourceOptions = array(
		'GET', 'POST', 'DELETE', 'PUT', 'OPTIONS'
	);
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::onDispatch()
	 * @ignore
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		if(!parent::check_permission('view_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
	
		return $e;
	}
		
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::getList()
	 */
	public function getList()
	{
		$company_id = $this->getRequest()->getQuery('company_id', false);
		$order = $this->getRequest()->getQuery('order', false);
		$order_dir = $this->getRequest()->getQuery('order_dir', false);
		$limit = $this->getRequest()->getQuery('limit', 10);
		$page = $this->getRequest()->getQuery('page', 1);
		
		if($company_id)
		{
			$company = $this->getServiceLocator()->get('Api\Model\Companies');
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				$company_id = $company_data = FALSE;
			}
		}		

		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		if($company_id)
		{
			$project_data = $project->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getProjectsByCompanyId($company_id, FALSE);
				
		}
		else
		{
			if($this->perm->check($this->identity, 'manage_projects'))
			{
				$project_data = $project->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAllProjects(FALSE);
			}
			else
			{
				$user = $this->getServiceLocator()->get('Api\Model\Users');
				$project_data = $user->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAssignedProjects($this->identity);
			}
		}
		
		if(!empty($company_data))
		{
			$project_data = array_merge($company_data, $project_data);
		}
		
		if(empty($project_data['data']))
		{
			return $this->setError(404, 'not_found');
		}
		
		$project_data['data'] = $this->cleanCollectionOutput($project_data['data'], $project->projectOutputMap);
		return new JsonModel( $this->setupHalCollection($project_data, 'api-projects', 'projects', 'projects/view', 'project_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::get()
	 */
	public function get($id)
	{
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		$project_data = $project->getProjectById($id);
		if(!$project_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		$project_data = $this->cleanResourceOutput($project_data, $project->projectOutputMap);
		
		$proj_team = $this->cleanCollectionOutput($project->getProjectTeamMembers($id), $project->projectTeamOutputMap);
		$on_team = $project->isUserOnProjectTeam($this->identity, $id, $proj_team);
		if(!$on_team && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(404, 'not_found');
		}
		
		$embeds = array();
		$embeds['proj_team'] = $this->setupCollectionMeta($proj_team, 'api-users', 'users/view', 'user_id'); 
		return new JsonModel( $this->setupHalResource($project_data, 'api-projects', $embeds, 'projects/view', 'project_id') );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::create()
	 */
	public function create($data)
	{
		if(!$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
				
		if(empty($data['company_id']))
		{
			return $this->setError(422, 'invalid_company_id');
		}

		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		$project = $this->getServiceLocator()->get('Api\Model\Projects');

		//we have to validate the data has everything we need
		$inputFilter = $project->getInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
		
		$defaults = array(
			'status' => $this->settings['default_project_status'],
			'type' => $this->settings['default_project_type'],
			'priority' => $this->settings['default_project_priority'],
		);
		
		$data = array_merge($defaults, $data);
		$data['creator'] = $this->identity;
		$project_id = $project->addProject($data);
		if(!$project_id)
		{
			return $this->setError(500, 'project_create_failed');
		}

		$project->addProjectTeamMember($this->identity, $project_id);
		if(is_numeric($data['company_id']))
		{
			$company = $this->getServiceLocator()->get('Api\Model\Companies');
			$company->updateCompanyProjectCount($data['company_id']);
		}

		$this->setStatusCode(201);
		$project_data = $project->getProjectById($project_id);
		$project_data = $this->cleanResourceOutput($project_data, $project->projectOutputMap);
		$proj_team = $this->cleanCollectionOutput($project->getProjectTeamMembers($project_id), $project->projectTeamOutputMap);

		$embeds = array();
		$embeds['proj_team'] = $this->setupCollectionMeta($proj_team, 'api-users', 'users/view', 'user_id');
		
		$project_data['project_id'] = $project_id;
		return new JsonModel( $this->setupHalResource($project_data, 'api-projects', $embeds, 'projects/view', 'project_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::delete()
	 */
	public function delete($id)
	{
		if(!$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
				
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		$project_data = $project->getProjectById($id);
		if(!$project_data)
		{
			return $this->setError(404, 'not_found');
		}
	
		if(!$project->isUserOnProjectTeam($this->identity, $id) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		if(!$project->removeProject($id))
		{
			return $this->setError(500, 'project_remove_failed');
		}
	
		return new JsonModel( );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::update()
	 */
	public function update($id, $data)
	{
		if(!$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
				
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		$project_data = $project->getProjectById($id);
	
		if (!$project_data)
		{
			return $this->setError(404, 'not_found');
		}
	
		if(!$project->isUserOnProjectTeam($this->identity, $id) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$inputFilter = $project->getInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
	
		$data = array_merge($project_data, $data);
	
		try {
				
			$project->updateProject($data, $id);
				
		} catch(Zend_Exception $e)
		{
			return $this->setError(500, 'project_update_failed');
		}
	
		$project_data = $project->getProjectById($id);
		$project_data = $this->cleanResourceOutput($project_data, $project->projectOutputMap);
		$proj_team = $this->cleanCollectionOutput($project->getProjectTeamMembers($id), $project->projectTeamOutputMap);

		$embeds = array();
		$embeds['proj_team'] = $this->setupCollectionMeta($proj_team, 'api-users', 'users/view', 'user_id');
		
		$project_data['project_id'] = $id;
		return new JsonModel( $this->setupHalResource($project_data, 'api-projects', $embeds, 'projects/view', 'project_id') );
	}	
}
