<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

 /**
 * PM - Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Options.php
 */
class Options extends AbstractModel
{
	/**
	 * Contains the input filter
	 * @var \Zend\InputFilter\InputFilter
	 */
    protected $inputFilter;
	
	/**
	 * The system areas where options are stored
	 * @var array
	 */
	public $areas = array(
		'project_type' => 'project_type',
		'task_type' => 'task_type'
	);
	
	/**
	 * The System Options
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * The SQL array for creating and updating options
	 * @param array $data
	 * @return multitype:\Zend\Db\Sql\Expression unknown
	 */
	public function getSQL(array $data){
		return array(
			'name' => $data['name'],
			'area' => $data['area'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}
	
	/**
	 * @ignore
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Sets the input filter object and configures it
	 * @param object $translator
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter($translator)
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
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $translator('required', 'pm') 
                            ),
                        ),
                    ),
                ),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'area',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $translator('required', 'pm') 
                            ),
                        ),
                    ),
                ),
			)));
			
			
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllProjectTypes()
	{		
			$sql = $this->db->select()->from('options')->columns(array('id', 'name'))
									  ->where(array('area' => $this->areas['project_type']))
									  ->order('name ASC');
			$types = $this->getRows($sql);
		return $types;
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllTaskTypes()
	{	
		$sql = $this->db->select()->from('options')->columns(array('id', 'name'))
								  ->where(array('area' => $this->areas['task_type']))
								  ->order('name ASC');
		$types = $this->getRows($sql);
		return $types;
	}
	
	/**
	 * Returns all the Options based on $where
	 * @param array $where
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getAllOptions(array $where = array())
	{
		$sql = $this->db->select()->from('options');
		if($where)
		{
			$sql = $sql->where($where);
		}
		
		return $this->getRows($sql);
	}
	
	/**
	 * Returns a specific option
	 * @param int $id
	 * @return array
	 */
	public function getOptionById($id)
	{
		$sql = $this->db->select()->from(array('o'=>'options'));
		$sql = $sql->where(array('o.id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Adds an Option to the db
	 * @param array $data
	 * @param int $creator
	 * @return int
	 */
	public function addOption(array $data, $creator)
	{
		$sql = $this->getSQL($data);
		$sql['creator'] = $creator;
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		return $this->insert('options', $sql);
	}
	
	/**
	 * Removes an option and updates all the entries for that option
	 * @param string $id
	 * @return bool
	 */
	public function removeOption($id)
	{
		if($this->remove('options', array('id' => $id)))
		{
			return TRUE;
		}
	}
	
	/**
	 * Updates an Ip Address on the white list
	 * @param array $data
	 * @param int $id
	 */
	public function updateOption(array $data, $id)
	{
		$sql = $this->getSQL($data);
		return $this->update('options', $sql, array('id' => $id));
	}
}