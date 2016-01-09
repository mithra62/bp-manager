<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Users.php
 */
namespace HostManager\Model;

use PM\Model\Users as PmUsers;

/**
 * HostManager - Users Model
 *
 * @package HostManager
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/Model/Users.php
 */
class Users extends PmUsers
{

    /**
     * The HostManager Account object
     * 
     * @var HostManager\Model\Accounts
     */
    private $account = null;

    /**
     * Sets the Account object for use
     * 
     * @param \HostManager\Model\Accounts $account            
     * @return \HostManager\Model\Users
     */
    public function setAccount(\HostManager\Model\Accounts $account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Returns the users on an account
     * 
     * @param string $status            
     */
    public function getAccountUsers($status = FALSE)
    {
        $sql = $this->db->select()->from('users');
        if ($status != '') {
            $sql = $sql->where(array(
                'user_status' => $status
            ));
        }
        
        $sql = $sql->where(array(
            'account_id' => $this->account->getAccountId()
        ))
            ->join(array(
            'ua' => 'user_accounts'
        ), 'ua.user_id = users.id', array());
        return $this->getRows($sql);
    }
}