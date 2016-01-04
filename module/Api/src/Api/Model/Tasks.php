<?php
 /**
 * mithra62 - MojiTrac
 * 
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Tasks.php
 */

namespace Api\Model;

use PM\Model\Tasks as PmTasks;

/**
 * Api - Tasks Model
 *
 * @package 	Tasks\Rest
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Api/src/Api/Model/Tasks.php
 */
class Tasks extends PmTasks
{	
	/**
	 * The REST output for the tasks db table 
	 * @var array
	 */
	public $taskOutputMap = array(
		'id' => 'id',
		'name' => 'name',
		'company_name' => 'company_name',
		'project_name' => 'project_name',
		'project_id' => 'project_id',
		'project_name' => 'project_name',
		'company_id' => 'company_id',
		'description' => 'description',
		'type' => 'type_id',
		'priority' => 'priority_id',
		'status' => 'status_id',
		'progress' => 'progress'
	);
	
	/**
	 * The REST output for the assignments db table
	 * @var array
	 */
	public $taskAssignmentMap = array(
		'id' => 'assignment_id',
		'assigned_by' => 'assigned_by_member_id',
		'assigned_to' => 'assigned_to_member_id',
		'comments' => 'comments',
		'assigned_date' => 'created_date',
		'to_first_name' => 'assigned_to_first_name',
		'to_last_name' => 'assigned_to_last_name',
		'by_first_name' => 'assigned_by_first_name',
		'by_last_name' => 'assigned_by_last_name',
	);
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Tasks::getTaskById()
	 */
	public function getTaskById($id, array $what = null)
	{
		$tasks = parent::getTaskById($id, $what);
		return $tasks;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Tasks::getTaskAssignments()
	 */
	public function getTaskAssignments($id)
	{
		$assignments = parent::getTaskAssignments($id);
		return $assignments;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Tasks::getTasksByProjectId()
	 */
	public function getTasksByProjectId($id, array $where = null, array $not = null)
	{
		$tasks = parent::getTasksByProjectId($id, $where, $not);
		$total_results = $this->getTotalResults();

		if(count($tasks) >= 1)
		{
			$return = array(
				'data' => $tasks,
				'total_results' => (int)$total_results,
				'total' => count($tasks),
				'page' => (int)$this->getPage(),
				'limit' => $this->getLimit()
			);
			
			return $return;
		}
	}
}