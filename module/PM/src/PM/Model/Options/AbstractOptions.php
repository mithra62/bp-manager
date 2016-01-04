<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/AbstractOptions.php
 */

namespace PM\Model\Options;

/**
 * PM - Abstract Options Object
 *
 * @package 	Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/AbstractOptions.php
 */
abstract class AbstractOptions
{
	/**
	 * Creates an array of options for form input
	 * @param array $options
	 * @return multitype:string array
	 */
	static public function filterOptions($options)
	{
		$arr = array();
		$arr['0'] = 'Unknown';
		foreach($options AS $option)
		{
			$arr[$option['id']] = $option['name'];
		}
		return $arr;
	}
	
	static public function priorities()
	{
		$priorities = array();
		$priorities[0] = 'None';
		$priorities[1] = 'Very Low';
		$priorities[2] = 'Low';
		$priorities[3] = 'Medium';
		$priorities[4] = 'High';
		$priorities[5] = 'Very High';
		return $priorities;		
	}
	
	static public function status()
	{
		$status = array();
		$status[0] = 'Not Defined';
		$status[1] = 'Proposed';
		$status[2] = 'In Planning';
		$status[3] = 'In Progress';
		$status[4] = 'On Hold';
		$status[5] = 'Complete';
		$status[6] = 'Archived';
		return $status;
	}	
}