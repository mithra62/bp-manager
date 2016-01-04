<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		1.0
 * @filesource 	./module/PM/src/PM/Model/Charts.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - Charts Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Charts.php
 */
class Charts extends AbstractModel
{
	/**
	 * The cache object
	 * @var object
	 */
	public $cache;
	
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	public function getProjectGantt($id)
	{
		$project = new PM_Model_DbTable_Projects;
		$sql = $project->select()->setIntegrityCheck(false)->from(
				array('p' => $project->getTableName()), 
				array('project_id' => 'p.id', 'project_name' => 'p.name')
		);
		$sql = $sql->joinRight(array('t' => 'tasks'), 'p.id = t.project_id', array('task_name' => 't.name', 't.start_date', 't.end_date', 'task_id' => 't.id'));	
		$sql = $sql->where('p.id = ?', $id)->where('t.start_date != ?', '0000-00-00 00:00:00')->where('t.end_date != ?', '0000-00-00 00:00:00');
		return $project->getProjects($sql);
	}
	
	public function getProjectTaskDateRange($id)
	{
		$task = new PM_Model_DbTable_Tasks();
		$sql = $task->select()->setIntegrityCheck(false)->from(
				array('t' => $task->getTableName()), 
				array(
					new Zend_Db_Expr('MIN(t.start_date) AS min_date'), 
					new Zend_Db_Expr('MAX(t.end_date) AS max_date')
				)
		);
		$sql = $sql->where('t.project_id = ?', $id)->where('t.start_date != ?', '0000-00-00 00:00:00')->where('t.end_date != ?', '0000-00-00 00:00:00');
		$sql = $sql->order('t.start_date ASC');
		return $task->getTask($sql);		
	}

	public function getDateSumTimes()
	{
		$time = new PM_Model_DbTable_Times;
		$sql = $time->select()->setIntegrityCheck(false)->from(
				array('t'=>$time->getTableName()), 
				array(new Zend_Db_Expr('date_format(date,"%Y-%m") AS f_date'), new Zend_Db_Expr('SUM(hours) AS total_hours'))
		);
		$sql = $sql->group('f_date');
		return $time->getTimes($sql);		
	}

	public function getUserDateSumTimes($user, $range = 30)
	{
		$start_date = date('Y-m-d', mktime(0, 0, 0, date("m")  , (date("d")-$range), date("Y")));
		$sql = $this->db->select()->from(array('t'=>'times'))
				->columns(
						array('date' => new \Zend\Db\Sql\Expression('date'), 'total_hours' => new \Zend\Db\Sql\Expression('SUM(hours)'))
				);
		$where = $sql->where->greaterThanOrEqualTo('date', $start_date);
		$sql->where($where);
		$where = $sql->where->lessThanOrEqualTo('date', date('Y-m-d'));
		$sql->where($where)->where(array('creator ' => $user));
		$sql->group('date');
		
		return $this->getRows($sql);		
	}	
	
	public function getTaskStatus()
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()->setIntegrityCheck(false)->from(
				array('t'=>$task->getTableName()), 
				array('status', new Zend_Db_Expr('COUNT(t.id) AS status_count'))
		);
		$sql = $sql->group('status');
		return $task->getTasks($sql);		
	}
		
	public function getProjectStatus()
	{
		$project = new PM_Model_DbTable_Projects;
		$sql = $project->select()->setIntegrityCheck(false)->from(
				array('p'=>$project->getTableName()), 
				array('status', new Zend_Db_Expr('COUNT(p.id) AS status_count'))
		);
		$sql = $sql->group('status');
		return $project->getProjects($sql);		
	}
		
	public function getCompanyTypes()
	{
		$company = new PM_Model_DbTable_Companies;
		$sql = $company->select()->setIntegrityCheck(false)->from(
				array('c'=>$company->getTableName()), 
				array('type', new Zend_Db_Expr('COUNT(c.id) AS type_count'))
		);
		$sql = $sql->group('c.type')->order('type_count DESC');
		return $company->getCompanies($sql);		
	}
	
	public function getUserTimes($company = FALSE, $date = FALSE)
	{
		$user = new PM_Model_DbTable_Users;
		$sql = $user->select()->setIntegrityCheck(false)->from(
				array('u'=>$user->getTableName()), 
				array('first_name', 'last_name', 'id')
		);
		$sql = $sql->joinRight(array('t' => 'times'), 'u.id = t.creator', array(new Zend_Db_Expr('SUM(t.hours) AS hours_worked')));
		$sql = $sql->group('t.creator')->order('hours_worked DESC');
		if($company)
		{
			$sql = $sql->where('company_id = ?', $company);
		}
		
		if($date)
		{
			$sql = $sql->where(new Zend_Db_Expr('date_format(date,"%Y-%m")').' = ?', $date);
		}
		
		$data = $user->getUsers($sql);
		return $data;
	}
	
	public function getProjectsTasks($company)
	{
		$project = new PM_Model_DbTable_Projects;
		$sql = $project->select()->setIntegrityCheck(false)->from(
				array('p'=>$project->getTableName()), 
				array('name', 'id')
		);
		$sql = $sql->joinRight(array('t' => 'tasks'), 'p.id = t.project_id', array(new Zend_Db_Expr('COUNT(t.id) AS task_count')));
		$sql = $sql->group('p.id')->order('task_count DESC');
		$sql = $sql->where('p.company_id = ?', $company);
		return $project->getProjects($sql);
	}
		
	public function getCompanyProjectsToTask()
	{
		$company = new PM_Model_DbTable_Companies;
		$sql = $company->select()->setIntegrityCheck(false)->from(
				array('c'=>$company->getTableName()), 
				array('name' => 'c.name', 'id')
		);
		$sql = $sql->joinRight(array('p' => 'projects'), 'c.id = p.company_id', array(new Zend_Db_Expr('COUNT(p.id) AS project_count')));
		$sql = $sql->group('p.company_id')->order('project_count DESC');
		$data = $company->getCompanies($sql);
		$arr = array();
		foreach($data AS $client)
		{
			if(!array_key_exists('id', $client))
			{
				continue;
			}			
			$arr[$client['id']]['id'] = $client['id'];
			$arr[$client['id']]['name'] = $client['name'];
			$arr[$client['id']]['project_count'] = (int)$client['project_count'];
		}
		unset($data);
		
		$sql = $company->select()->setIntegrityCheck(false)->from(
				array('c'=>$company->getTableName()), 
				array('name' => 'c.name', 'id')
		);
		$sql = $sql->joinRight(array('p' => 'projects'), 'c.id = p.company_id', array());
		$sql = $sql->joinRight(array('t' => 'tasks'), 'p.id = t.project_id', array(new Zend_Db_Expr('COUNT(t.id) AS task_count')));
		$sql = $sql->group('c.id');	
		$data = $company->getCompanies($sql);		
		foreach($data AS $client)
		{
			if(!array_key_exists('id', $client))
			{
				continue;
			}
			$arr[$client['id']]['task_count'] = (int)$client['task_count'];
		}
		
		return $arr;
	}
}