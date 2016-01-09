<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/Module.php
 */
namespace HostManager;

use Zend\EventManager\EventInterface as Event;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use HostManager\Event\SqlEvent;
use HostManager\Event\NotificationEvent;
use HostManager\Event\ViewEvent;
use HostManager\Model\Accounts;
use HostManager\Model\Account\Invites;
use HostManager\Model\Users;
use HostManager\Form\SignUpForm;
use HostManager\Form\InviteForm;
use Application\Model\Cron;
use Zend\ModuleManager\ModuleManager;

/**
 * HostManager - Module Object
 *
 * @package HostManager
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/Module.php
 */
class Module implements ConsoleUsageProviderInterface, ConsoleBannerProviderInterface
{

    /**
     * Initializes the module
     * 
     * @param ModuleManager $moduleManager            
     */
    public function init(ModuleManager $moduleManager)
    {
        $this->sharedEvents = $moduleManager->getEventManager()->getSharedManager();
    }

    /**
     * Mostly we're just attaching events to the system
     * 
     * @param Event $e            
     */
    public function onBootstrap(Event $e)
    {
        $application = $e->getApplication();
        $this->service_manager = $application->getServiceManager();
        $sql_event = $this->service_manager->get('HostManager\Event\SqlEvent');
        $sql_event->register($this->sharedEvents);
        
        $event = $e->getApplication()
            ->getServiceManager()
            ->get('HostManager\Event\ViewEvent');
        $event->register($this->sharedEvents);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface::getConsoleUsage()
     */
    public function getConsoleUsage(Console $console)
    {
        return array(
            // Describe available commands
            'run account cron [--verbose|-v]',
            array(
                'Executes any pending Cron requests from hosted accounts'
            ),
            
            // Describe expected parameters
            array(
                '--verbose|-v',
                '(optional) turn on verbose mode'
            ),
            '---------------------------------------',
            '',
            '---------------------------------------'
        );
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ModuleManager\Feature\ConsoleBannerProviderInterface::getConsoleBanner()
     */
    public function getConsoleBanner(Console $console)
    {
        return 'Host Manager 2.X';
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        $local_config = __DIR__ . '/config/module.local.config.php';
        if (file_exists($local_config)) {
            $local_config = include $local_config;
            $config = array_merge($config, $local_config);
        }
        return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                
                // models
                'HostManager\Model\Accounts' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $config = $sm->get('Config');
                    $account = new Accounts($adapter, $db);
                    $account->setConfig($config);
                    return $account;
                },
                'HostManager\Model\Account\Invites' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $config = $sm->get('Config');
                    $invite = new Invites($adapter, $db);
                    $invite->setConfig($config);
                    return $invite;
                },
                'HostManager\Model\Users' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $account = $sm->get('HostManager\Model\Accounts');
                    $roles = $sm->get('Application\Model\Roles');
                    $ud = $sm->get('Application\Model\UserData');
                    $db = $sm->get('SqlObject');
                    $user = new Users($adapter, $db, $roles, $ud);
                    $user->setAccount($account);
                    return $user;
                },
                'Crons' => function ($sm) {
                    $db = $sm->get('SqlObject');
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $cron = new Cron($adapter, $db);
                    $path = realpath(__DIR__ . '/src/' . __NAMESPACE__ . '/Cron');
                    $cron->setNamespace(__NAMESPACE__)->setPath($path);
                    return $cron;
                },
                
                // forms
                'HostManager\Form\SignUpForm' => function ($sm) {
                    return new SignUpForm('signup_form');
                },
                'HostManager\Form\InviteForm' => function ($sm) {
                    return new InviteForm('invite_form');
                },
                
                // events
                'HostManager\Event\SqlEvent' => function ($sm) {
                    $auth = $sm->get('AuthService');
                    $config = $sm->get('Config');
                    $account = $sm->get('HostManager\Model\Accounts');
                    
                    $sqlEvent = new SqlEvent($auth->getIdentity(), $account, $config);
                    return $sqlEvent;
                },
                'PM\Event\NotificationEvent' => function ($sm) {
                    $auth = $sm->get('AuthService');
                    $mail = $sm->get('Application\Model\Mail');
                    $user = $sm->get('PM\Model\Users');
                    $task = $sm->get('PM\Model\Tasks');
                    $project = $sm->get('PM\Model\Projects');
                    return new NotificationEvent($mail, $user, $project, $task, $auth->getIdentity());
                },
                'HostManager\Event\ViewEvent' => function ($sm) {
                    $auth = $sm->get('AuthService');
                    $user = $sm->get('Api\Model\Users');
                    $account = $sm->get('HostManager\Model\Accounts');
                    return new ViewEvent($auth->getIdentity(), $user, $account);
                }
            )
        );
    }
}
