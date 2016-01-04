<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Accounts.php
 */

namespace HostManager\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;
use HostManager\Traits\Account;

/**
 * HostManager - Accounts Model
 *
 * @package 	HostManager
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Model/Accounts.php
 */
class Accounts extends AbstractModel
{
	use Account;

	const EventAddAccountPre = 'account.add.pre';
	const EventAddAccountPost = 'account.add.post';
	
	/**
	 * Prepares the SQL array for the accounts table
	 * @param array $data
	 * @return array
	 */	
	public function getSQL(array $data){
		return array(
			'slug' => $data['subdomain'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}
	
	/**
	 * @ignore
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns an instance of the InputFilter for data validation
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'email',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'EmailAddress',
					),
				),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'subdomain',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'Db\NoRecordExists',
						'options' => array(
							'table' => 'accounts',
						    'field' => 'slug',
							'adapter' => $this->adapter
						)
					),
					array(
						'name' => 'Alnum'		
					),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'password',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'organization',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'last_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'first_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Returns the data on an account
	 * @param array $where
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getAccount(array $where = array())
	{
		$sql = $this->db->select()->from(array('a'=> 'accounts'));
		if( $where )
		{
			$sql = $sql->where($where);
		}
		
		return $this->getRow($sql);	
	}

	/**
	 * Returns the data on an account
	 * @param array $where
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getAccounts(array $where = array())
	{
		$sql = $this->db->select()->from(array('a'=> 'accounts'));
		if( $where )
		{
			$sql = $sql->where($where);
		}
	
		return $this->getRows($sql);
	}	
	
	/**
	 * Creates a MojiTrac account 
	 * @param array $data
	 * @param \Application\Model\Users $user
	 * @param \PM\Model\Companies $company
	 * @param \Application\Model\Hash $hash
	 * @param \Application\Model\Settings $setting
	 * @param \PM\Model\Options $hash
	 */
	public function createAccount(array $data, 
			\Application\Model\Users $user, 
			\PM\Model\Companies $company, 
			\Application\Model\Hash $hash, 
			\Application\Model\Settings $setting, 
			\PM\Model\Options $option
	)
	{	

		$ext = $this->trigger(self::EventAddAccountPre, $this, compact('data'), array());
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		$user_data = $user->getUserByEmail($data['email']);
		if( !$user_data )
		{
			$user_id = $user->addUser($data, $hash);
			$user_data = $user->getUserById($user_id);
		}
		
		$user_id = $user_data['id'];
		$sql = $this->getSQL($data);
		$sql['owner_id'] = $user_data['id'];
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$account_id = $this->insert('accounts', $sql);
		
		$this->linkUserToAccount($user_data['id'], $account_id);
		
		//create the user roles now
		$user_roles = $user->roles->getAllRoles(array('account_id' => 1));
		$new_user_roles = array();
		foreach($user_roles AS $user_role)
		{
			$permissions = $user->roles->getRolePermissions($user_role['id']);
			$sql = array('name' => $user_role['name'], 'description' => $user_role['description'], 'account_id' => $account_id, 'created_date' => new \Zend\Db\Sql\Expression('NOW()'), 'last_modified' => new \Zend\Db\Sql\Expression('NOW()'));
			$role_id = $this->insert('user_roles', $sql);
			foreach($permissions As $perm)
			{
				$sql = array('role_id' => $role_id, 'permission_id' => $perm);
				$this->insert('user_role_2_permissions', $sql);
			}

			//attach the user to the role
			$sql = array('role_id' => $role_id, 'user_id' => $user_data['id'], 'account_id' => $account_id);
			$this->insert('user2role', $sql);
		}
		
		//now create the initial company
		$company_data = array('name' => $data['organization'], 'type' => '6');
		$company_id = $company->addCompany($company_data);
		if( $company_id )
		{
			$sql = array('account_id' => $account_id);
			$this->update('companies', $sql, array('id' => $company_id));
		}
		
		//and link the new company as the master company for this account
		$sql = array(
			'option_name' => 'master_company', 
			'option_value' => $company_id, 
			'account_id' => $account_id, 
			'created_date' => new \Zend\Db\Sql\Expression('NOW()'), 
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		); 
		$this->insert('settings', $sql);
		
		//now create the option types
		$option_data = $option->getAllOptions();
		foreach($option_data AS $opt)
		{
			$sql = array('name' => $opt['name'], 'area' => $opt['area'], 'account_id' => $account_id, 'created_date' => new \Zend\Db\Sql\Expression('NOW()'), 'last_modified' => new \Zend\Db\Sql\Expression('NOW()'));
			$this->insert('options', $sql);
		}		
		

		$ext = $this->trigger(self::EventAddAccountPost, $this, compact('account_id', 'user_id'), array());
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $account_id = $ext->last();
				
		//and wrap it up so we can go home
		return $account_id;
	}
	
	/**
	 * Returns a full URL for the passed account_id
	 * @param int $account_id
	 * @return string
	 */
	public function createAccountUrl($account_id)
	{
		$account_data = $this->getAccount(array('id' => $account_id));
		if($account_data)
		{
			return 'http://'.$account_data['slug'].$this->config['sub_primary_url'];
		}
	}
	
	/**
	 * Removes a user from an account
	 * @param unknown $user_id
	 * @param string $account_id
	 * @return int
	 */
	public function removeUserFromAccount($user_id, $account_id = false)
	{
		if(!$account_id)
		{
			$account_id = $this->getAccountId();
		}

		if($this->remove('user_accounts', array('user_id' => $user_id, 'account_id' => $account_id)))
		{
			$this->remove('user2role', array('user_id' => $user_id, 'account_id' => $account_id));
			return true;
		}
	}
}