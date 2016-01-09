<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/config/module.config.php
 */
return array(
    
    'router' => array(
        'routes' => array(
            'api' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/api',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Index',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'chain-projects' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/chain-projects',
                            'defaults' => array(
                                'action' => 'chainProjects'
                            )
                        )
                    ),
                    'chain-tasks' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/chain-tasks[/:project_id]',
                            'constraints' => array(
                                'project_id' => '[0-9]*'
                            ),
                            'defaults' => array(
                                'action' => 'chainTasks'
                            )
                        )
                    )
                )
            ),
            'api-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/api/login',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Login',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'logout' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'action' => 'logout'
                            )
                        )
                    ),
                    'chain-tasks' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/chain-tasks[/:project_id]',
                            'constraints' => array(
                                'project_id' => '[0-9]*'
                            ),
                            'defaults' => array(
                                'action' => 'chainTasks'
                            )
                        )
                    )
                )
            ),
            'api-projects' => array( // Project Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/projects[/:id]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Projects'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            ),
            // end Project Routes
            'api-tasks' => array( // Tasks Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/tasks[/:id]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Tasks'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            ), // end Tasks Routes
            'api-users' => array( // Users Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/users[/:id]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Users'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            ), // end Users Routes
            'api-companies' => array( // Companies Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/companies[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\Companies'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            ), // end Companies Routes
            'api-options' => array( // Options Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/options[/:id]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Options'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            ), // end Companies Options
            'api-roles' => array( // Options Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/roles[/:id]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Roles'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            ), // end Companies Options
            
            'api-task' => array( // Tasks Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/task',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Task',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'update-progress' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/update-progress/:task_id',
                            'defaults' => array(
                                'action' => 'updateProgress'
                            )
                        )
                    )
                )
            )
        ) // end Tasks Routes

        
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'GetApiKey' => 'Api\View\Helper\GetApiKey'
        )
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
                'text_domain' => 'api'
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\Index' => 'Api\Controller\IndexController',
            'Api\Controller\Projects' => 'Api\Controller\ProjectsController',
            'Api\Controller\Task' => 'Api\Controller\TaskController',
            'Api\Controller\Tasks' => 'Api\Controller\TasksController',
            'Api\Controller\Companies' => 'Api\Controller\CompaniesController',
            'Api\Controller\Users' => 'Api\Controller\UsersController',
            'Api\Controller\Options' => 'Api\Controller\OptionsController',
            'Api\Controller\Roles' => 'Api\Controller\RolesController',
            'Api\Controller\Login' => 'Api\Controller\LoginController'
        )
    )
);