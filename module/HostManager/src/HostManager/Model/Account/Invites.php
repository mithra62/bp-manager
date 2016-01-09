<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Invites.php
 */
namespace HostManager\Model\Account;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;
use HostManager\Traits\Account;

/**
 * HostManager - Invites Model
 *
 * @package HostManager
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/Model/Invites.php
 */
class Invites extends AbstractModel
{

    const EventAddAccountInvitePre = 'invite.add.pre';

    const EventAddAccountInvitePost = 'invite.add.post';

    const EventAcceptAccountInvitePre = 'invite.accept.pre';

    const EventAcceptAccountInvitePost = 'invite.accept.post';
    
    /**
     * Setup the Account Trait
     */
    use Account;

    /**
     * Prepares the SQL array for the accounts table
     * 
     * @param array $data            
     * @return array
     */
    public function getSQL(array $data)
    {
        return array(
            'user_id' => $data['user_id'],
            'account_id' => $data['account_id'],
            'verification_hash' => $data['verification_hash'],
            'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
        );
    }

    /**
     *
     * @ignore
     *
     * @param InputFilterInterface $inputFilter            
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Returns an instance of the InputFilter for data validation
     * 
     * @param int $identity
     *            The user ID
     * @param string $match_col
     *            The column we want to restrict matching to
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter($identity, \Application\Model\Users $user, $match_col = 'email')
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    ),
                    array(
                        'name' => '\Application\Validate\User\PreventSelfEmail',
                        'options' => array(
                            'identity' => $identity,
                            'invite' => $this,
                            'user' => $user
                        )
                    )
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    /**
     * Creates an Invite to Account in the system
     * 
     * @param int $user_id            
     * @param \Application\Model\Hash $hash            
     * @param string $account_id            
     */
    public function addInvite($user_id, \Application\Model\Hash $hash, $account_id = false)
    {
        if (! $account_id) {
            $account_id = $this->getAccountId();
        }
        
        $data = array(
            'user_id' => $user_id,
            'account_id' => $account_id,
            'verification_hash' => $hash->guidish()
        );
        
        $ext = $this->trigger(self::EventAddAccountInvitePre, $this, compact('data'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $data = $ext->last();
        
        $data = $this->getSQL($data);
        $data['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
        $invite_id = $this->insert('account_invites', $data);
        
        $ext = $this->trigger(self::EventAddAccountInvitePost, $this, compact('invite_id'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $invite_id = $ext->last();
        
        return $invite_id;
    }

    /**
     * Returns details about an Invite
     * 
     * @param array $where            
     */
    public function getInvite(array $where = array())
    {
        $sql = $this->db->select()
            ->from(array(
            'ai' => 'account_invites'
        ))
            ->join(array(
            'u' => 'users'
        ), 'u.id = user_id', array(
            'first_name',
            'last_name',
            'email'
        ));
        if ($where) {
            $sql = $sql->where($where);
        }
        
        return $this->getRow($sql);
    }

    /**
     * Returns an account's invites
     * 
     * @param int $account_id            
     * @param array $where            
     * @return array
     */
    public function getAccountInvites($account_id = false, array $where = array())
    {
        if (! $account_id) {
            $account_id = $this->getAccountId();
        }
        
        $columns = array(
            'user_id',
            'total_invites' => new \Zend\Db\Sql\Expression('COUNT(ai.id)'),
            'last_sent' => new \Zend\Db\Sql\Expression('MAX(ai.created_date)')
        );
        $sql = $this->db->select()
            ->columns($columns)
            ->from(array(
            'ai' => 'account_invites'
        ))
            ->where(array(
            'account_id' => $account_id
        ))
            ->join(array(
            'u' => 'users'
        ), 'u.id = user_id', array(
            'first_name',
            'last_name',
            'email'
        ))
            ->group('user_id');
        if ($where) {
            $sql = $sql->where($where);
        }
        
        return $this->getRows($sql);
    }

    /**
     * Creats the URL for the Invite to Account system
     * 
     * @param string $code            
     * @param string $account_id            
     */
    public function createInviteUrl($code, $account_id = false)
    {
        if (! $account_id) {
            $account_id = $this->getAccountId();
        }
    }

    /**
     * Wrapper to approve an Invite code and attach a user to an account
     * 
     * @param string $code            
     * @return bool
     */
    public function approveCode($code)
    {
        $invite_data = $this->getInvite(array(
            'verification_hash' => $code
        ));
        
        $ext = $this->trigger(self::EventAcceptAccountInvitePre, $this, compact('invite_data'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $invite_data = $ext->last();
        
        if ($this->linkUserToAccount($invite_data['user_id'], $invite_data['account_id'])) {
            $this->remove('account_invites', array(
                'user_id' => $invite_data['user_id'],
                'account_id' => $invite_data['account_id']
            ));
            
            $ext = $this->trigger(self::EventAcceptAccountInvitePost, $this, compact('invite_data'), array());
            if ($ext->stopped())
                return $ext->last();
            elseif ($ext->last())
                $invite_data = $ext->last();
            
            return true;
        }
    }

    /**
     * Removes user invites
     * 
     * @param array $where            
     * @return int
     */
    public function removeInvites($user_id, $account_id)
    {
        $where = array(
            'user_id' => $user_id,
            'account_id' => $account_id
        );
        if ($this->remove('account_invites', $where)) {
            $this->remove('user2role', $where);
            return true;
        }
    }
}