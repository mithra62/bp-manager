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
                    'route' => '/cp/settings[/:section]',
                    'constraints' => array(
                        'section' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Cp\Controller\Settings',
                        'action' => 'index'
                    )
                )
            ), // end Settings Routes
            'manage_users' => array( //User Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/cp/users',
        			'defaults' => array(
        				'controller' => 'Cp\Controller\Users',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:user_id]',
        					'constraints' => array(
        						'user_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/:user_id',
        					'constraints' => array(
        						'user_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add',
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit[/:user_id]',
        					'constraints' => array(
        						'user_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			)
        		)
        	), //End User Routes 

        	'ips' => array( //Ips Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/ip-locker',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Ips',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/:ip_id',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array( 
        						'controller' => 'PM\Controller\Ips',
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add',
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/:ip_id',
        					'constraints' => array(
        						'ip_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),      			
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/:ip_id',
        					'constraints' => array(
        						'ip_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),    			
        			'enable' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/enable',
        					'defaults' => array(
        						'action' => 'enable'
        					)
        				)
        			), 			
        			'self-allow' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/allow-self',
        					'defaults' => array(
        						'action' => 'allowSelf'
        					)
        				)
        			), 			
        			'blocked' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/blocked',
        					'defaults' => array(
        						'action' => 'blocked'
        					)
        				)
        			),		
        			'verify-allow' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/v/:verify_code',
        					'defaults' => array(
        						'action' => 'verifyCode'
        					),
        					'constraints' => array(
        						'verify_code' => '([a-z0-9]{8})-([a-z0-9]{4})-([a-z0-9]{4})-([a-z0-9]{4})-([a-z0-9]{12})'
        					),
        				)
        			),
        			
        		)
        	), //end IP Routes
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
            'Cp\Controller\Users' => 'Cp\Controller\UsersController',
            'Cp\Controller\Roles' => 'Cp\Controller\RolesController',
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