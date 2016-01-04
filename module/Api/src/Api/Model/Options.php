<?php
 /**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Options.php
 */

namespace Api\Model;

use PM\Model\Options as PmOptions;

/**
 * Api - Users Model
 *
 * @package 	Options\Api
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Model/Options.php
 */
class Options extends PmOptions
{
	/**
	 * Determines wheher we should filter results based on REST output
	 * @var bool
	 */
	private $filter = TRUE;
	
	/**
	 * The REST output for the users db table 
	 * @var array
	 */
	public $optionsOutputMap = array(
		'id' => 'id',
		'name' => 'name',
		'area' => 'area',
	);
	
	/**
	 * The REST output for the user_roles db table 
	 * @var array
	 */
	public $userRolesOutputMap = array(
		'id' => 'id',
		'id' => 'role_id',
		'name' => 'name',
		'description' => 'description'
	);
	
	/**
	 * (non-PHPdoc)
	 * @see \Application\Model\Users::getAllUsers()
	 */
	public function getAllOptions()
	{
		$users = parent::getAllOptions();
		$total_results = $this->getTotalResults();
		if(count($users) >= 1)
		{
			$return = array(
				'data' => $users,
				'total_results' => (int)$total_results,
				'total' => count($users),
				'page' => (int)$this->getPage(),
				'limit' => $this->getLimit()
			);
			
			return $return;
		}
	}
}