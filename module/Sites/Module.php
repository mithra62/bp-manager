<?php
namespace Sites;

use Sites\Model\Sites;

use Sites\Form\SiteForm;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
    
                // setting up the Authentication stuff
                'Sites\Model\Sites' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    
                    return new Sites($adapter, $db);
                },
				'Sites\Form\SiteForm' => function($sm) {
					return new SiteForm('site_form');
				},
            )
        );
    }  
}
