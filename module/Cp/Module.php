<?php
namespace Cp;

use Zend\ModuleManager\ModuleManager;

use Cp\Model\Users;

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
                'Cp\Model\Users' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    $roles = $sm->get('Application\Model\User\Roles');
                    $ud = $sm->get('Application\Model\User\Data');
                    return new Users($adapter, $db, $roles, $ud);
                },
            )
        );
    }    
}
