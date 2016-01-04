<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Bookmarks.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Base\Model\HashInterface;
use Application\Model\AbstractModel;

/**
 * PM - Bookmarks Model
 *
 * @package 	Bookmarks
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Bookmarks.php
 */
class Bookmarks extends AbstractModel implements HashInterface
{	
    protected $inputFilter;

    /**
     * The Hashing object
     * @var \Application\Model\Hash
     */
    protected $hash;
    
	/**
	 * The Times Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db, \Application\Model\Hash $hash)
	{
		parent::__construct($adapter, $db);
		$this->hash = $hash;
	}
	
	public function getSQL($data){
		return array(
			'owner' => $data['owner'],
			'name' => $data['name'],
			'url' => $data['url'],
			'hashed' => $data['hashed'],
			'description' => ($data['hashed'] == '1' ? $this->encrypt($data['description']) : $data['description']),
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
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
	 * Returns a bookmark for a given task $id
	 * @param int $id
	 * @return array
	 */
	public function getBookmarkById($id)
	{
		$sql = $this->db->select()->from(array('bk'=> 'bookmarks'));
		$sql = $sql->where(array('bk.id' => $id));
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = bk.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('u' => 'users'), 'u.id = bk.owner', array('owner_first_name' => 'first_name', 'owner_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = bk.task_id', array('task_name' => 'name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = bk.company_id', array('company_name' => 'name'), 'left');
		$bookmark = $this->getRow($sql);
		
		if($bookmark && $bookmark['hashed'] == '1')
		{
			$bookmark['description'] = $this->decrypt($bookmark['description']);
		}
		
		return $bookmark;
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllBookmarks($view_type = FALSE)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('b'=>$this->db->getTableName()));
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where('p.status = ?', $view_type);
		}
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = b.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = b.owner', array('first_name AS owner_first_name', 'last_name AS owner_last_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = b.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
			
		return $this->db->getBookmarks($sql);		
	}
	
	/**
	 * Returns the tasks for a company
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.company_id'] = $id;
		return $this->getBookmarksWhere($where, $not);	
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.project_id'] = $id;
		return $this->getBookmarksWhere($where, $not);
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByTaskId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.task_id'] = $id;
		return $this->getBookmarksWhere($where, $not);
	}
	
	/**
	 * Returns the bookmarks that belong to a user
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByUserId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.owner'] = $id;
		return $this->getBookmarksWhere($where, $not);
	}
	
	private function getBookmarksWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->from(array('bk'=> 'bookmarks'));
		
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
				$sql = $sql->where("$key != ? ", $value);
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
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = bk.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = bk.task_id', array('task_name' => 'name'), 'left');
		return $this->getRows($sql);	
	}	

	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addBookmark($data)
	{
		$ext = $this->trigger(self::EventBookmarkAddPre, $this, compact('data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		$sql = $this->getSQL($data);
		$sql['company_id'] = (array_key_exists('company', $data) ? $data['company'] : 0);
		$sql['project_id'] = (array_key_exists('project', $data) ? $data['project'] : 0);
		$sql['task_id'] = (array_key_exists('task', $data) ? $data['task'] : 0);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
			
		$bookmark_id = $data['bookmark_id'] = $this->insert('bookmarks', $sql);
		
		$ext = $this->trigger(self::EventBookmarkAddPost, $this, compact('bookmark_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $bookmark_id = $ext->last();
				
		return $bookmark_id;
	}
	
	/**
	 * Updates a bookmark
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateBookmark($data, $bookmark_id)
	{
	    $ext = $this->trigger(self::EventBookmarkUpdatePre, $this, compact('data', 'bookmark_id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		$sql = $this->getSQL($data);
		$return = $this->update('bookmarks', $sql, array('id' => $bookmark_id));
		
	    $ext = $this->trigger(self::EventBookmarkUpdatePost, $this, compact('bookmark_id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $return = $ext->last();

		return $return;
	}	
	
	/**
	 * Removes a Bookmark based on the $id.
	 * @param $id
	 * @return bool
	 */
	public function removeBookmark($bookmark_id)
	{
	    $data = $this->getBookmarkById($bookmark_id);
	    $ext = $this->trigger(self::EventBookmarkRemovePre, $this, compact('bookmark_id', 'data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $bookmark_id = $ext->last();
	    	    
		$remove = $this->remove('bookmarks', array('id' => $bookmark_id));
		
		$ext = $this->trigger(self::EventBookmarkRemovePost, $this, compact('bookmark_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $remove = $ext->last();
				
		return $remove;
	}
	
	/**
	 * Removes all the Bookmarks based on the $company_id.
	 * @param $company_id
	 * @return bool
	 */
	public function removeBookmarksByCompany($company_id)
	{
		return $this->db->deleteBookmark($company_id, 'company_id');
	}

	/**
	 * Removes all the Bookmarks based on the $project_id.
	 * @param $project_id
	 * @return bool
	 */
	public function removeBookmarksByProject($project_id)
	{
		return $this->db->deleteBookmark($project_id, 'project_id');
	}	
	
	/**
	 * Sets up the contextual hooks based on $data
	 * @param array $data
	 * @return array
	 */
	public function setXhooks(array $data = array())
	{
		$return = array();
		if(!empty($data['company']))
			$return[] = array('company' => $data['company']);
	
		if(!empty($data['id']))
			$return[] = array('bookmark' => $data['id']);
	
		if(!empty($data['project']))
			$return[] = array('project' => $data['project']);
	
		if(!empty($data['priority']))
			$return[] = array('priority' => $data['priority']);
	
		if(!empty($data['type']))
			$return[] = array('type' => $data['type']);
	
		if(!empty($data['status']))
			$return[] = array('status' => $data['status']);
	
		return $return;
	}

	/**
	 * Encrypts a string
	 * @see \Base\Model\HashInterface::encrypt()
	 */
	public function encrypt($string) {
		return $this->hash->encrypt($string);
	}
	
	/**
	 * Decrypts a string
	 * @see \Base\Model\HashInterface::decrypt()
	 */
	public function decrypt($string) {
		return $this->hash->decrypt($string);
	}
	
}