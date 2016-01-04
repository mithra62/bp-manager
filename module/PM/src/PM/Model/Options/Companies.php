<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Companies.php
 */

namespace PM\Model\Options;

/**
 * PM - Companies Options Model
 *
 * @package 	Companies\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Companies.php
 */
class Companies extends AbstractOptions
{
	/**
	 * Returns an associative array for the supported Company Types
	 * @return multitype:string
	 */
	static public function types()
	{
		$types = array();
		$types[0] = 'Not Applicable';
		$types[1] = 'Client';
		$types[2] = 'Vendor';
		$types[3] = 'Supplier';
		$types[4] = 'Consultant';
		$types[5] = 'Government';
		$types[6] = 'Internal';
		
		return $types;
	}
	
	/**
	 * Translates a type_id to a human name
	 * @param int $id
	 * @return Ambigous <string>
	 */
	static public function translateTypeId($id)
	{
		$types = self::types();
		return $types[$id];
	}
	
	/**
	 * Returns an associative array of company names and company IDs
	 * @param \PM\Model\Companies $companies
	 * @param string $blank
	 * @param string $empty
	 * @param string $types
	 * @param string $ids
	 * @return multitype:|multitype:string Ambigous <>
	 */
	static public function companies(\PM\Model\Companies $companies, $blank = FALSE, $empty = FALSE, $types = FALSE, $ids = FALSE)
	{
		if($empty)
		{
			return array();
		}
		
		$arr = $companies->getAllCompanyNames($types, $ids);
		$_new = array();
		if($blank)
		{
			$_new[null] = '';
		}
		foreach($arr AS $company)
		{
			$_new[$company['id']] = $company['name'];
		}
		
		return $_new;
	}
}