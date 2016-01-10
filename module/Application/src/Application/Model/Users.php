<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Users.php
 */
namespace Application\Model;

use Zend\Db\Sql\Sql;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;
use Application\Model\Hash;

/**
 * Application - User Model
 *
 * @package Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/Users.php
 */
class Users extends AbstractModel
{

    /**
     * Contains all the permissions
     * 
     * @var array
     */
    public static $permissions = false;

    /**
     * The validation filter for the password form
     * 
     * @var object
     */
    protected $passwordInputFilter;

    /**
     * The validation filter for the user roles form
     * 
     * @var object
     */
    protected $rolesInputFilter;

    /**
     * The validation filter for the user registration form
     * 
     * @var object
     */
    protected $registrationInputFilter;

    /**
     * The Roles Model
     * 
     * @var \Application\Model\Roles
     */
    public $roles = null;

    /**
     * The User Model
     * 
     * @param \Zend\Db\Adapter\Adapter $adapter            
     * @param Sql $db            
     * @param \Application\Model\Roles $roles            
     * @param \Application\Model\User\UserData $user_data            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db, \Application\Model\Roles $roles, \Application\Model\User\UserData $user_data)
    {
        parent::__construct($adapter, $db);
        $this->roles = $roles;
        $this->user_data = $user_data;
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
            'email' => (! empty($data['email']) ? $data['email'] : ''),
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
     * Change Password specific validation logic
     * 
     * @param int $identity            
     * @param \Application\Model\Hash $hash            
     * @param bool $confirm            
     * @return object
     */
    public function getPasswordInputFilter($identity, \Application\Model\Hash $hash, $confirm = TRUE)
    {
        if (! $this->passwordInputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            if ($confirm) {
                $inputFilter->add($factory->createInput(array(
                    'name' => 'old_password',
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
                            'name' => '\Application\Validate\Password\Match',
                            'options' => array(
                                'identity' => $identity,
                                'users' => $this,
                                'hash' => $hash
                            )
                        )
                    )
                )));
            }
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'new_password',
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
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'confirm_password',
                            'strict' => FALSE
                        )
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'confirm_password',
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
            
            $this->passwordInputFilter = $inputFilter;
        }
        
        return $this->passwordInputFilter;
    }

    /**
     * Sets the Input Filter for the registration form
     * 
     * @return object
     */
    public function getRegistrationInputFilter()
    {
        if (! $this->registrationInputFilter) {
            
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
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'users',
                            'field' => 'email',
                            'adapter' => $this->adapter
                        )
                    )
                )
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'password',
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
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'confirm_password',
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
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password',
                            'strict' => FALSE
                        )
                    )
                )
            )));
            
            $this->registrationInputFilter = $inputFilter;
        }
        
        return $this->registrationInputFilter;
    }

    /**
     * Sets the Input Filter for the registration form
     * 
     * @return object
     */
    public function getEditInputFilter()
    {
        if (! $this->registrationInputFilter) {
            
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'first_name',
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
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'last_name',
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
            
            $this->registrationInputFilter = $inputFilter;
        }
        
        return $this->registrationInputFilter;
    }

    /**
     * Returns the InputFilter for validation
     * 
     * @return object
     */
    public function getRolesInputFilter()
    {
        if (! $this->rolesInputFilter) {
            
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'user_roles',
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
            
            $this->rolesInputFilter = $inputFilter;
        }
        
        return $this->rolesInputFilter;
    }

    /**
     * Changes a users password
     * 
     * @param int $id            
     * @param string $password            
     * @return Ambigous <\Zend\Db\Adapter\Driver\StatementInterface, \Zend\Db\ResultSet\Zend\Db\ResultSet, \Zend\Db\Adapter\Driver\ResultInterface, \Zend\Db\ResultSet\Zend\Db\ResultSetInterface>
     */
    public function changePassword($id, $password)
    {
        $hash = new Hash();
        $salt = $hash->genSalt();
        $sql = array(
            'hash' => $salt,
            'password' => $hash->password($password, $salt),
            'pw_forgotten' => null,
            'forgotten_hash' => null
        );
        return $this->update('users', $sql, array(
            'id' => $id
        ));
    }

    /**
     * Verifies that the provided credentials are accurate after salting, hashing and db checking.
     * 
     * @param unknown $key            
     * @param unknown $password            
     * @param string $col            
     * @return array
     */
    public function verifyCredentials($key, $password, $col = 'email')
    {
        $salt = $this->getHash($key, $col);
        $hash = new Hash();
        $sql = $this->db->select()->from(array('u' => 'users'))
                    ->columns(array('id'))
                    ->where(array($col => $key))
                    ->where(array('password' => $hash->password($password, $salt)));
        return $this->getRow($sql);
    }

    /**
     * Returns the hash field for password comparisons
     * 
     * @param string $key            
     * @param string $col            
     */
    public function getHash($key, $col = 'id')
    {
        $sql = $this->db->select()
            ->from(array(
            'u' => 'users'
        ))
            ->columns(array(
            'hash'
        ))
            ->where(array(
            $col => $key
        ));
        $hash = $this->getRow($sql);
        if (array_key_exists('hash', $hash)) {
            return $hash['hash'];
        }
    }

    /**
     * Returns a user array by email address
     * 
     * @param string $email            
     */
    public function getUserByEmail($email)
    {
        $sql = $this->db->select()
            ->from('users')
            ->where(array(
            'email' => $email
        ));
        return $this->getRow($sql);
    }

    /**
     * Returns a user array by password hash
     * 
     * @param unknown $hash            
     * @param string $expired            
     * @return array
     */
    public function getUserByPwHash($hash, $expired = TRUE)
    {
        $sql = $this->db->select()
            ->from(array(
            'u' => 'users'
        ))
            ->where(array(
            'forgotten_hash' => $hash
        ));
        if ($expired) {
            $where = $sql->where->greaterThan('pw_forgotten', date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), date("d") - 1, date("Y"))))->where->lessThan('pw_forgotten', date('Y-m-d H:i:s'));
            $sql = $sql->where($where);
        }
        
        return $this->getRow($sql);
    }

    /**
     * Returns an individual user array
     * 
     * @param int $id            
     * @return array
     */
    public function getUserById($id)
    {
        $sql = $this->db->select()->from(array(
            'u' => 'users'
        ));
        $sql = $sql->where(array(
            'u.id' => $id
        ));
        return $this->getRow($sql);
    }

    /**
     * Returns an array of all user names
     * 
     * @return mixed
     */
    public function getAllUsersNames()
    {
        $sql = $this->db->select()->from($this->db->getTableName(), array(
            'id',
            'name'
        ));
        return $this->db->getCompanies($sql);
    }

    /**
     * Returns all the system users
     * 
     * @param string $status            
     * @return array
     */
    public function getAllUsers($status = FALSE)
    {
        $sql = $this->db->select()->from('users');
        
        if ($status != '') {
            $sql = $sql->where(array(
                'user_status' => $status
            ));
        }
        
        return $this->getRows($sql);
    }

    /**
     * Creates a member
     * 
     * @param array $data            
     * @param \Application\Model\Hash $hash            
     * @return int
     */
    public function addUser(array $data, \Application\Model\Hash $hash)
    {
        $ext = $this->trigger(self::EventUserAddPre, $this, compact('data'), $this->setXhooks($data));
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $data = $ext->last();
        
        $sql = $this->getSQL($data);
        $sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
        $sql['hash'] = $hash->genSalt();
        $sql['password'] = $hash->password($data['password'], $sql['hash']);
        
        $user_id = $data['user_id'] = $this->insert('users', $sql);
        if ($user_id) {
            if (isset($data['user_roles'])) {
                $this->roles->updateUsersRoles($user_id, $data['user_roles']);
            }
            
            $ext = $this->trigger(self::EventUserAddPost, $this, compact('user_id', 'data'), $this->setXhooks($data));
            if ($ext->stopped())
                return $ext->last();
            elseif ($ext->last())
                $user_id = $ext->last();
            
            return $user_id;
        }
    }

    /**
     * Updates a user
     * 
     * @param array $data            
     * @param int $id            
     * @return bool
     */
    public function updateUser($data, $user_id)
    {
        $ext = $this->trigger(self::EventUserUpdatePre, $this, compact('data', 'user_id'), $this->setXhooks($data));
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $data = $ext->last();
        
        $sql = $this->getSQL($data);
        if ($this->update('users', $sql, array(
            'id' => $user_id
        ))) {
            if (isset($data['user_roles'])) {
                $this->roles->updateUsersRoles($user_id, $data['user_roles']);
            }
            
            $ext = $this->trigger(self::EventUserUpdatePost, $this, compact('user_id', 'data'), $this->setXhooks($data));
            if ($ext->stopped())
                return $ext->last();
            elseif ($ext->last())
                $user_id = $ext->last();
            
            return $user_id;
        }
    }

    /**
     * Updates the login time for the user
     * 
     * @param int $id            
     */
    public function upateLoginTime($id)
    {
        $sql = array(
            'last_login' => new \Zend\Db\Sql\Expression("NOW()"),
            'last_modified' => new \Zend\Db\Sql\Expression("NOW()")
        );
        $where = array(
            'id' => $id
        );
        return $this->update('users', $sql, $where);
    }

    /**
     * Updates the password hash for the user
     * 
     * @param int $id            
     */
    public function upatePasswordHash($id, $hash)
    {
        $sql = array(
            'pw_forgotten' => new \Zend\Db\Sql\Expression("NOW()"),
            'forgotten_hash' => $hash,
            'last_modified' => new \Zend\Db\Sql\Expression("NOW()")
        );
        
        $where = array(
            'id' => $id
        );
        return $this->update('users', $sql, $where);
    }

    /**
     * Updates the Roles a $user_id is asssociated with
     * 
     * @param int $user_id            
     * @param array $roles            
     * @return boolean
     */
    public function updateUserRoles($user_id, array $roles)
    {
        return $this->roles->updateUsersRoles($user_id, $roles);
    }

    /**
     * Handles everything for removing a user.
     * 
     * @param
     *            $user_id
     * @return int
     */
    public function removeUser($user_id)
    {
        $ext = $this->trigger(self::EventUserRemovePre, $this, compact('user_id'), array());
        if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $user_id = $ext->last();
        
        if ($this->remove('users', array('id' => $user_id))) {
            
            $ext = $this->trigger(self::EventUserRemovePost, $this, compact('user_id'), array());
            if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $user_id = $ext->last();
            
            return $user_id;
        }
        
    }

    /**
     * Returns all the roles a user is attached to
     * 
     * @param int $id            
     * @return array
     */
    public function getUserRoles($id)
    {
        $sql = $this->db->select()->from(array(
            'r' => 'user_roles'
        ), 'r.*');
        $sql = $sql->join(array(
            'u2r' => 'user2role'
        ), 'u2r.role_id = r.id');
        $sql = $sql->where(array(
            'u2r.user_id' => $id
        ));
        return $this->getRows($sql);
    }

    /**
     * Returns the roles a user $id is attached to as a simple, one dimentional, array
     * 
     * @param int $id            
     * @return array
     */
    public function getUserRolesArr($id)
    {
        $sql = $this->db->select()
            ->from(array(
            'r' => 'user_roles'
        ))
            ->columns(array(
            'id' => 'id'
        ));
        $sql = $sql->join(array(
            'u2r' => 'user2role'
        ), 'u2r.role_id = r.id ')->where(array(
            'u2r.user_id' => $id
        ));
        $user_roles = $this->getRows($sql);
        $return = array();
        foreach ($user_roles as $role) {
            $return[] = $role['id'];
        }
        return $return;
    }

    /**
     * Determines whether a user has a preference set
     * 
     * @param int $id            
     * @param string $pref            
     * @param string $default            
     * @return unknown|string
     */
    public function checkPreference($id, $pref, $default = FALSE)
    {
        $data = $this->user_data->getUsersData($id);
        if ($data) {
            if (isset($data[$pref])) {
                return $data[$pref];
            } else {
                return $data[$pref] = $default;
            }
        }
    }
    
    /**
     * Sends the user welcome email
     * @param int $user_id The user ID for the member we're contacting
     * @param Mail $mail The Mail object
     * @return void
     */
    public function sendWelcomeEmail($user_id, Mail $mail)
    {
        $data = $this->getUserById($user_id);
        $mail->addTo($data['email']);
        $mail->setEmailView('user-registration', array(
            'user_data' => $data,
            'user_id' => $user_id
        ));
        
        $mail->setSubject('user_registration_email_subject');
        $mail->send();
    }
    
    public function sendVerifyEmail($user_id, Mail $mail, Hash $hash)
    {
        $guid = $hash->guidish();
        $user_data = $this->getUserById($user_id);
        if (! $user_data) {
            return FALSE;
        }
        
        if ($this->upateVerifyHash($user_id, $guid)) {
            $change_url = $mail->web_url . '/account/verify/'.$guid;
            $mail->addTo($user_data['email']);
            $mail->setViewDir($this->getModulePath(__DIR__) . '/view/emails');
            $mail->setEmailView('forgot-password', array(
                'change_url' => $change_url,
                'user_data' => $user_data
            ));
            $mail->addTo($user_data['email']);
            $mail->setSubject('verify_email_email_subject');
            return $mail->send($mail->transport);
        }
    }

    /**
     * Updates the verify hash for the user
     * 
     * @param int $id
     * @param string $hash
     */
    public function upateVerifyHash($id, $hash)
    {
        $sql = array(
            'verified_sent_date' => new \Zend\Db\Sql\Expression("NOW()"),
            'verified_hash' => $hash,
            'last_modified' => new \Zend\Db\Sql\Expression("NOW()")
        );
        
        $where = array(
            'id' => $id
        );
        return $this->update('users', $sql, $where);
    }
}