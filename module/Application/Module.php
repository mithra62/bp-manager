<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link			http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/Module.php
 */
namespace Application;

use DateTime;
use Zend\Mvc\ModuleRouteListener;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\Db\Sql\Sql;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Config\StandardConfig;
use Intervention\Image\ImageManager;

use Application\Model\Auth\AuthAdapter;
use Application\Model\Users;
use Application\Model\User\Roles;
use Application\Model\User\UserData;
use Application\Model\User\Permissions;
use Application\Model\User\Login;
use Application\Model\User\ForgotPassword;
use Application\Model\Settings;
use Application\Model\Hash;
use Application\Model\Mail;
use Application\Model\ViewEvents;
use Application\Model\Image;
use Application\Model\Ips;

use Application\Form\ForgotPasswordForm;
use Application\Form\SettingsForm;
use Application\Form\LoginForm;
use Application\Form\PasswordForm;
use Application\Form\PrefsForm;
use Application\Form\UsersForm;
use Application\Form\RolesForm;
use Application\Form\User\RolesForm as UserRolesForm;
use Application\Form\ConfirmForm;
use Application\Form\EmailForm;
use Application\Form\IpForm;

use Application\Event\NotificationEvent;

/**
 * Application - Module Loader
 *
 * @package MojiTrac
 * @author Eric Lamb
 * @filesource ./module/Application/Module.php
 */
class Module
{

    /**
     * Sets up the module layout
     * 
     * @param ModuleManager $moduleManager            
     */
    public function init(ModuleManager $moduleManager)
    {
        $this->sharedEvents = $moduleManager->getEventManager()->getSharedManager();
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $event = $e->getApplication()
            ->getServiceManager()
            ->get('Application\Event\NotificationEvent');
        $event->register($this->sharedEvents);
    }

    /**
     *
     * @param \Zend\Mvc\MvcEvent $e
     *            The MvcEvent instance
     * @return void
     */
    public function setLayoutTitle($e)
    {
        $matches = $e->getRouteMatch();
        if (! $matches) {
            return;
        }
        
        $action = $matches->getParam('action');
        $controller = $matches->getParam('controller');
        $module = __NAMESPACE__;
        $siteName = 'MojiTrac';
        
        // Getting the view helper manager from the application service manager
        $viewHelperManager = $e->getApplication()
            ->getServiceManager()
            ->get('viewHelperManager');
        
        // Getting the headTitle helper from the view helper manager
        $headTitleHelper = $viewHelperManager->get('headTitle');
        
        // Setting a separator string for segments
        $headTitleHelper->setSeparator(' - ');
        
        // Setting the action, controller, module and site name as title segments
        $headTitleHelper->append($action);
        $headTitleHelper->append($controller);
        $headTitleHelper->append($module);
        $headTitleHelper->append($siteName);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
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
                
                // setting up the Authentication stuff
                'Application\Model\Auth\AuthStorage' => function ($sm) {
                    $storage = new \Application\Model\Auth\AuthStorage('mojitrac');
                    
                    $moji_config = $sm->get('Config');
                    $session_config = new StandardConfig();
                    $session_config->setOptions($moji_config['moji_session']);
                    
                    $storage->setConfig($session_config);
                    return $storage;
                },
                'AuthService' => function ($sm) {
                    $db = $sm->get('Zend\Db\Adapter\Adapter');
                    $user = $sm->get('Application\Model\Users');
                    
                    $authService = new AuthenticationService();
                    $authService->setAdapter(new AuthAdapter($user));
                    $authService->setStorage($sm->get('Application\Model\Auth\AuthStorage'));
                    return $authService;
                },
                'SqlObject' => function ($sm) {
                    $db = $sm->get('Zend\Db\Adapter\Adapter');
                    return new Sql($db);
                },
                
                // models
                'Application\Model\Users' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $roles = $sm->get('Application\Model\User\Roles');
                    $ud = $sm->get('Application\Model\User\Data');
                    return new Users($adapter, $db, $roles, $ud);
                },
                'Application\Model\Mail' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $settings = $sm->get('Application\Model\Settings');
                    $message = new \Zend\Mail\Message();
                    $mailer = new Mail($adapter, $db, $message, $settings->getSettings());
                    
                    $config = $sm->get('Config');
                    $sendmail = new \Zend\Mail\Transport\Sendmail();
                    $file = new \Zend\Mail\Transport\File();
                    $file_options = new \Zend\Mail\Transport\FileOptions($config['email_logging']['file_options']);
                    $file->setOptions($file_options);
                    $smtp = new \Zend\Mail\Transport\Smtp();
                    $mailer->setMailConfig($config)
                        ->setSendmailTransport($sendmail)
                        ->setFileTransport($file)
                        ->setSmtpTransport($smtp)
                        ->setViewHelpers($sm->get('ViewHelperManager'))
                        ->setTranslator($sm->get('viewhelpermanager')
                        ->get('_')
                        ->getTranslator());
                    return $mailer;
                },
                'Application\Model\User\Roles' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $permissions = $sm->get('Application\Model\User\Permissions');
                    return new Roles($adapter, $db, $permissions);
                },
                'Application\Model\User\Permissions' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new Permissions($adapter, $db);
                },
                'Application\Model\User\Login' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new Login($adapter, $db);
                },
                'Application\Model\User\ForgotPassword' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $users = $sm->get('Application\Model\Users');
                    return new ForgotPassword($adapter, $db, $users);
                },
                'Application\Model\ForgotPasswordForm' => function ($sm) {
                    return new ForgotPasswordForm('forgot_password');
                },
                'Application\Model\Settings' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new Settings($adapter, $db);
                },
                'Application\Model\User\Data' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new UserData($adapter, $db);
                },
                'Application\Model\Hash' => function ($sm) {
                    return new Hash();
                },
                'Application\Model\Image' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Image(new ImageManager(array(
                        'driver' => $config['image_handling']['driver']
                    )));
                },
                'Application\Model\ViewEvents' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new ViewEvents($adapter, $db);
                },
				'Application\Model\Ips' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Ips($adapter, $db);
				},
                
                // forms
                'Application\Form\SettingsForm' => function ($sm) {
                    return new SettingsForm('settings');
                },
                'Application\Form\PrefsForm' => function ($sm) {
                    return new PrefsForm('preferences');
                },
                'Application\Form\PasswordForm' => function ($sm) {
                    return new PasswordForm('password');
                },
                'Application\Form\LoginForm' => function ($sm) {
                    return new LoginForm('login');
                },
                'Application\Form\UsersForm' => function ($sm) {
                    return new UsersForm('user');
                },
                'Application\Form\RolesForm' => function ($sm) {
                    return new RolesForm('roles', $sm->get('Application\Model\Roles'));
                },
                'Application\Form\User\RolesForm' => function ($sm) {
                    return new UserRolesForm('roles', $sm->get('Application\Model\Roles'));
                },
				'Application\Form\ConfirmForm' => function($sm) {
					return new ConfirmForm('confirm');
				},
				'Application\Form\EmailForm' => function($sm) {
					return new EmailForm('email_change');
				},
				'Application\Form\IpForm' => function($sm) {
					return new IpForm('ip');
				},
                
                // events
                'Application\Event\NotificationEvent' => function ($sm) {
                    $auth = $sm->get('AuthService');
                    $mail = $sm->get('Application\Model\Mail');
                    $user = $sm->get('Application\Model\Users');
                    return new NotificationEvent($mail, $user, $auth->getIdentity());
                }
            )
        );
    }
}
