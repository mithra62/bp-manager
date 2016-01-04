<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/ActivityLog.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - ProjectForm Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/ActivityLog.php
 */
class ActivityLog extends AbstractModel
{
    
    /**
     * The Activity Log Model
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Sql\Sql $db
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
    {
    	parent::__construct($adapter, $db);
    }

    /**
     * The database SQL array
     * @param array $data
     * @return multitype:Ambigous <string, unknown>
     */
    public function getSQL(array $data = array())
    {
        return array(
    		'date' => (!empty($data['date']) ? $data['date'] : ''),
    		'type' => (!empty($data['type']) ? $data['type'] : ''),
    		'performed_by' => (!empty($data['performed_by']) ? $data['performed_by'] : ''),
    		'stuff' => (!empty($data['stuff']) ? json_encode($data['stuff']) : ''),
    		'company_id' => (!empty($data['company_id']) ? $data['company_id'] : '0'),
    		'project_id' => (!empty($data['project_id']) ? $data['project_id'] : '0'),
    		'task_id' => (!empty($data['task_id']) ? $data['task_id'] : '0'),
    		'note_id' => (!empty($data['note_id']) ? $data['note_id'] : '0'),
    		'bookmark_id' => (!empty($data['bookmark_id']) ? $data['bookmark_id'] : '0'),
    		'user_id' => (!empty($data['user_id']) ? $data['user_id'] : '0'),
    		'file_id' => (!empty($data['file_id']) ? $data['file_id'] : '0'),
    		'file_rev_id' => (!empty($data['file_rev_id']) ? $data['file_rev_id'] : '0'),
    		'file_review_id' => (!empty($data['file_review_id']) ? $data['file_review_id'] : '0'),
    		'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
        );      
    }
	
	/**
	 * Returns the mysql formatted timestamp
	 * @return string
	 */
	static private function setDate()
	{
		return date('Y-m-d H:i:s');
	}
	
	/**
	 * Returns all the activity for a given project $id
	 * @param int $id
	 */
	public function getUsersProjectActivity($id, $filter = FALSE, $limit = 20)
	{
		$sql = $this->db->select()->from(array('a'=>'activity_logs'));
		$sql->join(array('pt' => 'project_teams'), 'pt.project_id = a.project_id', array());

		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = a.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = a.task_id', array('task_name' => 'name'), 'left');
		$sql = $sql->join(array('n' => 'notes'), 'n.id = a.note_id AND pt.project_id = n.project_id', array('note_subject' => 'subject'), 'left');
		$sql = $sql->join(array('u' => 'users'), 'u.id = a.user_id', array('user_first_name' => 'first_name', 'user_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = a.performed_by', array('performed_by_first_name' => 'first_name', 'performed_by_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('b' => 'bookmarks'), 'b.id = a.bookmark_id AND pt.project_id = b.project_id', array('bookmark_name' => 'name'), 'left');
		$sql = $sql->join(array('f' => 'files'), 'f.id = a.file_id', array('file_name' => 'name'), 'left');
		
		if($filter && is_array($filter))
		{
			if(array_key_exists('company_id', $filter) && is_numeric($filter['company_id']))
			{
				$sql = $sql->where('a.company_id = ?', $filter['company_id']);
			}
			
			if(array_key_exists('project_id', $filter) && is_numeric($filter['project_id']))
			{
				$sql = $sql->where(array('a.project_id' => $filter['project_id']));
			}			
		}
		
		$sql = $sql->where(array('pt.user_id' => $id));
		$sql = $sql->order('a.date DESC');
		$sql = $sql->limit($limit);
		return $this->getRows($sql);
	}

	/**
	 * Handles the logging of an activity
	 * @param $data
	 * @return void
	 */
	public function logActivity(array $data)
	{	
	    $ext = $this->trigger(self::EventActivityLogAddPre, $this, compact('data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    
		$sql = $this->getSQL($data);
		$sql['date'] = new \Zend\Db\Sql\Expression('NOW()');
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$this->insert('activity_logs', $sql);
		
		$ext = $this->trigger(self::EventActivityLogAddPre, $this, compact('data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); 
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
	
		if(!empty($data['project_id']))
			$return[] = array('project' => $data['project_id']);
	
		if(!empty($data['file_id']))
			$return[] = array('file' => $data['file_id']);
	
		if(!empty($data['bookmark_id']))
			$return[] = array('bookmark' => $data['bookmark_id']);
	
		if(!empty($data['task_id']))
			$return[] = array('task' => $data['task_id']);
	
		return $return;
	}	
}