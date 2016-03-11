<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Login.php
 */
namespace Application\Model\User;

use Zend\Db\Sql\Sql;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Authentication\Result as AuthenticationResult;
use Application\Model\AbstractModel;

/**
 * Application - Login Model
 *
 * Handles login functionality
 *
 * @package Users\Login
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/Login.php
 */
class Login extends AbstractModel
{

    /**
     * The validation filters
     * 
     * @var object
     */
    protected $inputFilter;

    /**
     * The athentication adaptor
     * 
     * @var object
     */
    private $authAdapter;

    /**
     * The Login Model
     * 
     * @param \Zend\Db\Adapter\Adapter $adapter            
     * @param Sql $db            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db)
    {
        parent::__construct($adapter, $db);
    }

    public function setAuthAdapter(\Zend\Db\Adapter\Adapter $auth)
    {
        $this->authAdapter = $auth;
    }
    
    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Sets up the inputFilter object
     * 
     * @return object
     */
    public function getInputFilter($translator)
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
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => $translator('email_required', 'app')
                            ),
                        ),
                    ),  
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'emailAddressInvalidFormat' => $translator('invalid_email_address', 'app')
                            ),
                        ),
                    ),                  
                    array(
                        'name' => 'Db\RecordExists',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'table' => 'users',
                            'field' => 'email',
                            'adapter' => $this->authAdapter,
                            'messages' => array(
                                'noRecordFound' => $translator('enter_registered_email', 'app')
                            )
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
                ),
                'validators' => array(
                    
                    array(
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => $translator('password_required', 'app')
                            ),
                        ),
                    )
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    /**
     * Processes the User Login routine
     * 
     * @param string $email            
     * @param string $password            
     * @param \Zend\Authentication\AuthenticationService $adapter            
     * @return \Zend\Authentication\Result
     */
    public function procLogin($email, $password, \Zend\Authentication\AuthenticationService $adapter)
    {
        $credentials = compact('email', 'password');
        $ext = $this->trigger(self::EventUserLoginPre, $this, $credentials);
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $credentials = $ext->last();
        
        $adapter->getAdapter()
            ->setIdentity($credentials['email'])
            ->setCredential($credentials['password']);
        $check = $adapter->authenticate();
        
        $result_code = $check->getCode();
        if ($result_code == AuthenticationResult::SUCCESS) {
            $ext = $this->trigger(self::EventUserLoginPost, $this, array(
                'result_code' => $result_code
            ));
            if ($ext->stopped())
                return $ext->last();
            elseif ($ext->last())
                $result_code = $ext->last();
        }
        
        return $result_code;
    }

    /**
     * Processes the User Logout routine
     * 
     * @param \Application\Model\Auth\AuthStorage $storage            
     * @param \Zend\Authentication\AuthenticationService $auth            
     * @return void
     */
    public function logout(\Application\Model\Auth\AuthStorage $storage, \Zend\Authentication\AuthenticationService $auth)
    {
        $ext = $this->trigger(self::EventUserLogoutPre, $this, array());
        if ($ext->stopped())
            return $ext->last();
        
        $storage->forgetMe();
        $auth->clearIdentity();
        
        $ext = $this->trigger(self::EventUserLoginPost, $this, array());
        if ($ext->stopped())
            return $ext->last();
    }
}