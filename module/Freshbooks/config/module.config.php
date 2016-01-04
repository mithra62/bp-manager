<?php
/**
 * mithra62 - MojiTrac
 *
 * @package		Default
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/config/module.config.php
*/

return array(
    'router' => array(
        'routes' => array(
        	'freshbooks' => array( //Index Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/freshbooks',
        			'constraints' => array(
        				'id' => '[0-9]+'
        			),
        			'defaults' => array(
        				'controller' => 'Freshbooks\Controller\Index',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        		)
        	), //end Index Routes

        	'freshbooks-settings' => array( //Settings Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/freshbooks/settings',
        			'defaults' => array(
        				'controller' => 'Freshbooks\Controller\Settings',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'link-account' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/link-account',
        					'defaults' => array(
        						'action' => 'linkAccount'
        					)
        				)
        			),
        		)
        	), //end Bookmarks Routes
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Freshbooks\Controller\Index' => 'Freshbooks\Controller\IndexController',
            'Freshbooks\Controller\Settings' => 'Freshbooks\Controller\SettingsController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
                'text_domain' => 'freshbooks',
            ),
        ),
    ),
              
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
