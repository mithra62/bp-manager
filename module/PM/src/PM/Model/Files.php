<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Files.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;
use PM\Traits\File;

 /**
 * PM - Files Model
 *
 * @package 	Files
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Files.php
 */
class Files extends AbstractModel
{
	/**
	 * Include the File Trait
	 */
	use File;
	
	/**
	 * The Revisions Model 
	 * @var \PM\Model\Files\Revisions
	 */
	public $revision = null;
	
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $input_filter;
	
	/**
	 * The Transfer Adapter for validating and moving uploaded files
	 * @var \Zend\File\Transfer\Adapter\Http
	 */
	protected $file_transfer_adapter;
	
	/**
	 * The Project Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 * @param \PM\Model\Files\Revisions $rev
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db, \PM\Model\Files\Revisions $rev)
	{
		parent::__construct($adapter, $db);
		$this->revision = $rev;
	}
    
	/**
	 * Returns an array for modifying the `files` table
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
			'name' => $data['name'],
			'description' => $data['description'],
			'status' => $data['status'],
			'creator' => $data['creator'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}
	
	/**
	 * Sets the input filter
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns an instace of the InputFilter, creating it if it doens't exist yet
	 * @param string $file_field Determines if a file field should be validated
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter($file_field = false)
	{
		if (!$this->input_filter) {
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

			if($file_field)
			{
				$inputFilter->add($factory->createInput(array(
					'name'     => 'file_upload',
					'required' => true,
					'messages' => array(
	                	\Zend\Validator\NotEmpty::IS_EMPTY => 'fdsafdsa'
					),
				)));	
			}	
			
	
			$this->input_filter = $inputFilter;
		}
	
		return $this->input_filter;
	}

	/**
	 * Sets up the Transfer Adapter and returns it
	 * @param string $file_name	The name for the upload field we're validating
	 * @return \Zend\File\Transfer\Adapter\Http
	 */
	public function getFileTransferAdapter($file_name)
	{
		if(!$this->file_transfer_adapter)
		{
			$this->file_transfer_adapter = new \Zend\File\Transfer\Adapter\Http();
			$validators = array(
				//new \Application\Validate\Upload(),
				new \Zend\Validator\File\Size(array('max'=>$this->getMaxFileSize())),
			);
			$this->file_transfer_adapter->setValidators($validators, $file_name);
			$this->file_transfer_adapter->setDestination($this->getStoragePath());
		}
		
		return $this->file_transfer_adapter;		
	}
	
	/**
	 * The maximum file size, in bytes, for an uploaded file
	 * @return string
	 * @todo abstract max size to settings
	 */
	public function getMaxFileSize()
	{
		return '52428800';
	}
	
	/**
	 * Returns the file data only based on $where and $not
	 * @param string $view_type
	 * @param array $where
	 * @param array $not
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getAllFiles($view_type = false, array $where = null, array $not = null)
	{
		if($view_type)
		{
			if(!is_array($where))
			{
				$where = array();
			}
			$where['f.status'] = $view_type;
		}
		
		return $this->getFilesWhere($where, $not);			
	}
	
	/**
	 * Returns a specific file based on $id
	 * @param int $id
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getFileById($id)
	{
		$sql = $this->db->select()->from(array('f'=>'files'));
		
		$sql = $sql->where(array('f.id' => $id));
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = f.project_id', array('project_name' => 'name'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = f.task_id', array('task_name' => 'name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = f.company_id', array('company_name' => 'name'), 'left');
		$sql = $sql->join(array('u' => 'users'), 'u.id = f.creator', array('file_creator_first_name' => 'first_name','file_creator_last_name' => 'last_name'), 'left');
		
		return $this->getRow($sql);			
	}
	
	/**
	 * Returns all a company's files
	 * @param int $id
	 * @param array $where
	 * @param array $not
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getFilesByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.company_id'] = $id;
		return $this->getFilesWhere($where, $not);			
	}
	
	/**
	 * Returns all a project's files
	 * @param int $id
	 * @param array $where
	 * @param array $not
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getFilesByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.project_id'] = $id;
		return $this->getFilesWhere($where, $not);				
	}
	
	/**
	 * Returns all a task's files
	 * @param int $id
	 * @param array $where
	 * @param array $not
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getFilesByTaskId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.task_id'] = $id;
		return $this->getFilesWhere($where, $not);				
	}
	
	/**
	 * Returns all the files uploaded by a given user
	 * @param int $id
	 * @param array $where
	 * @param array $not
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getFilesByUserId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.creator'] = $id;
		return $this->getFilesWhere($where, $not);				
	}	
	
	/**
	 * Allows for abstract file database queries
	 * @param array $where
	 * @param array $not
	 * @param array $orwhere
	 * @param array $ornot
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	private function getFilesWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->from(array('f'=> 'files'));
		
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
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = f.project_id', array('project_name' => 'name'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = f.task_id', array('task_name' => 'name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = f.company_id', array('company_name' => 'name'), 'left');
		$sql = $sql->join(array('u' => 'users'), 'u.id = f.creator', array('file_creator_first_name' => 'first_name', 'file_creator_last_name' => 'last_name'), 'left');
		
		return $this->getRows($sql);	
		
	}	

	/**
	 * Adds a file to the system
	 * @param array $data
	 * @param array $file_info
	 * @param \PM\Model\Projects $project
	 * @param \PM\Model\Tasks $task
	 * @return boolean|int
	 */
	public function addFile($data, $file_info, \PM\Model\Projects $project = null, \PM\Model\Tasks $task = null)
	{
		$ext = $this->trigger(self::EventFileAddPre, $this, compact('data', 'file_info'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
		
		$sql = $this->getSQL($data);
		$sql['company_id'] = (array_key_exists('company_id', $data) ? $data['company_id'] : 0);
		$sql['project_id'] = (array_key_exists('project_id', $data) ? $data['project_id'] : 0);
		$sql['task_id'] = (array_key_exists('task_id', $data) ? $data['task_id'] : 0);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		
		$data['file_id'] = $file_id = $this->insert('files', $sql);
		if($data['file_id'])
		{
			if(is_numeric($data['project_id']) && $project)
			{
				$project->updateProjectFileCount($data['project_id'], 1, 'file_count');
			}
			
			if(is_numeric($data['task_id']) && $task)
			{
				$task->updateTaskFileCount($data['task_id'], 1, 'file_count');
			}
			
			$data['upload_file_data'] = $file_info;
			$data['file_data'] = $this->getFileById($data['file_id']);
			$file_info['revision_id'] = $this->revision->addRevision($data['file_id'], $data, true);		
		}
		
		$ext = $this->trigger(self::EventFileAddPost, $this, compact('file_id', 'data', 'file_info'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data['file_id'] = $ext->last();
				
		return $data['file_id'];
	}
	
	/**
	 * Updates a file
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateFile($data, $file_id)
	{
		$ext = $this->trigger(self::EventFileUpdatePre, $this, compact('file_id', 'data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		$sql = $this->getSQL($data);
		$update = $this->update('files', $sql, array('id' => $file_id));

		$ext = $this->trigger(self::EventFileUpdatePost, $this, compact('file_id', 'data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $update = $ext->last();
		
		return $update;
	}
	
	/**
	 * Removes a file from the system
	 * @param $id
	 * @return bool
	 */
	public function removeFile($file_id)
	{
		$ext = $this->trigger(self::EventFileRemovePre, $this, compact('file_id'), $this->setXhooks(array()));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $file_id = $ext->last();
		
		$delete = $this->remove('files', array('id' => $file_id));
		if($delete)
		{
			$this->remove('file_revisions', array('file_id' => $file_id));
		}

		$ext = $this->trigger(self::EventFileRemovePost, $this, compact('file_id'), $this->setXhooks(array()));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $delete = $ext->last();
		
		return $delete;
	}
	
	/**
	 * Adds a revision
	 * @param int $file_id
	 * @param array $data
	 * @param string $process_file
	 * @return Ambigous <\Zend\EventManager\mixed, NULL, mixed>|Ambigous <number, boolean, \Base\Model\Ambigous, \Zend\Db\Adapter\Driver\mixed, NULL, \Zend\EventManager\mixed, mixed>
	 */
	public function addRevision($file_id, array $data, $process_file = false)
	{
		$ext = $this->trigger(self::EventFileRevisionAddPre, $this, compact('file_id', 'data', 'process_file'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
		
		$revision_id = $this->revision->addRevision($file_id, $data, $process_file);
		
		$ext = $this->trigger(self::EventFileRevisionAddPost, $this, compact('revision_id', 'data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $revision_id = $ext->last();

		return $revision_id;
	}
	
	/**
	 * Removes a revision
	 * @param int $revision_id
	 * @return Ambigous <\Zend\EventManager\mixed, NULL, mixed>|unknown
	 */
	public function removeRevision($revision_id)
	{
		$ext = $this->trigger(self::EventFileRevisionRemovePre, $this, compact('revision_id'), $this->setXhooks(array()));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $revision_id = $ext->last();
		
		$delete = $this->revision->removeRevision($revision_id);
		
		$ext = $this->trigger(self::EventFileRevisionRemovePost, $this, compact('revision_id'), $this->setXhooks(array()));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $revision_id = $ext->last();
		
		return $delete;		
	}
	
	/**
	 * Handles moving all a Project's file data to a new company 
	 * @param int $project_id
	 * @param int $company_id
	 * @return int
	 */
	public function changeProjectCompany($project_id, $company_id)
	{
		$file_data = $this->getFilesByProjectId($project_id);
		if($file_data)
		{
			foreach($file_data AS $file)
			{
				$task_id = ($file['task_id'] != '0' ? $file['task_id'] : false);
				$old_path = $this->checkMakeDirectory($this->getStoragePath(), $file['company_id'], $project_id, $task_id);
				$new_path = $this->checkMakeDirectory($this->getStoragePath(), $company_id, $project_id, $task_id);
				//now grab the revisions
				
				$revision_data = $this->revision->getFileRevisions($file['id']);
				if( $revision_data )
				{
					foreach($revision_data AS $revision)
					{
						//now move things to where they need to be
						$old_file_path = realpath($old_path.'/'.$revision['stored_name']);
						$new_file_path = realpath($new_path).'/'.$revision['stored_name'];
						if( file_exists($old_file_path) )
						{
							rename($old_file_path, $new_file_path);
						}
					}
				}
			}
			
			return $this->update('files', array('company_id' => $company_id), array('project_id' => $project_id));
		}
	}
}