<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Projects.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

/**
 * PM - Project Model
 *
 * @package 	Projects
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Projects.php
 */
class Projects extends AbstractModel
{
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $inputFilter;
	
	/**
	 * The Project Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
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
	 * Creates the array for modifying the DB
	 * @param array $data
	 * @return array
	 */
	private function getSQL($data){
		$sql = array(
    		'name' => (!empty($data['name']) ? $data['name'] : ''), 
    		'company_id' => (!empty($data['company_id']) ? $data['company_id'] : '0'),
    		'start_date' => (!empty($data['start_date']) ? $data['start_date'] : null),
    		'end_date' => (!empty($data['end_date']) ? $data['end_date'] : null),
    		'actual_end_date' => (!empty($data['actual_end_date']) ? $data['actual_end_date'] : null),
    		'status' => (!empty($data['status']) ? $data['status'] : 0),
    		'percent_complete' => (!empty($data['percent_complete']) ? $data['percent_complete'] : 0),
    		'description' => (!empty($data['description']) ? $data['description'] : ''),
    		'target_budget' => (!empty($data['target_budget']) ? $data['target_budget'] : 0),
    		'actual_budget' => (!empty($data['actual_budget']) ? $data['actual_budget'] : 0),
    		'creator' => (!empty($data['creator']) ? $data['creator'] : 0),
    		'priority' => (!empty($data['priority']) ? $data['priority'] : 0),
    		'type' => (!empty($data['type']) ? $data['type'] : 0),
    		'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
		
		if(empty($sql['creator']))
			unset($sql['creator']);
		
		return $sql;
	}	
	
	/**
	 * Returns the project for a given $name
	 * @param $name
	 * @return mixed
	 */
	public function getProjectIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $this->db->getProject($sql);
	}
	
	/**
	 * Returns the $company_id for a give project $id
	 * @param int $id
	 * @return array
	 */
	public function getCompanyIdById($id)
	{
		$sql = $this->db->select()->from('projects')->columns( array('company_id'))->where(array('id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Returns a project by the $id
	 * @param int $id
	 * @return array
	 */
	public function getProjectById($id, $what = null)
	{
		$sql = $this->db->select();
		if(is_array($what))
		{
			$sql->from(array('p'=> 'projects'))->columns($what);
		}
		else
		{
			$sql->from(array('p'=> 'projects'));
		}
				
		$sql = $sql->where(array('p.id' => $id));
		
		$sql = $sql->join(array('u' => 'users'), 'u.id = p.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = p.company_id', array('company_name' => 'name'), 'left');
		return $this->getRow($sql);
	}
	
	/**
	 * Returns a project by the $id
	 * @param int $id
	 * @return array
	 */
	public function getProjectByHarvestId($harvest_id)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('p'=>$this->db->getTableName()))->where('p.harvest_id = ?', $harvest_id);
		
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = p.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		$sql = $sql->joinLeft(array('c' => 'companies'), 'c.id = p.company_id', array('name AS company_name'));
		return $this->db->getProject($sql);
	}

	/**
	 * Returns a companies projects by the $id
	 * @param $id
	 * @return array
	 */
	public function getProjectsByCompanyId($id, $exclude_archive = FALSE)
	{
		$sql = $this->db->select()->from('projects')->where(array('company_id' => $id));
		if($exclude_archive)
		{
			$sql = $sql->where(array('status' => '6'));
		}		
		return $this->getRows($sql);
	}

	/**
	 * Returns all the projects that start on a given date
	 * @param string $date
	 */
	public function getProjectsByStartDate($year = null, $month = null, $day = null)
	{
		$sql = $this->db->select()->from(array('p'=>'projects'));
		if($year !== null)
		{
			$sql = $sql->where(array('start_year' => $year));
		}
		if($month !== null)
		{
			$sql = $sql->where(array('start_month' => $month));
		}
		if($day !== null)
		{
			$sql = $sql->where(array('start_day' => $day));
		}				
		
		$sql = $sql->join(array('c' => 'companies'), 'c.id = p.company_id', array('company_name' => 'name'), 'left');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns an array of all unique artist names
	 * @return mixed
	 */
	public function getAllProjectNames()
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('name'))
								->where('status = ?', 'active');
		return $this->db->getProjects($sql);
	}
	
	/**
	 * Returns an array of all projects filtered by $view_type
	 * @return mixed
	 */
	public function getAllProjects($view_type = FALSE)
	{
		$sql = $this->db->select()->from(array('p'=> 'projects'));
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('p.status' => $view_type));
		} else {
			
			$sql = $sql->where(array('p.status != 6'));
		}
		
		$sql = $sql->join('companies', 'p.company_id = companies.id', array('company_name' => 'name'), 'left');
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns the project name and id for a given $company_id. 
	 * @param int $company_id
	 * @return array
	 */
	public function getProjectOptions($company_id = FALSE)
	{
		$sql = $this->db->select()->from(array('p'=> 'projects'))->columns(array('name', 'id'));
		if($company_id)
		{
			$sql = $sql->where(array('company_id' => $company_id));
		}
		
		$sql = $sql->order('name ASC');
		
		return $this->getRows($sql);
	}
	
	/**
	 * Returns the total tasks a company has
	 * @param int $id
	 * @param int $status
	 * @return int
	 */
	public function getTaskCount($id, $status = FALSE)
	{
		$sql = $this->db->select()
					->from('tasks')->columns(array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('project_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return $data['count'];
		}		
	}
	
	/**
	 * Returns the total files a company has
	 * @param int $id
	 * @param int $status
	 * @return int
	 */
	public function getFileCount($id, $status = FALSE)
	{
		$sql = $this->db->select()
					->from('files')->columns( array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('project_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return $data['count'];
		}		
	}

	/**
	 * Returns the company_id for a given project $id
	 * @param int $id
	 * @return int
	 */
	public function getCompanyId($id)
	{
		$sql = $this->db->select()->from('projects')->columns( array('company_id'))
						->where(array('id' => $id));
		$company = $this->getRow($sql);
		if(array_key_exists('company_id', $company))
		{
			return $company['company_id'];
		}
	}

	
	/**
	 * Inserts a Project
	 * @param $data
	 * @return mixed
	 */
	public function addProject($data, $bypass_update = FALSE)
	{
		$ext = $this->trigger(self::EventProjectAddPre, $this, compact('data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();

		$sql = $this->getSQL($data);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$data['project_id'] = $project_id = $this->insert('projects', $sql);
		
		$ext = $this->trigger(self::EventProjectAddPost, $this, compact('project_id', 'data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $project_id = $ext->last();		
			
		return $data['project_id'];
	}
	
	/**
	 * Updates a project
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateProject($data, $project_id, \PM\Model\Files $file = null)
	{
		$ext = $this->trigger(self::EventProjectUpdatePre, $this, compact('data', 'project_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();

		//ok. we have to explicitly check for company changes so we need a copy of what we're working with
		$project_data = $this->getProjectById($project_id);
		$sql = $this->getSQL($data);
		$return = $this->update('projects', $sql, array('id' => $project_id));
		
		if($return && !empty($data['company_id']) && ($project_data['company_id'] != $data['company_id']))
		{
			//we have a company change here
			$this->update('tasks', array('company_id' => $data['company_id']), array('project_id' => $project_id));
			$this->update('bookmarks', array('company_id' => $data['company_id']), array('project_id' => $project_id));
			$this->update('notes', array('company_id' => $data['company_id']), array('project_id' => $project_id));
			$this->update('times', array('company_id' => $data['company_id']), array('project_id' => $project_id));
			$this->update('activity_logs', array('company_id' => $data['company_id']), array('project_id' => $project_id));
			//files have to be handled seperately
			if($file !== null){
				$file->changeProjectCompany($project_id, $data['company_id']);
			}
		}		
		
		$ext = $this->trigger(self::EventProjectUpdatePost, $this, compact('data', 'project_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $return = $ext->last();
		
		return $return;
	}
	
	/**
	 * Updates the task count for a given $id
	 * @param int 	 $id
	 * @param int 	 $count
	 * @param string $col
	 * @return bool
	 */
	public function updateProjectTaskCount($id, $count = 1, $col = 'task_count')
	{
		$sql = array($col => new \Zend\Db\Sql\Expression($col.'+'.$count));
		return $this->update('projects', $sql, array('id' => $id));
	}
	
	/**
	 * Updates the hours_worked for a given $id
	 * @param int 	$id
	 * @param floar $time
	 * @return bool
	 */
	public function updateProjectTime($id, $time)
	{
		$sql = array('hours_worked' => new \Zend\Db\Sql\Expression('hours_worked+'.$time));
		return $this->update('projects', $sql, array('id' => $id));		
	}
	
	/**
	 * Updates the file count for a given $id
	 * @param int 	 $id
	 * @param int 	 $count
	 * @param string $col
	 * @return bool
	 */
	public function updateProjectFileCount($id, $count = 1, $col = 'task_count')
	{
		$sql = array($col => new \Zend\Db\Sql\Expression($col.'+'.$count));
		return $this->update('projects', $sql, array('id' => $id));
	}	
	
	/**
	 * Handles everything for a campaign to stop tracking a Last.fm Album Profile.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeProject($id)
	{	
		$ext = $this->trigger(self::EventProjectRemovePre, $this, compact('id'), $this->setXhooks(array('id' => $id)));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $id = $ext->last();	
		
		$company_id = $this->getCompanyId($id);
		if($this->remove('projects', array('id' => $id)))
		{
		    $success = TRUE;
		    /*
			$tasks = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$tasks->removeTasksByProject($id);
			
			$files = new PM_Model_Files(new PM_Model_DbTable_Files);
			$files->removeFilesByProject($id);

			$notes = new PM_Model_Notes;
			$notes->removeNotesByProject($id);

			$bookmarks = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
			$bookmarks->removeBookmarksByProject($id);
			
			$companies = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$companies->updateCompanyProjectCount($company_id, -1, 'active_projects');
			*/
			
			$ext = $this->trigger(self::EventProjectRemovePost, $this, compact('id'), $this->setXhooks(array('id' => $id)));
			if($ext->stopped()) return $ext->last(); elseif($ext->last()) $success = $ext->last();
						
			return $success;
		}
	}
	
	/**
	 * Removes all the projects for the given $company_id
	 * @param int $company_id
	 * @return bool
	 */
	public function removeProjectsByCompany($company_id)
	{
		return $this->db->deleteProject($company_id, 'company_id');		
	}
	
	/**
	 * Returns all the users, with info, attached to a particular team
	 * @param int $id
	 * @return array
	 */
	public function getProjectTeamMembers($project_id)
	{
		$sql = $this->db->select()->from(array('pt'=> 'project_teams'));
		$sql = $sql->where(array('project_id' => $project_id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = pt.user_id', array('first_name', 'last_name', 'email', 'job_title', 'user_id' => 'id'), 'left');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns just the $user_ids for all the users attached to team_id
	 * @param int $id
	 * @return array
	 */
	public function getProjectTeamMemberIds($id)
	{
		$sql = $this->db->select()->from(array('pt'=> 'project_teams'))->columns(array('user_id'))->where(array('project_id' => $id));
		$members = $this->getRows($sql);
		$_members = array();
		foreach($members AS $member)
		{
			$_members[] = $member['user_id'];
		}
		return $_members;
	}
	
	/**
	 * Adds a user to the projec team
	 * @param $id
	 * @param $project
	 * @return bool
	 */
	public function addProjectTeamMember($id, $project)
	{
	    $ext = $this->trigger(self::EventProjectAddTeamPre, $this, compact('id', 'project'), $this->setXhooks(array('id' => $project)));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $project = $ext->last();
	    	    
		$sql = array(
			'project_id' => $project,
			'user_id' => $id,
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()'),
			'created_date' => new \Zend\Db\Sql\Expression('NOW()')
		);
		
		$insert = $this->insert('project_teams', $sql);

		$ext = $this->trigger(self::EventProjectAddTeamPost, $this, compact('id', 'project'), $this->setXhooks(array('id' => $project)));
		if($ext->stopped()) return $ext->last();

		return $insert;
	}
	
	/**
	 * Removes a user from a project team
	 * @param int $id
	 * @param int $project
	 * @return unknown_type
	 */
	public function removeProjectTeamMember($user_id, $project_id)
	{
		$where = array('user_id' => $user_id, 'project_id' => $project_id);
		
	    $ext = $this->trigger(self::EventProjectRemoveTeamMemberPre, $this, $where, $this->setXhooks(array('id' => $project_id)));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $where = $ext->last();
	    	    
		$delete = $this->remove('project_teams', $where);
		
		$ext = $this->trigger(self::EventProjectRemoveTeamMemberPost, $this, $where, $this->setXhooks(array('id' => $project_id)));
		if($ext->stopped()) return $ext->last();

		return $delete;
	}
	
	/**
	 * Removes the entire project team from $id
	 * @param int $id
	 */
	public function removeProjectTeam($id)
	{
	    $ext = $this->trigger(self::EventProjectRemoveTeamPre, $this, compact('id'), $this->setXhooks(array('id' => $id)));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $id = $ext->last();
	    	    
		$where = array('project_id' => $id);
		$delete = $this->remove('project_teams', $where);
		
		$ext = $this->trigger(self::EventProjectRemoveTeamPost, $this, compact('id'), $this->setXhooks(array('id' => $id)));
		if($ext->stopped()) return $ext->last();

		return $delete;	
	}
	
	/**
	 * Checks if the given $user is on $project. Optionally, send the project team array along with the request (if available)
	 * @param int $user
	 * @param int $project
	 * @param array $project_teams
	 * @return bool
	 */
	public function isUserOnProjectTeam($user, $project, array $project_teams = null)
	{
		if(null === $project_teams)
		{
			$project_teams = $this->getProjectTeamMembers($project);
		}
		foreach($project_teams AS $team_member)
		{
			if($user == $team_member['user_id'])
			{
				return TRUE;
			}
		}
	}
	
	public function getByCompanyIdProjectName($name, $company_id)
	{
		$sql = $this->db->select()->from(array('p' => $this->db->getTableName()), array('id'))->where('company_id = ?', $company_id)->where('name LIKE ?', $name);
		return $this->db->getProject($sql);
	}
	
	/**
	 * Sets up the contextual hooks based on $data
	 * @param array $data
	 * @return array
	 */
	public function setXhooks(array $data = array())
	{
        $return = array();
        if(!empty($data['company_id']))
            $return[] = array('company' => $data['company_id']);
            
        if(!empty($data['id']))
            $return[] = array('project' => $data['id']);  
                  
        if(!empty($data['project_id']))
            $return[] = array('project' => $data['project_id']);

        if(!empty($data['priority']))
        	$return[] = array('priority' => $data['priority']);
        
        if(!empty($data['type']))
        	$return[] = array('type' => $data['type']);

        if(!empty($data['status']))
        	$return[] = array('status' => $data['status']);
                        
        return $return;        
	}
}