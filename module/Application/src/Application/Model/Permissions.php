<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./moji/Application/src/Application/Model/Permissions.php
 */

namespace Application\Model;

use Zend\Db\Sql\Sql;
use Application\Model\AbstractModel;

/**
 * Application - Permission Model
 *
 * @package 	Users\Roles\Permissions
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./moji/Application/src/Application/Model/Permissions.php
 */
class Permissions extends AbstractModel
{
	private $permissions;
	
	public $cache_key = 'permissions';
	
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Checks a users permission
	 * @param int $id
	 * @param string $permission
	 * @param bool $redirect
	 * @return bool
	 */
	public function check($id, $permission)
	{
		// If I don't have any permissions, fetch them
		if (!$this->permissions[$id] || !is_array($this->permissions[$id])) 
		{
			$this->permissions[$id] = array();
			
			$sql = $this->db->select()->from(array('urp'=>'user_role_permissions')) 
					->columns(array('name'));
			
			$sql = $sql->join('user_role_2_permissions', 'user_role_2_permissions.permission_id = urp.id');
			$sql = $sql->join('user2role', 'user2role.role_id = user_role_2_permissions.role_id');
			
			$sql = $sql->where(array('user2role.user_id' => $id));
			$perms = $this->getRows($sql);
			foreach($perms As $perm)
			{
				$this->permissions[$id][] = $perm['name'];
			}
		}

		if (in_array($permission ,$this->permissions[$id]))
		{
			return TRUE;
		} 
		else 
		{
			return FALSE;
		}
	}
	
}