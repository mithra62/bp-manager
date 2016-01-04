<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/TasksController.php
*/

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * Api - Tasks Controller
 *
 * Tasks REST API Controller
 *
 * @package 	Tasks\Rest
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Api/src/Api/Controller/TasksController.php
 */
class TasksController extends AbstractRestfulJsonController
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
	 * @see \Api\Controller\AbstractRestfulJsonController::getList()
	 */
	public function getList()
	{
		$project_id = $this->getRequest()->getQuery('project_id', false);
		$order = $this->getRequest()->getQuery('order', false);
		$order_dir = $this->getRequest()->getQuery('order_dir', false);
		$limit = $this->getRequest()->getQuery('limit', 10);
		$page = $this->getRequest()->getQuery('page', 1);
		
		if(empty($project_id))
		{
			return $this->setError(422, 'invalid_project_id');
		}

		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(404, 'not_found');
		}

		$task = $this->getServiceLocator()->get('Api\Model\Tasks');
		$tasks = $task->setLimit($limit)
					  ->setOrderDir($order_dir)
					  ->setOrder($order)
					  ->setPage($page)
					  ->getTasksByProjectId($project_id);

		if(count($tasks) == 0)
		{
			return $this->setError(404, 'not_found');
		}
		
		$tasks['data'] = $this->cleanCollectionOutput($tasks['data'], $task->taskOutputMap);
		return new JsonModel( $this->setupHalCollection($tasks, 'api-tasks', 'tasks', 'tasks/view', 'task_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::get()
	 */
	public function get($id)
	{
		$task = $this->getServiceLocator()->get('Api\Model\Tasks');
		$task_data = $task->getTaskById($id);
		if (!$task_data)
		{
			return $this->setError(404, 'not_found');
		}
	
		if(!$this->perm->check($this->identity, 'view_tasks'))
		{
			return $this->setError(404, 'not_found');
		}
		
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(404, 'not_found');
		}
		
		$task_data = $this->cleanResourceOutput($task_data, $task->taskOutputMap);
		$task_data['assignment_history'] = $this->cleanCollectionOutput($task->getTaskAssignments($id), $task->taskAssignmentMap);
		if($this->perm->check($this->identity, 'view_time'))
		{
			$times = $this->getServiceLocator()->get('PM\Model\Times');
			$task_data['hours'] = $times->getTotalTimesByTaskId($id);
		}
		
		return new JsonModel( $this->setupHalResource($task_data, 'api-tasks', array(), 'tasks/view', 'task_id') );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::create()
	 */
	public function create($data)
	{
		if(empty($data['project_id']))
		{
			return $this->setError(422, 'invalid_project_id');
		}
		
		//make sure we're dealing with a valid project
		$projects = $this->getServiceLocator()->get('Api\Model\Projects');
		$project_data = $projects->getProjectById($data['project_id']);
		if(!$project_data)
		{
			return $this->setError(422, 'invalid_project_id');
		}

		$task = $this->getServiceLocator()->get('Api\Model\Tasks');
		
		//we have to validate the data has everything we need
		$inputFilter = $task->getInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
		
		//now we can add it to the db...
		$data['creator'] = $this->identity;
		$task_id = $task->addTask($data);
		if(!$task_id)
		{
			return $this->setError(500, 'task_create_failed');
		}
		
		$this->setStatusCode(201);
		
		//and now let's pull the created task for the response
		$task_data = $task->getTaskById($task_id);
		$task_data = $this->cleanResourceOutput($task_data, $task->taskOutputMap);
		return new JsonModel( $this->setupHalResource($task_data, 'api-tasks', array(), 'tasks/view', 'task_id') );
	}  
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::delete()
	 */
	public function delete($id)
	{
		$task = $this->getServiceLocator()->get('Api\Model\Tasks');
		$task_data = $task->getTaskById($id);
		
		if(!$task_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		if(!$task->removeTask($id))
		{
			return $this->setError(500, 'task_remove_failed');
		}

		return new JsonModel( );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::update()
	 */
	public function update($id, $data)
	{
		//we have to use the PM model to avoid filtering results automatically
		$task = $this->getServiceLocator()->get('Pm\Model\Tasks');
		$task_data = $task->getTaskById($id);
		
		if (!$task_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		//and now we grab the API model
		$task = $this->getServiceLocator()->get('Api\Model\Tasks');
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$inputFilter = $task->getInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}

		$data = array_merge($task_data, $data);

		try {
			
			$task->updateTask($data, $id);
			
		} catch(Zend_Exception $e)
		{
			return $this->setError(500, 'task_update_failed');
		}		

		$task_data = $task->getTaskById($id);
		$task_data = $this->cleanResourceOutput($task_data, $task->taskOutputMap);
		return new JsonModel( $this->setupHalResource($task_data, 'api-tasks', array(), 'tasks/view', 'task_id') );
	}
}
