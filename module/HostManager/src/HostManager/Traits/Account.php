<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Traits/Account.php
 */

namespace HostManager\Traits;

/**
 * HostManager - Account Trait
 *
 * Contains methods for dealing with accounts
 *
 * @package 	MojiTrac\Traits
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Traits/Account.php
 */
trait Account
{
	/**
	 * The system account_id we're using
	 * @var int
	 */
	public $account_id = false;
	
	/**
	 * The ZF2 config array
	 * @param array $config
	 */
	public function setConfig(array $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Returns the Account ID
	 * @param array $where
	 * @return int
	 */
	public function getAccountId(array $where = array())
	{
		if( !$this->account_id )
		{
			$parts = parse_url($_SERVER['HTTP_HOST']);
			$sub = str_replace($this->config['sub_primary_url'], '', $parts['path']);
			$sql = $this->db->select()->from(array('a'=> 'accounts'))->columns(array('id'))->where(array('slug' => $sub));
			if( $where )
			{
				$sql = $sql->where($where);
			}
				
			$account = $this->getRow($sql);
			if( !empty($account['id']) )
			{
				$this->account_id = $account['id'];
			}
		}
	
		return $this->account_id;
	}
	
	/**
	 * Returns the account details for $where
	 * @param array $where
	 */
	public function getUserAccounts(array $where = array())
	{
		$sql = $this->db->select()->from(array('ua'=> 'user_accounts'));
		if( $where )
		{
			$sql = $sql->where($where);
		}
		
		return $this->getRows($sql);	
	}
	
	/**
	 * Links a user to a given account
	 * @param int $user_id
	 * @param int $account_id
	 */
	public function linkUserToAccount($user_id, $account_id)
	{
		$data = array('user_id' => $user_id, 'account_id' => $account_id, 'created_date' => new \Zend\Db\Sql\Expression('NOW()'));
		if( !$this->userOnAccount($user_id, $account_id) )
		{
			return $this->insert('user_accounts', $data);
		}
	}
	
	/**
	 * Checks if a user is on a given account
	 * @param int $user_id
	 * @param int $account_id
	 */
	public function userOnAccount($user_id, $account_id)
	{
		$where = array('user_id' => $user_id, 'account_id' => $account_id);
		$sql = $this->db->select()->from('user_accounts')->where($where)->join(array('u' => 'users'), 'user_id = u.id', array());
		return $this->getRow($sql);
	}
	
	/**
	 * Will return a detailed array about an account
	 * @param int $account_id
	 * @return array
	 */
	public function getAccountDetails($account_id)
	{
		$sql = $this->db->select()->from('settings')->where(array('option_name' => 'master_company', 'settings.account_id' => $account_id));
		$sql->join('companies', 'companies.id = settings.option_value', array('company_name' => 'name'));
		$sql->join('accounts', 'accounts.id = settings.account_id', array('slug'));
		$sql->join('users', 'accounts.owner_id = users.id', array('email', 'first_name', 'last_name'));
		return $this->getRow($sql);
	}
}