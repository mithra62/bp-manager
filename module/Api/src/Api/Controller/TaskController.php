<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/TaskController.php
*/

namespace Api\Controller;

use Api\Controller\TasksController;
use Zend_Exception;

/**
 * Api - Task Controller
 *
 * Tasks REST API Controller
 *
 * @package 	Tasks\Rest
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Api/src/Api/Controller/TaskController.php
 */
class TaskController extends TasksController
{
	public function updateProgressAction()
	{
		if (!$this->getRequest()->isPost())
		{
			return $this->methodNotAllowed();
		}
		
		$id = $this->params()->fromRoute('task_id');
		if (!$id)
		{
			return $this->setError(404, 'not_found');
		}
		
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task_data = $task->getTaskById($id);
		if (!$task_data)
		{
			return $this->setError(404, 'not_found');
		}

		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(404, 'not_found');
		}		
	
		$progress = $this->getRequest()->getPost('progress');
		if (!$progress || !is_numeric($progress) || $progress > 100 || $progress < 0)
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => array('progress' => array('Should be a number between 0 (zero) and 100'))));
		}

		$task->updateProgress($id, $progress);
		
		return $this->get($id);
	}
}
