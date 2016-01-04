<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Projects.php
 */

namespace Api\Model;

use PM\Model\Projects as PmProjects;

/**
 * Api - Projects Model
 *
 * @package 	Projects\Rest
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Api/src/Api/Model/Projects.php
 */
class Projects extends PmProjects
{
	/**
	 * The REST output for the projects db table 
	 * @var array
	 */
	public $projectOutputMap = array(
		'id' => 'id',
		'name' => 'name',
		'description' => 'description',
		'company_name' => 'company_name',
		'company_id' => 'company_id',
		'description' => 'description',
		'type' => 'type_id',
		'priority' => 'priority_id',
		'status' => 'status_id',
		'hours_worked' => 'hours_worked',
		'task_count' => 'total_tasks'
	);
	
	/**
	 * The REST output for the project teams db table 
	 * @var array
	 */
	public $projectTeamOutputMap = array(
		'project_id' => 'project_id',
		'first_name' => 'first_name',
		'last_name' => 'last_name',
		'email' => 'email',
		'user_id' => 'user_id',
		'created_date' => 'added_to_team'
	);
	
	public function getProjectsByCompanyId($id, $exclude_archive = FALSE)
	{
		$projects = parent::getProjectsByCompanyId($id, $exclude_archive);
		$total_results = $this->getTotalResults();
		if(count($projects) >= 1)
		{
			$return = array(
					'data' => $projects,
					'total_results' => (int)$total_results,
					'total' => count($projects),
					'page' => (int)$this->getPage(),
					'limit' => $this->getLimit()
			);
				
			return $return;
		}
		
	}
	
	/**
	 * Returns an array of all projects filtered by $view_type
	 * @return mixed
	 */
	public function getAllProjects($view_type = FALSE)
	{
		$projects = parent::getAllProjects($view_type);
		$total_results = $this->getTotalResults();
		if(count($projects) >= 1)
		{
			$return = array(
					'data' => $projects,
					'total_results' => (int)$total_results,
					'total' => count($projects),
					'page' => (int)$this->getPage(),
					'limit' => $this->getLimit()
			);
				
			return $return;
		}
	}	
}