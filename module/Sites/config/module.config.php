<?php
return array(
    'router' => array(
        'routes' => array(
            'sites' => array( //Sites Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/sites',
        			'defaults' => array(
        				'controller' => 'Sites\Controller\Index',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:site_id]',
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
        					'route' => '/remove/:site_id',
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
        					'route' => '/edit/:site_id',
        					'constraints' => array(
        						'user_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			)
        		)
        	), //End Sites Routes 
            'dashboard' => array( //Dashboard Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/dashboard/:site_id',
                    'constraints' => array(
                        'site_id' => '[0-9]+'
                    ),                    
                    'defaults' => array(
        				'controller' => 'Sites\Controller\Dashboard',
        				'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'view' => array(
        				'type' => 'segment',
        				'options' => array(
        				    'route' => '/[:site_id]',
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
        				    'route' => '/remove/:site_id',
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
        				    'route' => '/edit/:site_id',
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
        )

    ),
    'controllers' => array(
        'invokables' => array(
            'Sites\Controller\Index' => 'Sites\Controller\IndexController',
            'Sites\Controller\Dashboard' => 'Sites\Controller\DashboardController',
            'Sites\Controller\Settings' => 'Sites\Controller\SettingsController',
            'Sites\Controller\Backup' => 'Sites\Controller\BackupController',
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
                'text_domain' => 'sites'
            )
        )
    ),
);