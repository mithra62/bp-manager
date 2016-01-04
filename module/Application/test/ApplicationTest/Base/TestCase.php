<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/test/ApplicationTest/Base/TestCase.php
 */

namespace ApplicationTest\Base;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Application - Base Test
 *
 * Sets up the unit testing objects for PHP Unit
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/test/ApplicationTest/Base/TestCase.php
 */
abstract class TestCase extends AbstractHttpControllerTestCase
{
	public $credentials = array(
		'email' => 'phpunit@mojitrac.com', 
		'password' => '12345', 
		'identity' => '19'
	);
	
	/**
	 * Should errors be traced for output
	 * @var bool
	 */
	protected $traceError = true;
	
	public function setUp()
	{
		$this->setApplicationConfig(
				include 'D:\ProjectFiles\mithra62\moji2\config/application.config.php'
		);
		parent::setUp();
		$this->serviceManager = $this->getApplicationServiceLocator();
	}	
}