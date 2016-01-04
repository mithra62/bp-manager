<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/test/ApplicationTest/Controller/ForgotPasswordTest.php
 */

namespace ApplicationTest\Controller;

use ApplicationTest\Base\TestCase;

/**
 * Application - Forgot Password Test Controller
 *
 * Tests the ForgotPassword functionality
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/test/ApplicationTest/Controller/ForgotPasswordTest.php
 */
class ForgotPasswordControllerTest extends TestCase
{   
    public function testIndexActionCanBeAccessed()
    {
    	$this->dispatch('/forgot-password');
    	$this->assertResponseStatusCode(200);    
    	$this->assertModuleName('Application');
    	$this->assertControllerName('Application\Controller\ForgotPassword');
    	$this->assertControllerClass('ForgotPasswordController');
    	$this->assertActionName('index');
    	$this->assertMatchedRouteName('forgot-password');
    }
     
    public function testResetActionNoHash()
    {
    	$this->dispatch('/forgot-password/reset');
    	$this->assertResponseStatusCode(404);
    }  
    
    public function testResetActionBadHash()
    {
    	$this->dispatch('/forgot-password/reset/fdsafdsa');
    	$this->assertResponseStatusCode(302);
    	$this->assertRedirectTo('/forgot-password');
    }    
}