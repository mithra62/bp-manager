<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Users.php
 */
namespace Api\Model;

use Application\Model\Users as AppUsers;

/**
 * Api - Users Model
 *
 * @package Users\Rest
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Model/Users.php
 */
class Users extends AppUsers
{

    /**
     * Determines wheher we should filter results based on REST output
     * 
     * @var bool
     */
    private $filter = TRUE;

    /**
     * The REST output for the users db table
     * 
     * @var array
     */
    public $usersOutputMap = array(
        'id' => 'id',
        'email' => 'email',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'phone_mobile' => 'phone_mobile',
        'phone_home' => 'phone_home',
        'phone_work' => 'phone_work',
        'phone_fax' => 'phone_fax',
        'job_title' => 'job_title',
        'description' => 'description',
        'user_status' => 'status'
    );

    /**
     * The REST output for the user_roles db table
     * 
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
     * 
     * @see \Application\Model\Users::getAllUsers()
     */
    public function getAllUsers($status = FALSE)
    {
        $users = parent::getAllUsers($status);
        $total_results = $this->getTotalResults();
        if (count($users) >= 1) {
            $return = array(
                'data' => $users,
                'total_results' => (int) $total_results,
                'total' => count($users),
                'page' => (int) $this->getPage(),
                'limit' => $this->getLimit()
            );
            
            return $return;
        }
    }

    /**
     * Takes the passed $roles array and returns only valid user groups ids
     * 
     * @param array $roles            
     * @return array
     */
    public function filterUserRoles(array $roles)
    {
        $user_roles = $this->roles->getAllRoles();
        $return = array();
        foreach ($user_roles as $user_role) {
            if (in_array($user_role['id'], $roles)) {
                $return[] = $user_role['id'];
            }
        }
        
        return $return;
    }
}