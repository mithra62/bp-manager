<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb <eric@mithra62.com>
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Settings.php
*/

namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Base\Model\KeyValue;

/**
* Setting Model
*
* @package 		Settings
* @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Model/Settings.php
*/
class Settings extends KeyValue
{		
	/**
	 * The validation filters
	 * @var object
	 */
	protected $inputFilter;
		
	/**
	 * The databaes table the Moji Settings are stored in
	 * @var string
	 */
	public $table = 'settings';
		
	/**
	 * Contains all the defaults for the global settings
	 * @var array
	 */
	public $defaults = array(
		'date_format' => 'F j, Y',
		'date_format_custom' => '',  
		'time_format' => 'g:i A',  
		'time_format_custom' => '',
	    'default_user_groups' => array(2)
	);	

	/**
	 * The system setttings array
	 * @var array
	 */
	public $settings = array();
	
	/**
	 * Abstracts handling of key => value style database tables
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $sql
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter = null, \Zend\Db\Sql\Sql $sql = null)
	{
		parent::__construct($adapter, $sql);
		$this->setTable($this->table);
		
		$defaults = $this->defaults;
		$ext = $this->trigger(self::EventSettingsDefaultsSetPre, $this, compact('defaults'));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $defaults = $ext->last();

		$this->setDefaults($defaults);

		$defaults = $this->defaults;
		$ext = $this->trigger(self::EventSettingsDefaultsSetPost, $this, compact('defaults'));
	}	
	
	/**
	 * Creates the base SQL query for updates and inserts
	 * @param array $data
	 * @return multitype:\Zend\Db\Sql\Expression unknown
	 */
	public function getSQL(array $data, $create = TRUE){
		return array(
			'option_value' => (isset($data['option_value']) ? $data['option_value'] : ''),
			'option_name' => $data['option_name'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Returns the full settings array
	 * @return multitype:array
	 */
	public function getSettings(array $where = array())
	{
		return parent::getItems($where);
	}
	
	/**
	 * Sets the Input Filter
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns an instance of the Input Filter
	 * @return object
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'email',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'EmailAddress',
					),
					array(
						'name' => 'Db\RecordExists',
						'options' => array(
							'table' => 'users',
						    'field' => 'email',
							'adapter' => $this->authAdapter
						)
					),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Handles updating a setting
	 * @param array $settings
	 * @return boolean
	 */
	public function updateSettings(array $settings, array $where = array())
	{
		return parent::updateItems($settings, $where);
	}
	
}