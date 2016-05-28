<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./modules/Application/src/Application/Model/Auth/AuthStorage.php
 */
namespace Application\Model\Auth;

use Zend\Authentication\Storage;

/**
 * Application - Authentication Storage
 *
 * Customizes the storage object so we can do the remember me logic
 *
 * @package Users\Authentication
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./modules/Application/src/Application/Model/Auth/AuthStorage.php
 */
class AuthStorage extends Storage\Session
{

    /**
     * Sets the session configuration object
     * 
     * @param \Zend\Session\Config\ConfigInterface $config            
     */
    public function setConfig(\Zend\Session\Config\ConfigInterface $config)
    {
        $this->session->getManager()->setConfig($config);
        return $this;
    }

    /**
     * Sets the logged in timeout
     * 
     * @param number $rememberMe            
     * @param number $time            
     * @return void
     */
    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
        if ($rememberMe == 1) {
            $this->session->getManager()->rememberMe($time);
        }
        return $this;
    }

    /**
     * Clears up the session so things are all logged out
     * 
     * @return void
     */
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
        return $this;
    }
}