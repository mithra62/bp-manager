<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Files/Revisions.php
 */

namespace PM\Model\Files;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;
use PM\Traits\File;

/**
 * PM - Files Model
 *
 * @package 	Files\Revisions
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Files/Revisions.php
 */
class Revisions extends AbstractModel
{
	use File;
	
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
			'file_name' => $data['file_name'],
			'stored_name' => $data['stored_name'],
			'size' => $data['size'],
			'extension' => $data['extension'],
			'mime_type' => $data['mime_type'],
			'description' => $data['description'],
			'status' => $data['status'],
			'approver' => $data['approver'],
			'uploaded_by' => $data['uploaded_by'],
			'approval_comment' => $data['approval_comment'],
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
	 * Returns an instance of the File Revisions InputFilter
	 * @param string $file_field Whether to return validation requirements on the file upload field
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter($file_field = false)
	{
		if (!$this->input_filter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();

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
	 * Adds a file revision to the system
	 * @param unknown $file_id
	 * @param array $data
	 * @param string $process_file
	 * @return int
	 */
	public function addRevision($file_id, array $data, $process_file = false)
	{	
		if($process_file) {
			
			$path = $this->checkMakeDirectory($data['upload_file_data']['destination'],
					$data['file_data']['company_id'],
					$data['file_data']['project_id'],
					$data['file_data']['task_id']
			);
			
			$data['extension'] = $this->getFileExtension($data['upload_file_data']['tmp_name']);
			$data['size'] = filesize($data['upload_file_data']['tmp_name']);
			$data['name'] = $data['upload_file_data']['name'];
			$data['type'] = $data['upload_file_data']['type'];
			$data['stored_name'] = time().'.'.$data['extension'];
			$new_name = $path.DS.$data['stored_name'];
			if(!rename($data['upload_file_data']['tmp_name'],$new_name))
			{
				return false;
			}
			
			$data['stored_path'] = $path;
			$data['file_name'] = $data['upload_file_data']['name'];
			$data['mime_type'] = $data['upload_file_data']['type'];
		}		

		$sql = $this->getSQL($data);
		$sql['file_id'] = $file_id;
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		
		return $this->insert('file_revisions', $sql);
	}

	/**
	 * Returns all the revisions for a given $file_id
	 * @param int $file_id
	 * @return array
	 */
	public function getFileRevisions($file_id)
	{
		$sql = $this->db->select()->from(array('fr' => 'file_revisions'))->where(array('file_id' => $file_id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = fr.uploaded_by', array('uploader_first_name' => 'first_name','uploader_last_name' => 'last_name'), 'left');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns all the revision for a given $revision_id
	 * @param int $revision_id
	 * @return array
	 */
	public function getRevision($revision_id)
	{
		$sql = $this->db->select()->from(array('fr' => 'file_revisions'))->where(array('fr.id' => $revision_id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = fr.uploaded_by', array('uploader_first_name' => 'first_name', 'uploader_last_name' => 'last_name'), 'left');
		return $this->getRow($sql);
	}
	
	/**
	 * Deletes a revision entry based on $revision_id
	 * @param int $revision_id
	 */
	public function removeRevision($revision_id)
	{
		$delete = $this->remove('file_revisions', array('id' => $revision_id));
		return $delete;
	}
	
	/**
	 * Returns the number of revisions a given $file_id has
	 * @param int $file_id
	 * @return int
	 */
	public function getTotalFileRevisions($file_id)
	{
		$sql = $this->db->select()->columns(array('Count' => new \Zend\Db\Sql\Expression('COUNT(id)')))->from(array('fr' => 'file_revisions'))->where(array('fr.file_id' => $file_id));
		$total = $this->getRow($sql);
		if($total)
		{
			return $total['Count'];
		}
	}
	
}