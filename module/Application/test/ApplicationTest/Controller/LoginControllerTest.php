<?php
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/test/ApplicationTest/Controller/LoginControllerTest.php
 */
namespace ApplicationTest\Controller;

use ApplicationTest\Base\TestCase;
use Zend\Dom\Query;

/**
 * Application - Login Test Controller
 *
 * Tests the LoginController functionality
 *
 * @package mithra62:Mojitrac
 * @author Eric Lamb
 * @filesource ./module/Application/test/ApplicationTest/Controller/LoginControllerTest.php
 */
class LoginControllerTest extends TestCase
{

    /**
     * Verifies the index action can be accessed
     */
    public function testLoginActionCanBeAccessedAndSetupCorrectly()
    {
        $this->dispatch('/login');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Application');
        $this->assertControllerName('Application\Controller\Login');
        $this->assertControllerClass('LoginController');
        $this->assertActionName('index');
        $this->assertMatchedRouteName('login');
    }

    /**
     * @depends testLoginActionCanBeAccessedAndSetupCorrectly
     */
    public function testLoginActionFailsFromBadEmail()
    {
        $this->dispatch('/login');
        $body = $this->getResponse()->getBody();
        
        $params = array(
            'email' => 'test',
            'password' => 'fdsafdsa'
        );
        
        $this->dispatch('/login', 'POST', $params);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        // $this->assertQueryContentContains('li.error[0]', 'not a valid');
    }

    /**
     * @depends testLoginActionFailsFromBadEmail
     */
    public function testLoginActionGoodCredentials()
    {
        $this->dispatch('/login', 'GET');
        $html = $this->getResponse()->getBody();
        $dom = new Query($html);
        $csrf = $dom->execute('input[name="_x"]')
            ->current()
            ->getAttribute('value');
        
        $params = array(
            'email' => $this->credentials['email'],
            'password' => $this->credentials['password'],
            '_x' => $csrf
        );
        
        // $this->reset(true);
        // $this->dispatch('/login', 'POST', $params);
        
        /**
         * $html = $this->getResponse()->getBody();
         * echo $csrf;
         * echo $html;
         * exit;
         */
        $this->assertResponseStatusCode(200);
        
        // $this->assertRedirect();
        // $this->assertQueryContentContains('ul.errors li', 'not a valid');
    }
}