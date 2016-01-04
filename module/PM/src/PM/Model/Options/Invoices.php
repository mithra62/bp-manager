<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Invoices.php
 */

namespace PM\Model\Options;

/**
 * PM - Invoices Options Model
 *
 * @package 	Invoices\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Invoices.php
 */
class Invoices
{
	static public function status()
	{
		$types = array();
		$types['draft'] = 'Draft';
		$types['sent'] = 'Sent';		
		$types['paid'] = 'Paid';
		$types['cancelled'] = 'Cancelled';
		$types['refunded'] = 'Refunded';
		
		return $types;
	}
	
	static public function types()
	{
		$types = array();
		$types[0] = 'Time Based';
		$types[1] = 'Item Based';		
		$types[2] = 'Combined';
		
		return $types;
	}

	static public function translateStatusId($id)
	{
		$types = self::status();
		return $types[$id];
	}
}