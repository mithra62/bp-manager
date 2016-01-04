<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Tasks.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

/**
 * PM - Tasks Model
 *
 * @package 	Tasks
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Tasks.php
 */
class Tasks extends AbstractModel
{
    /**
     * The form validation filering
     * @var \Zend\InputFilter\InputFilter
     */
    protected $inputFilter;
        
	/**
	 * For passing to the DB
	 * @var array
	 */
	private $sql_where = array();
	
	/**
	 * For passing to the DB
	 * @var array
	 */
	private $sql_not = array();	

	/**
	 * The key to use for the cache items
	 * @var string
	 */
	public $cache_key = 'tasks';

	/**
	 * The Tasks Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL(array $data){
		return array(
    		'name' => (!empty($data['name']) ? $data['name'] : ''),
    		'parent' => (!empty($data['parent']) ? $data['parent'] : 0),
    		'milestone' => (!empty($data['milestone']) ? $data['milestone'] : 0),
    		'assigned_to' => (!empty($data['assigned_to']) ? $data['assigned_to'] : 0),
    		'project_id' => (!empty($data['project_id']) ? $data['project_id'] : 0),
    		'progress' => (!empty($data['progress']) ? $data['progress'] : 0),
    		'duration' => (!empty($data['duration']) ? $data['duration'] : 0),
    		'hours_worked' => (!empty($data['hours_worked']) ? $data['hours_worked'] : 0),
    		'start_date' => (!empty($data['start_date']) ? $data['start_date'] : null),
    		'end_date' => (!empty($data['end_date']) ? $data['end_date'] : null),
    		'status' => (!empty($data['status']) ? $data['status'] : 0),
    		'percent_complete' => (!empty($data['percent_complete']) ? $data['percent_complete'] : 0),
    		'description' => (!empty($data['description']) ? $data['description'] : ''),
    		'notify' => ($data['notify'] != '' ? $data['notify'] : '0'),
    		'priority' => (!empty($data['priority']) ? $data['priority'] : 0),
    		'type' => (!empty($data['type']) ? $data['type'] : 0),
    		'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}

	/**
	 * Returns an array for modifying the Task Assignments
	 * @param unknown $data
	 * @return multitype:\PM\Model\Zend_Db_Expr unknown
	 */
	public function getAssignmentSQL(array $data){
		return array(
			'task_id' => $data['task_id'],
			'assigned_by' => $data['assigned_by'],
			'assigned_to' => $data['assigned_to'],
			'comments' => (!empty($data['assign_comment']) ? $data['assign_comment'] : ''), 
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Sets the input filter to use
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns the InputFilter
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Returns a task for a given task $id
	 * @param int $id
	 * @param arrray $what
	 * @return array
	 */
	public function getTaskById($id, array $what = null)
	{
		$sql = $this->db->select();
		
		if(is_array($what))
		{
			$sql->from(array('t'=> 'tasks'))->columns($what);
		}
		else
		{
			$sql->from(array('t'=> 'tasks'));
		}
		
		$sql = $sql->where(array('t.id' => $id));
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = t.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.assigned_to', array('assigned_first_name' => 'first_name', 'assigned_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = p.company_id', array('company_id' => 'id', 'company_name' => 'name'), 'left');
		
		return $this->getRow($sql);
	}
	
	/**
	 * Returns the tasks based on start date paramters
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 */
	public function getTasksByStartDate($year = null, $month = null, $day = null)
	{
		$sql = $this->db->select()->from(array('t'=>'tasks'));	
		
		$where = array();
		if($year)
		{
			$where['t.start_year'] = $year;
		}
		
		if($month)
		{
			$where['t.start_month'] = $month;
		}
		
		if($day)
		{
			$where['t.start_day'] = $day;
		}
		
		$sql = $sql->where($where);
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = t.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('u' => 'users'), 'u.id = t.assigned_to', array('assigned_first_name' => 'first_name', 'assigned_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns the name and id for the tasks on the $project_id
	 * @param int $project_id
	 * @return array
	 */
	public function getTaskOptions($project_id = FALSE)
	{
		$sql = $this->db->select()->from(array('t'=>'tasks'), array('name', 'id'));
		
		$predicate = new \Zend\Db\Sql\Where();
		$sql = $sql->where($predicate->notEqualTo('status', '6'));
		if($project_id)
		{
			$sql = $sql->where(array('project_id' => $project_id));
		}
		
		$sql = $sql->order('name ASC');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns the tasks for a company
	 * @param int $id
	 * @return array
	 */
	public function getTasksByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where = array();	
		$where['company_id'] = $id;
		return $this->getTasksWhere($where, $not);
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param unknown $id
	 * @param array $where
	 * @param array $not
	 * @return array
	 */
	public function getTasksByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['project_id'] = $id;
		return $this->getTasksWhere($where, $not);
	}
	
	/**
	 * Returns all the tasks a user is related to. By default will only include tasks user is assigned to.
	 * @param int $id
	 * @param bool $inc_created
	 * @param bool $inc_owned
	 * @param bool $inc_archived
	 * @return array
	 */
	public function getTasksByUserId($id, $inc_created = FALSE, $inc_archived = FALSE, $inc_assigned_to = FALSE)
	{
		$sql = $this->db->select()->from(array('t'=> 'tasks'));
		
		if($inc_created)
		{
			if($inc_archived)
			{
				$sql->where(array('creator' => $id), 'OR');
			}
			else
			{
				$sql->where(array('creator' => $id), 'OR');
				$sql->where("status != '6'", 'OR');
			}
		}
		
		if($inc_assigned_to)
		{
			if($inc_archived)
			{
				$sql->where(array('assigned_to' => $id), 'OR');
			}
			else
			{
				$sql->orwhere("assigned_to = ? AND status != '6'", $id);
			}
		}		
		
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('u3' => 'users'), 'u3.id = t.assigned_to', array('assigned_first_name' => 'first_name', 'assigned_last_name' => 'last_name'), 'left');
		return $this->getRows($sql);
	}

	/**
	 * Abstracts retrieving tasks from the system
	 * @param array $where
	 * @param array $not
	 * @param array $orwhere
	 * @param array $ornot
	 * @return array
	 */
	private function getTasksWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->from(array('t'=> 'tasks'));
		
		if(is_array($where))
		{
			foreach($where AS $key => $value)
			{
				$sql = $sql->where(array($key => $value));
			}
		}
		
		if(is_array($not))
		{
			foreach($not AS $key => $value)
			{
				$sql = $sql->where("$key != '$value'");
			}
		}
		
		if(is_array($orwhere))
		{
			foreach($orwhere AS $key => $value)
			{
				$sql = $sql->orwhere("$key = ? ", $value);
			}
		}
		
		if(is_array($ornot))
		{
			foreach($ornot AS $key => $value)
			{
				$sql = $sql->orwhere("$key != ? ", $value);
			}
		}		
		
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.creator', array('creator_first_name' => 'first_name','creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('u3' => 'users'), 'u3.id = t.assigned_to', array('assigned_first_name' => 'first_name','assigned_last_name' => 'last_name'), 'left');
		return $this->getRows($sql);
		
	}

	
	/**
	 * Inserts a Task
	 * @param $data
	 * @return int
	 */
	public function addTask(array $data)
	{
		$ext = $this->trigger(self::EventTaskAddPre, $this, compact('data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();

		if((is_numeric($data['start_hour']) && $data['start_hour'] <= 24)
		&& (is_numeric($data['start_minute']) && $data['start_minute'] <= 60))
		{
			$data['start_date'] = $data['start_date'].' '.$data['start_hour'].':'.$data['start_minute'];
		}
		
		if((is_numeric($data['end_hour']) && $data['end_hour'] <= 24)
		&& (is_numeric($data['end_minute']) && $data['end_minute'] <= 60))
		{
			$data['end_date'] = $data['end_date'].' '.$data['end_hour'].':'.$data['end_minute'];
		}
		
		$sql = $this->getSQL($data);	
		$sql['creator'] = $data['creator'];
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$task_id = $this->insert('tasks', $sql);	

	    if($task_id && $data['assigned_to'] != 0)
	    {
	    	$data['id'] = $task_id;
	    	$this->logTaskAssignment($task_id, $data['assigned_to'], $sql['creator']);
	    }

		$ext = $this->trigger(self::EventTaskAddPost, $this, compact('data', 'task_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();	
				
		return $task_id;
	}
	
	/**
	 * Updates a Task
	 * @param array $data
	 * @param int $task_id
	 * @return Ambigous <\Zend\EventManager\mixed, NULL, mixed>|Ambigous <number, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function updateTask(array $data, $task_id)
	{	
		$ext = $this->trigger(self::EventTaskUpdatePre, $this, compact('data', 'task_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
		
		//setup some defaults and rearrange things based on input
        if((is_numeric($data['start_hour']) && $data['start_hour'] <= 24) 
		&& (is_numeric($data['start_minute']) && $data['start_minute'] <= 60))
		{	
			$data['start_date'] = $data['start_date'].' '.$data['start_hour'].':'.$data['start_minute'];
		}
		
		if((is_numeric($data['end_hour']) && $data['end_hour'] <= 24) 
		&& (is_numeric($data['end_minute']) && $data['end_minute'] <= 60))
		{
			$data['end_date'] = $data['end_date'].' '.$data['end_hour'].':'.$data['end_minute'];
		}
		
		if($data['status'] == '5' || $data['status'] == '6')
		{
			$data['progress'] = '100';
		} 
		elseif($data['progress'] == '100' && $data['status'] != '6')
		{
			$data['status'] = '5';
		}

		//check if we have to log an assignment
		$legacy = $this->getTaskById($task_id);
		if($legacy['assigned_to'] != $data['assigned_to'])
		{
			$assign_desc = (isset($data['assign_comment']) ? $data['assign_comment'] : null);
			$this->logTaskAssignment($task_id, $data['assigned_to'], $data['creator'], $assign_desc);
			if($data['assigned_to'] != 0)
			{
				//todo
				//$noti->sendTaskAssignment($formData);
			}
		}
		
		$sql = $this->getSQL($data);
		$sql['company_id'] = $legacy['company_id'];
		$return = $this->update('tasks', $sql, array('id' => $task_id));
		
		$ext = $this->trigger(self::EventTaskUpdatePost, $this, compact('data', 'task_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $return = $ext->last();	

		return $return;
	}
	
	/**
	 * Updates the company id for a given $task_id; grabs it if given $project_id
	 * @param int $task_id
	 * @param int $company_id
	 * @param int $project_id
	 * @return int
	 */
	public function updateCompanyId($task_id, $company_id)
	{
		$sql = array('company_id' => $company_id);
		return $this->update('tasks', $sql, array('id' => $task_id));
	}
	
	/**
	 * Updates the file count for a given $id
	 * @param int 	 $id
	 * @param int 	 $count
	 * @param string $col
	 * @return bool
	 */
	public function updateTaskFileCount($id, $count = 1, $col = 'file_count')
	{
		$sql = array($col => new \Zend\Db\Sql\Expression($col.'+'.$count));
		return $this->update('tasks', $sql, array('id' => $id));
	}

	/**
	 * Updates the hours_worked for a given $id
	 * @param int 	$id
	 * @param float $time
	 * @return bool
	 */
	public function updateTaskTime($id, $time)
	{
		$sql = array('hours_worked' => new \Zend\Db\Sql\Expression('hours_worked+'.$time));
		return $this->update('tasks', $sql, array('id' => $id));		
	}	
	
	/**
	 * Removes the given task from the system
	 * @param unknown $task_id
	 * @return bool
	 */
	public function removeTask($task_id)
	{
	    $data = $this->getTaskById($task_id);
		$ext = $this->trigger(self::EventTaskRemovePre, $this, compact('task_id', 'data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
		
		$remove = $this->remove('tasks', array('id' => $task_id));
		
		$ext = $this->trigger(self::EventTaskRemovePost, $this, compact('task_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $remove = $ext->last();

		return $remove;
	}

	/**
	 * Returns the total files a task has
	 * @param int $id
	 * @param int $status
	 * @return int
	 */
	public function getFileCount($id, $status = FALSE)
	{
		$sql = $this->db->select()
					->from('files')->columns( array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('task_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return $data['count'];
		}		
	}	
	
	/**
	 * Splits up the task start and end dates to their indivual parts
	 * @param array $arr
	 * @return array
	 */
	public function parseTaskDates(array $arr)
	{
		if(array_key_exists('start_date', $arr))
		{
			$temp = $arr['start_date'];
			$parts = explode(' ',$temp);
			
			if(count($parts) > 1)
			{			
				list($hours, $minute, $seconds) = explode(':', $parts['1']);
	
				$arr['start_hour'] = $hours;
				$arr['start_minute'] = $minute;
				$arr['start_date'] = $parts['0'];
			}
		}
		
		if(array_key_exists('end_date', $arr))
		{
			$temp = $arr['end_date'];
			$parts = explode(' ',$temp);
			
			if(count($parts) > 1)
			{
				list($hours, $minute, $seconds) = explode(':', $parts['1']);
	
				$arr['end_hour'] = $hours;
				$arr['end_minute'] = $minute;
				$arr['end_date'] = $parts['0'];					
			}				
		}
		
		return $arr;
	}
	
	/**
	 * Logs the assignment of the task to a particular user
	 * @param int $task_id
	 * @param int $assigned_to
	 * @param int $assigned_by
	 * @param string $assign_comment
	 * @return Ambigous <\Zend\EventManager\mixed, NULL, mixed>|Ambigous <\Base\Model\Ambigous, \Zend\Db\Adapter\Driver\mixed, NULL, \Zend\EventManager\mixed, mixed>
	 */
	public function logTaskAssignment($task_id, $assigned_to, $assigned_by, $assign_comment = null)
	{
	    $data = array(
    		'task_id' => $task_id, 
    		'assigned_to' => $assigned_to, 
    		'assigned_by' => $assigned_by, 
    		'assign_comment' => $assign_comment
	    );
	    
	    $ext = $this->trigger(self::EventTaskAssignPre, $this, $data, $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getAssignmentSQL($data);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$return = $this->insert('task_assignments', $sql);
		
		$ext = $this->trigger(self::EventTaskAssignPost, $this, $data, $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $return = $ext->last();		
		
		return $return;
	}
	
	/**
	 * Returns the assignment history for a given task
	 * @param int $id
	 * @return array
	 */
	public function getTaskAssignments($id)
	{
		$sql = $this->db->select()->from(array('ta'=> 'task_assignments'));
		$sql = $sql->where(array('task_id' => $id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = ta.assigned_to', array('to_first_name' => 'first_name', 'to_last_name' => 'last_name'));
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = ta.assigned_by', array('by_first_name' => 'first_name', 'by_last_name' => 'last_name'));
		return $this->getRows($sql);
	}
	
	/**
	 * Wrapper to mark a task "completed" along with progress value
	 * @param int $id
	 * @param int $identity
	 */
	public function markCompleted($id, $identity)
	{
		$sql = array('progress' => '100', 'status' => '5');
		$task_data = $this->getTaskById($id);
		$task_data = array_merge($task_data, $sql);
		return $this->updateTask($task_data, $id);
	}
	
	/**
	 * Updates a task's progress ONLY 
	 * @param int $id
	 * @param int $progress
	 * @return Ambigous <number, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function updateProgress($id, $progress)
	{
		$sql = array('progress' => $progress, 'last_modified' => new \Zend\Db\Sql\Expression('NOW()'));
		
		if($progress == 100)
		{
			$sql['status'] = '5';
		}
		else 
		{
			$sql['status'] = 3;
		}
		
		return $this->update('tasks', $sql, array('id' => $id));		
	}
	
	/**
	 * Calculates how long all the tasks a given project will take in hours
	 * @param int $project_id
	 * @return number
	 */
	public function getProjectEstimatedTime($project_id)
	{
		$sql = $this->db->select()
					->from(array('t' => 'tasks'), array('estimate_time' => new \Zend\Db\Sql\Expression('SUM(duration)')))
					->where(array('project_id' => $project_id))
					->where(array('t.status' => 4));
					
		$data = $this->getRow($sql);
		if($data && is_array($data))
		{
			return $data['estimate_time'];
		}
		return 0;
	}
	
	/**
	 * Sets the given tasks to $status if they haven't been touched in $days or more
	 * @param number $days
	 * @param number $status
	 * @return Ambigous <number, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function autoArchive($days = 7, $status = 6)
	{
		$sql = array('status' => $status);
		$date = mktime(0, 0, 0, date("m"), date("d")-$days, date("Y"));
		$date = date('Y-m-d H:i:s', $date);
		
		$where = array();
		//$where[] = $task->getAdapter()->quoteInto('status = ?', 5);
		//$where[] = $task->getAdapter()->quoteInto('last_modified < ?', $date);
		
		echo 'fail';
		exit;
		$where = array('status', );
		return $this->update('tasks', $sql, $where);
	}
}