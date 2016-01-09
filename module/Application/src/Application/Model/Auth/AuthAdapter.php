<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./modules/Application/src/Application/Model/Auth/AuthAdapter.php
 */
namespace Application\Model\Auth;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;

/**
 * Application - Authentication Adapter
 *
 * Runs the authentication logic and sets things up for storage
 *
 * @package Users\Authentication
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./modules/Application/src/Application/Model/Auth/AuthAdapter.php
 */
class AuthAdapter extends AbstractAdapter
{

    /**
     * The email to authenticate with
     * 
     * @var string
     */
    private $email;

    /**
     * The password to authenticate with
     * 
     * @var string
     */
    private $password;

    /**
     * Sets up the object
     * 
     * @param \Application\Model\Users $users            
     */
    public function __construct(\Application\Model\Users $users)
    {
        $this->users = $users;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\AuthAdapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        $authMessages = array();
        $data = $this->users->verifyCredentials($this->getIdentity(), $this->getCredential());
        if (! $data) {
            $authResult = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $authMessages[] = 'Not Found';
            return new AuthenticationResult($authResult, $this->email, $authMessages);
        }
        
        $authResult = AuthenticationResult::SUCCESS;
        return new AuthenticationResult($authResult, $data['id'], $authMessages);
    }
}