<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Roles.php
 */
namespace Application\Model\User;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

/**
 * Application - User Roles Model
 *
 * @package Users\Roles
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/Roles.php
 */
class Roles extends AbstractModel
{

    /**
     * The cache object
     * 
     * @var object
     */
    public $cache;

    /**
     * Contains all the user permissions
     * 
     * @var array
     */
    public static $permissions = FALSE;

    /**
     * The validation filters
     * 
     * @var object
     */
    protected $inputFilter;

    /**
     *
     * @ignore
     *
     * @param \Zend\Db\Adapter\Adapter $adapter            
     * @param \Zend\Db\Sql\Sql $db            
     * @param \Application\Model\Permissions $permissions            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db, \Application\Model\User\Permissions $permissions)
    {
        parent::__construct($adapter, $db);
        $this->perm = $permissions;
    }

    /**
     * Returns an array for modifying $_name
     * 
     * @param
     *            $data
     * @return array
     */
    public function getSQL($data)
    {
        return array(
            'name' => $data['name'],
            'description' => $data['description'],
            'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
        );
    }

    /**
     * Sets the InputFilter
     * 
     * @param InputFilterInterface $inputFilter            
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Role CRUD Validation logic
     * 
     * @return object
     */
    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    /**
     * Returns an individual user array
     * 
     * @param int $id            
     * @return array
     */
    public function getRoleById($id)
    {
        $sql = $this->db->select()
            ->from(array(
            'r' => 'user_roles'
        ))
            ->where(array(
            'r.id' => $id
        ));
        return $this->getRow($sql);
    }

    /**
     * Returns an array of all user names
     * 
     * @return mixed
     */
    public function getAllRoleNames()
    {
        $sql = $this->db->select()
            ->from('user_roles')
            ->columns(array(
            'id',
            'name'
        ));
        return $this->getRows($sql);
    }

    /**
     * Returns an array of all the created User Roles
     * 
     * @param string $view_type            
     * @return array
     */
    public function getAllRoles(array $where = array())
    {
        $sql = $this->db->select()->from('user_roles');
        if ($where) {
            $sql = $sql->where($where);
        }
        
        return $this->getRows($sql);
    }

    /**
     * Returuns all the users that belong to a role
     * 
     * @param int $id            
     * @return array
     */
    public function getUsersOnRole($id)
    {
        $sql = $this->db->select()
            ->from(array(
            'u' => 'users'
        ))
            ->join(array(
            'u2r' => 'user2role'
        ), 'u2r.user_id = u.id', array())
            ->where(array(
            'u2r.role_id' => $id
        ));
        return $this->getRows($sql);
    }

    /**
     * Returns all the permissions a given role has attached to it
     * 
     * @param int $id            
     * @return array
     */
    public function getRolePermissions($id, $return = 'keys')
    {
        $sql = $this->db->select()
            ->from(array(
            'p' => 'user_role_2_permissions'
        ), array(
            'p.*'
        ))
            ->where(array(
            'role_id' => $id
        ));
        if ($return == 'assoc') {
            $sql = $sql->join(array(
                'urp' => 'user_role_permissions'
            ), 'p.permission_id = urp.id', array(
                'name'
            ));
        }
        
        $perms = $this->getRows($sql);
        $p_arr = array();
        foreach ($perms as $p) {
            if ($return == 'keys') {
                $p_arr[] = $p['permission_id'];
            }
            
            if ($return == 'assoc') {
                $p_arr[$p['name']] = 1;
            }
        }
        return $p_arr;
    }

    /**
     * Returns all the permissions available to the system
     * 
     * @return array
     */
    public function getAllPermissions()
    {
        $sql = $this->db->select()->from('user_role_permissions');
        return $this->getRows($sql);
    }

    /**
     * Inserts or updates a user
     * 
     * @param
     *            $data
     * @param
     *            $bypass_update
     * @return mixed
     */
    public function addRole($data)
    {
        $ext = $this->trigger(self::EventUserRoleAddPre, $this, compact('data'), $this->setXhooks($data));
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $data = $ext->last();
        
        $perms = $this->getAllPermissions();
        $sql = $this->getSQL($data);
        $sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
        $role_id = $this->insert('user_roles', $sql);
        if ($role_id) {
            $this->addRolePermissions($data, $role_id);
            
            $ext = $this->trigger(self::EventUserRoleAddPost, $this, compact('role_id', 'data'), $this->setXhooks($data));
            if ($ext->stopped())
                return $ext->last();
            elseif ($ext->last())
                $entry_id = $ext->last();
            
            return $role_id;
        }
    }

    /**
     * Adds permissions to a role
     * 
     * @param array $data            
     * @param array $id            
     * @return boolean
     */
    public function addRolePermissions($data, $id)
    {
        $perms = $this->getAllPermissions();
        
        // remove old permissions
        $this->deleteRolePermissions($id);
        
        // add a new set
        foreach ($perms as $perm) {
            if (isset($data[$perm['name']]) && $data[$perm['name']] == '1') {
                // add the permission
                $insert = array(
                    'role_id' => $id,
                    'permission_id' => $perm['id']
                );
                $this->insert('user_role_2_permissions', $insert);
            }
        }
        
        return TRUE;
    }

    /**
     * Removes a role from a permission
     * 
     * @param unknown $role_id            
     * @return Ambigous <number, \Zend\EventManager\mixed, NULL, mixed>
     */
    public function deleteRolePermissions($role_id)
    {
        return $this->remove('user_role_2_permissions', array(
            'role_id' => $role_id
        ));
    }

    /**
     * Updates a Role
     * 
     * @param array $data            
     * @param int $id            
     * @return bool
     */
    public function updateRole($data, $id)
    {
        $ext = $this->trigger(self::EventUserRoleUpdatePre, $this, compact('data'), $this->setXhooks($data));
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $data = $ext->last();
        
        $sql = $this->getSQL($data);
        if ($this->update('user_roles', $sql, array(
            'id' => $id
        ))) {
            if ($this->addRolePermissions($data, $id)) {
                $ext = $this->trigger(self::EventUserRoleUpdatePost, $this, compact('data'), $this->setXhooks($data));
                if ($ext->stopped())
                    return $ext->last();
                elseif ($ext->last())
                    return true;
                
                return TRUE;
            }
        }
    }

    /**
     * Updates a users roleset
     * 
     * @param array $roles            
     * @param int $id            
     * @return bool
     */
    public function updateUsersRoles($user_id, array $roles)
    {
        $this->removeUsersRoles($user_id);
        foreach ($roles as $new_role) {
            $sql = array(
                'user_id' => $user_id,
                'role_id' => $new_role
            );
            $this->insert('user2role', $sql);
        }
        
        return TRUE;
    }

    /**
     * Removes a user from all groups
     * 
     * @param int $user_id            
     * @return number
     */
    public function removeUsersRoles($user_id)
    {
        return $this->remove('user2role', array(
            'user_id' => $user_id
        ));
    }

    /**
     * Handles everything for removing a role.
     * 
     * @param
     *            $id
     * @return bool
     */
    public function removeRole($role_id)
    {
        $ext = $this->trigger(self::EventUserRoleRemovePre, $this, compact('role_id'), $this->setXhooks(array()));
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $role_id = $ext->last();
        
        if ($this->remove('user_roles', array(
            'id' => $role_id
        ))) {
            $this->remove('user_role_2_permissions', array(
                'role_id' => $role_id
            ));
            
            $ext = $this->trigger(self::EventUserRoleRemovePost, $this, compact('role_id'), $this->setXhooks(array()));
            if ($ext->stopped())
                return $ext->last();
            elseif ($ext->last())
                $role_id = $ext->last();
            
            return $role_id;
        }
    }
}