<?php
namespace Sites;

use Sites\Model\Sites;
use Sites\Model\Api;

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
    
                'Sites\Model\Api' => function ($sm) {
                    return new Api();
                },
                // setting up the Authentication stuff
                'Sites\Model\Sites' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    
                    $site = new Sites($adapter, $db);
                    $site->setApi($sm->get('Sites\Model\Api'));
                    return $site;
                },
                // setting up the Authentication stuff
                'Sites\Model\Site\Teams' => function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $db = $sm->get('SqlObject');
                    
                    $site = new Sites($adapter, $db);
                    $site->setApi($sm->get('Sites\Model\Api'));
                    return $site;
                },
				'Sites\Form\SiteForm' => function($sm) {
					return new SiteForm('site_form');
				},
            )
        );
    }  
}
