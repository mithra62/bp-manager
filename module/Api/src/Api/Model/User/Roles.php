<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Roles.php
 */
namespace Api\Model\User;

use Application\Model\User\Roles as PmRoles;

/**
 * Api - User Roles Model
 *
 * @package Users\Roles\Rest
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Model/Roles.php
 */
class Roles extends PmRoles
{

    /**
     * The REST output for the user_roles db table
     * 
     * @var array
     */
    public $userRolesOutputMap = array(
        'id' => 'role_id',
        'id' => 'id',
        'name' => 'name',
        'description' => 'description'
    );

    public $rolePermissionOutputMap = array(
        'name' => 'permission'
    );

    /**
     * (non-PHPdoc)
     * 
     * @see \Application\Model\Roles::getAllRoles()
     */
    public function getAllRoles(array $where = array())
    {
        $roles = parent::getAllRoles();
        $total_results = $this->getTotalResults();
        if (count($roles) >= 1) {
            $return = array(
                'data' => $roles,
                'total_results' => (int) $total_results,
                'total' => count($roles),
                'page' => (int) $this->getPage(),
                'limit' => $this->getLimit()
            );
            
            return $return;
        }
    }
}