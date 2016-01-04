<?php 
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Tasks.php
 */

namespace PM\Model\Options;

/**
 * PM - Tasks Options Model
 *
 * @package 	Tasks\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Tasks.php
 */
class Tasks extends AbstractOptions
{
	/**
	 * Returns the availble "types" for tasks
	 * @param \PM\Model\Options $options
	 * @return Ambigous <\PM\Model\Options\multitype:string, multitype:string unknown >
	 */
	static public function types(\PM\Model\Options $options)
	{		
		return parent::filterOptions($options->getAllTaskTypes());
	}
	
	/**
	 * Translates a given type_id ($id) into its readable name
	 * @param int $id
	 * @param \PM\Model\Options $options
	 * @return Ambigous <>
	 */
	static public function translateTypeId($id, \PM\Model\Options $options)
	{
		$types = $options->getAllTaskTypes();
		foreach($types AS $type)
		{
			if($type['id'] == $id)
			{
				return $type['name'];
			}
		}
	}
	
	/**
	 * Creates the progress options array
	 * @return multitype:number
	 */
	static public function progress()
	{
		$arr = array();
		
		$i = 0;
		while($i <= 100)
		{
			$arr[$i] = $i;
			$i = ($i+5);
			if($i > 100)
				break;
		}
		return $arr;
	}
}