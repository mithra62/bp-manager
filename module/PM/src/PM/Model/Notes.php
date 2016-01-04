<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Notes.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Base\Model\HashInterface;
use Application\Model\AbstractModel;

 /**
 * PM - Times Model
 *
 * @package 	Notes
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Notes.php
 */
class Notes extends AbstractModel implements HashInterface
{
    protected $inputFilter;
    
    /**
     * The Hashing object
     * @var \Application\Model\Hash
     */
    protected $hash;
    
	/**
	 * The Notes Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 * @param \Application\Model\Hash $hash
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db, \Application\Model\Hash $hash)
	{
		parent::__construct($adapter, $db);
		$this->hash = $hash;
	}
	
	/**
	 * Contains the database setting array
	 * @param array $data
	 * @return multitype:\Zend\Db\Sql\Expression unknown
	 */
	public function getSQL(array $data)
	{
		return array(
			'topic' => $data['topic'],
			'date' => $data['date'],
			'hashed' => $data['hashed'],
			'subject' => $data['subject'],
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
				'name'     => 'subject',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'description',
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
	 * Returns a note for a given $id
	 * @param int $id
	 * @return array
	 */
	public function getNoteById($id)
	{
		$sql = $this->db->select()->from(array('n'=>'notes'));
		$sql = $sql->where(array('n.id' => $id));
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = n.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = n.task_id', array('task_name' => 'name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = n.company_id', array('company_name' => 'name'), 'left');
		$note = $this->getRow($sql);
		if($note && $note['hashed'] == '1')
		{
			$note['description'] = $this->decrypt($note['description']);
		}
		
		return $note;
	}
	
	/**
	 * Returns the notes for a company
	 * @param int $id
	 * @return array
	 */
	public function getNotesByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.company_id'] = $id;
		return $this->getNotesWhere($where, $not);	
	}

	/**
	 * Returns the notes that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getNotesByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.project_id'] = $id;
		return $this->getNotesWhere($where, $not);
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getNotesByTaskId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.task_id'] = $id;
		return $this->getNotesWhere($where, $not);
	}
	
	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getNotesByUserId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.owner'] = $id;
		return $this->getNotesWhere($where, $not);
	}	

	private function getNotesWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->from(array('n'=> 'notes'));
		
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
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = n.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = n.task_id', array('task_name' => 'name'), 'left');
		return $this->getRows($sql);
	}	

	
	/**
	 * Inserts or updates a Note
	 * @param $data
	 * @param $creator
	 * @return mixed
	 */
	public function addNote($data, $creator)
	{
	    $ext = $this->trigger(self::EventNoteAddPre, $this, compact('data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$sql['company_id'] = (array_key_exists('company', $data) ? $data['company'] : 0);
		$sql['project_id'] = (array_key_exists('project', $data) ? $data['project'] : 0);
		$sql['task_id'] = (array_key_exists('task', $data) ? $data['task'] : 0);
		
		$note_id = $this->insert('notes', $sql);
		
		$ext = $this->trigger(self::EventNoteAddPost, $this, compact('data', 'note_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $note_id = $ext->last();
				
		return $note_id;
	}
	
	/**
	 * Updates a Note
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateNote($data, $note_id)
	{
	    $ext = $this->trigger(self::EventNoteUpdatePre, $this, compact('data', 'note_id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$update = $this->update('notes', $sql, array('id' => $note_id));
		
		$ext = $this->trigger(self::EventNoteUpdatePost, $this, compact('data', 'note_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $update = $ext->last();	

		return $update;
	}	
	
	/**
	 * Handles everything for removing a note.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeNote($note_id)
	{
		$data = $this->getNoteById($note_id);
	    $ext = $this->trigger(self::EventNoteRemovePre, $this, compact('note_id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $note_id = $ext->last();
	    	    
		$remove = $this->remove('notes', array('id' => $note_id));
		
		$ext = $this->trigger(self::EventNoteRemovePost, $this, compact('note_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $remove = $ext->last();
				
		return $remove;
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