<?php
return array(
    'router' => array(
        'routes' => array(
            'cp' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/cp',
                    'defaults' => array(
                        'controller' => 'Cp\Controller\Index',
                        'action' => 'index'
                    )
                )
            ), // end Settings Routes
            'system_settings' => array( // Settings Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/cp/settings',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Cp\Controller\Settings',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'mail' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/mail',
                            'defaults' => array(
                                'action' => 'mail'
                            )
                        )
                    )
                )
            ), // end Settings Routes
            'manage_users' => array( // Settings Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/cp/settings',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Cp\Controller\Settings',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'mail' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/mail',
                            'defaults' => array(
                                'action' => 'mail'
                            )
                        )
                    )
                )
            ), // end Settings Routes
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Cp\Controller\Index' => 'Cp\Controller\IndexController',
            'Cp\Controller\Settings' => 'Cp\Controller\SettingsController',
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array()
        // 'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        // 'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
        // 'error/404' => __DIR__ . '/../view/error/404.phtml',
        // 'error/index' => __DIR__ . '/../view/error/index.phtml',
        ,
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
                'text_domain' => 'cp'
            )
        )
    ),
    
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array()
        )
    )
);