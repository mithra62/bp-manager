<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/Module.php
 */
namespace Api;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\View\Model\JsonModel;
use Api\Model\Projects;
use Api\Model\Key;
use Api\Model\Tasks;
use Api\Model\Users;
use Api\Model\Companies;
use Api\Model\Options;
use Api\Model\Roles;
use Api\Event\UserDataEvent;
use Api\Event\ViewEvent;

/**
 * Api - Module Loader
 *
 * @package MojiTrac
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/Module.php
 */
class Module implements Feature\BootstrapListenerInterface
{

    /**
     * Sets up the module layout
     * 
     * @param ModuleManager $moduleManager            
     */
    public function init(ModuleManager $moduleManager)
    {
        // sets the layout
        $this->sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $this->sharedEvents->attach(__NAMESPACE__, 'dispatch', function ($e) {
            $controller = $e->getTarget();
            $controller->layout('layout/pm');
        }, 100);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ModuleManager\Feature\BootstrapListenerInterface::onBootstrap()
     */
    public function onBootstrap(EventInterface $e)
    {
        $event = $e->getApplication()
            ->getServiceManager()
            ->get('Api\Event\UserDataEvent');
        $event->register($this->sharedEvents);
        
        $event = $e->getApplication()
            ->getServiceManager()
            ->get('Api\Event\ViewEvent');
        $event->register($this->sharedEvents);
        
        // we have to work some magic to only use the Json ViewStrategy on the API module
        $app = $e->getApplication();
        $em = $app->getEventManager()->getSharedManager();
        $sm = $app->getServiceManager();
        $em->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function ($e) use($sm) {
            $strategy = $sm->get('ViewJsonStrategy');
            $view = $sm->get('ViewManager')
                ->getView();
            $strategy->attach($view->getEventManager());
        });
        
        $em->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR, function ($e) use($sm) {
            return $this->getJsonModelError($e);
        });
    }

    /**
     * Sets up the Module config and DI'd objects
     * 
     * @return multitype:multitype:NULL |\Api\Model\Projects|\Api\Model\Tasks|\Api\Model\Users|\Api\Model\Companies|\Api\Model\Options|\Api\Model\Roles
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                
                // models
                'Api\Model\Key' => function ($sm) {
                    $hash = $sm->get('Application\Model\Hash');
                    $user = $sm->get('Api\Model\Users');
                    return new Key($hash, $user);
                },
                'Api\Model\Projects' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new Projects($adapter, $db);
                },
                'Api\Model\Tasks' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new Tasks($adapter, $db);
                },
                'Api\Model\Users' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $role = $sm->get('Api\Model\Roles');
                    $ud = $sm->get('Application\Model\User\Data');
                    return new Users($adapter, $db, $role, $ud);
                },
                'Api\Model\Companies' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new Companies($adapter, $db);
                },
                'Api\Model\Options' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    return new Options($adapter, $db);
                },
                'Api\Model\Roles' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $permission = $sm->get('Application\Model\Permissions');
                    return new Roles($adapter, $db, $permission);
                },
                
                // events
                'Api\Event\UserDataEvent' => function ($sm) {
                    return new UserDataEvent();
                },
                'Api\Event\ViewEvent' => function ($sm) {
                    $auth = $sm->get('AuthService');
                    $user = $sm->get('Api\Model\Users');
                    
                    return new ViewEvent($auth->getIdentity(), $user);
                }
            )
        );
    }

    public function onDispatchError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function onRenderError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function getJsonModelError($e)
    {
        $error = $e->getError();
        if (! $error) {
            return;
        }
        
        $response = $e->getResponse();
        $exception = $e->getParam('exception');
        $exceptionJson = array();
        if ($exception) {
            $exceptionJson = array(
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'stacktrace' => $exception->getTraceAsString()
            );
        }
        
        $errorJson = array(
            'message' => 'An error occurred during execution; please try again later.',
            'error' => $error,
            'exception' => $exceptionJson
        );
        if ($error == 'error-router-no-match') {
            $errorJson['message'] = 'Resource not found.';
        }
        
        $model = new JsonModel(array(
            'errors' => array(
                $errorJson
            )
        ));
        
        $e->setResult($model);
        
        return $model;
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
}
