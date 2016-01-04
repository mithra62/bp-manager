<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Projects.php
 */

namespace PM\Model\Options;

/**
 * PM - Projects Options Model
 *
 * @package 	Projects\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Projects.php
 */
class Projects extends AbstractOptions
{
	static public function types($options)
	{
		return parent::filterOptions($options->getAllProjectTypes());
	}
	
	static public function translatePriorityId($id)
	{
		$priority = self::priorities();
		return $priority[$id];		
	}
	
	static public function translateTypeId($id, \PM\Model\Options $options)
	{
		$types = $options->getAllProjectTypes();
		foreach($types AS $key => $value)
		{
			if($value['id'] == $id)
			{
				return $value['name'];
			}
		}
		
		return $types[$id];
	}
	
	/**
	 * thingy goes here...
	 * @param int $id
	 * @return string
	 */
	static public function translateStatusId($id)
	{
		$status = self::status();
		return $status[$id];		
	}
	
	static public function projects($blank = FALSE, $company_id = FALSE)
	{
		$projects = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$arr = $projects->getProjectOptions($company_id);
		
		$_new = array();
		if($blank)
		{
			$_new[null] = '';
		}
		foreach($arr AS $project)
		{
			$_new[$project['id']] = $project['name'];
		}
		return $_new;
	}
}