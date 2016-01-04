<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/Module.php
 */

namespace Freshbooks;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;

use Freshbooks\Model\Credentials;
use Freshbooks\Form\CredentialsForm;

use Freshbooks\Event\SettingsEvent;
use Freshbooks\Event\ViewEvent;

/**
 * Freshbooks - Module Object
 *
 * @package 	MojiTrac
 * @author		Eric Lamb
 * @filesource 	./module/Freshbooks/Module.php
 */
class Module implements 
    ConsoleUsageProviderInterface,
    ConsoleBannerProviderInterface
{
	/**
	 * Sets up the module layout
	 * @param ModuleManager $moduleManager
	 */
	public function init(ModuleManager $moduleManager)
	{
		//sets the layout
		$this->sharedEvents = $moduleManager->getEventManager()->getSharedManager();
		$this->sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
			$controller = $e->getTarget();
			$controller->layout('layout/pm');
		}, 100);
			
	}

	/**
	 * Setup the Events we're gonna piggyback on
	 *
	 * Note, we have to implement the other module events since we can't extend the Base\Controller
	 *
	 * @param \Zend\Mvc\MvcEvent $e
	 * @todo Abstract the registering of events
	 */	
	public function onBootstrap(\Zend\Mvc\MvcEvent $e)
	{
		$event = $e->getApplication()->getServiceManager()->get('Freshbooks\Event\SettingsEvent');
		$event->register($this->sharedEvents);

		$event = $e->getApplication()->getServiceManager()->get('Freshbooks\Event\ViewEvent');
		$event->register($this->sharedEvents);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface::getConsoleUsage()
	 * @todo Add actual routes
	 */
	public function getConsoleUsage(Console $console)
	{
		return array();
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ModuleManager\Feature\ConsoleBannerProviderInterface::getConsoleBanner()
	 */
	public function getConsoleBanner(Console $console)
	{
		return 'Freshbooks 2.X';
	}	
	
	/**
	 * @ignore
	 */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @ignore
     * @return multitype:multitype:multitype:string
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }    
    
    /**
     * @ignore
     * @return multitype:multitype:NULL  |\Freshbooks\Credentials|\Freshbooks\Form\CredentialsForm
     */
    public function getServiceConfig()
    {
    	return array(
			'factories' => array(
					
				//models
				'Freshbooks\Model\Credentials' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Credentials($adapter, $db);
				},
				
				//forms
				'Freshbooks\Form\CredentialsForm' => function($sm) {
					return new CredentialsForm('freshbooks_credentials_form');
				},				
				
				//events
				'Freshbooks\Event\SettingsEvent' => function($sm) {
					return new SettingsEvent();
				},
				'Freshbooks\Event\ViewEvent' => function($sm) {
					return new ViewEvent();
				},								
			),
    	);
    } 
    
    
}
