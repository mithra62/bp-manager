<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Files.php
 */

namespace PM\Model\Options;

/**
 * PM - Projects Options Model
 *
 * @package 	Files\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Files.php
 */
class Files
{
	static public function status()
	{
		$types = array();
		$types[0] = 'Not Approved';
		$types[1] = 'Approved With Changes';		
		$types[2] = 'Needs Approval';
		$types[3] = 'No Approvals Needed';
		$types[4] = 'Approved';
		
		return $types;
	}

	static public function translateStatusId($id)
	{
		$types = self::status();
		return $types[$id];
	}
}