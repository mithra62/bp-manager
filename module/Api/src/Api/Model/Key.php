<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Key.php
 */

namespace Api\Model;

/**
 * Api - Users Model
 *
 * @package 	Authentication\Api
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Api/src/Api/Model/Key.php
 */
class Key
{
	private $user = null;
	
	public function __construct(\Application\Model\Hash $hash, \Api\Model\Users $user)
	{
		$this->hash = $hash;
		$this->user = $user;
	}
	
	public function getKey($identity)
	{
		$user_data = $this->user->user_data->getUsersData($identity);
		if(isset($user_data['rest_api_secret']))
		{
			if($user_data['rest_api_secret'] == '')	
			{
				$user_data['rest_api_secret'] = $this->hash->guidish();
				$this->setKey($identity, $user_data['rest_api_secret']);
				return $user_data['rest_api_secret'];
			}
			else 
			{
				return $this->hash->decrypt($user_data['rest_api_secret']);
			}
		}
	}
	
	public function setKey($identity, $key)
	{
		$key = $this->hash->encrypt($key);
		$this->user->user_data->updateUserDataEntry('rest_api_secret', $key, $identity);
	}
}