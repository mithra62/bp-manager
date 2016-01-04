<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Model/
 */

namespace Base\Model;

 /**
 * Key Value Abstract
 *
 * Abstracts handling of key => value style database tables
 * <br /><strong>This Model is only useful for data stores that use a key => value style storage mechanism.
 * @see \PM\Model\Settings for an example</strong>
 *
 * @package 	MojiTrac\Model
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Base/src/Base/Model/KeyValue.php
 */
abstract class KeyValue extends BaseModel
{	
	/**
	 * The database table we're working with
	 * @var string
	 */
	private $table = null;
	
	/**
	 * The data retrieved from the queries
	 * @var array
	 */
	private $items = array();
	
	/**
	 * The default values to use if none is found
	 * @var array
	 */
	private $defaults = array();
	
	/**
	 * Abstracts handling of key => value style database tables
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $sql
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter = null, \Zend\Db\Sql\Sql $sql = null)
	{
		parent::__construct($adapter, $sql);
	}
	
	/**
	 * Wrapper to create the SQL array for updating/creating an entry
	 * @param array $data
	 * @param string $create
	 */
	abstract public function getSQL(array $data, $create = FALSE);
	
	/**
	 * Sets the database table name
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->table = $table;	
	}
	
	/**
	 * Sets the default values to assume
	 * @param array $defaults
	 */
	public function setDefaults(array $defaults)
	{
		$this->defaults = $defaults;
	}
	
	/**
	 * Verifies that a submitted setting is valid and exists. If it's valid but doesn't exist it is created.
	 * @param string $setting
	 */
	protected function checkItem($item, array $where = array())
	{
		if(array_key_exists($item, $this->defaults))
		{
			$check = $this->getItem($item, $where);
			if(!$check)
			{
				$this->addItem($item, $where);
			}
				
			return true;
		}
	}
	
	/**
	 * Adds a setting to the databse
	 * @param string $setting
	 */
	public function addItem($item, array $more = array())
	{
		$insert = array('option_name' => $item);
		$sql = $this->getSQL($insert, TRUE);
		if($more)
		{
			$sql = array_merge($sql, $more);
		}

		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		return $this->insert($this->table, $sql);
	}
	
	/**
	 * Returns the individual item
	 * @param string $item
	 */
	public function getItem($item, array $where = array())
	{
		$sql = $this->db->select()->from($this->table)->where(array('option_name' => $item));
		if($where)
		{
			$sql->where($where);
		}
		
		$result = $this->getRow($sql);
		return $this->getRow($sql);
	}
	
	/**
	 * Updates the value of a setting
	 * @param string $key
	 * @param string $value
	 */
	public function updateItem($key, $value, array $where = array())
	{
		if(!$this->checkItem($key, $where))
		{
			return false;
		}
	
		$where['option_name'] = $key;
		$sql = $this->getSQL(array('option_name' => $key, 'option_value' => $value), FALSE);
		return $this->update($this->table, $sql, $where);
	}
	
	/**
	 * Updates all the settings for the provided array
	 * @param array $items
	 */
	public function updateItems(array $items, array $where = array())
	{
		foreach($items AS $key => $value)
		{
			$this->updateItem($key, $value, $where);
		}
	
		return TRUE;
	}
	
	/**
	 * Returns the settings array and sets the cache accordingly
	 */
	public function getItems(array $where = array())
	{
		if(!$this->items)
		{
			$sql = $this->db->select()->from($this->table);
			if($where) {
				$sql->where($where);
			}
			$this->items = $this->translateItems($this->getRows($sql));
		}
		
		return $this->items;
	}
	
	/**
	 * Takes the key=>value db rows and creates an associative array
	 * @param array $items
	 * @return multitype:boolean array
	 */
	protected function translateItems(array $items)
	{
		$arr = array();
		foreach($items AS $item)
		{
			if(in_array(strtolower($item['option_value']), array('1', 'true', 'yes')))
			{
				$arr[$item['option_name']] = '1';
			}
			elseif(in_array(strtolower($item['option_value']), array('0', 'false', 'no')))
			{
				$arr[$item['option_name']] = '0';
			}
			else
			{
				$arr[$item['option_name']] = $item['option_value'];
			}
		}
		
		//now we verify there are settings for everything
		foreach($this->defaults AS $key => $value)
		{
			if(!isset($arr[$key]))
			{
				$arr[$key] = $value;
			}
		}
	
		return $arr;
	}

	/**
	 * Removes the class variable container
	 * @return \Base\Model\KeyValue
	 */
	public function reset()
	{
		$this->items = array();
		return $this;
	}
}