<?php
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link			http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/config/module.config.php
 */
return array(
    'sub_primary_url' => '.bp-server.com',
    'master_host_account' => '1',
    'router' => array(
        'routes' => array(
            'hosted-accounts' => array( // Account Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/account',
                    'defaults' => array(
                        'controller' => 'HostManager\Controller\Accounts',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'signup' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/signup[/:status]',
                            'defaults' => array(
                                'action' => 'signup'
                            )
                        )
                    )
                )
            ), // end Account Routes
            'api-hosted-accounts' => array( // Hosted Accounts Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/api/accounts[/:id]',
                    'defaults' => array(
                        'controller' => 'HostManager\Controller\AccountsApi'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            ), // end Hosted Accounts
            
            'account-invites' => array( // Account Invite Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/invite',
                    'defaults' => array(
                        'controller' => 'HostManager\Controller\Accounts',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'confirm' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/confirm/:confirm_code',
                            'constraints' => array(
                                'confirm_code' => '([a-z0-9]{8})-([a-z0-9]{4})-([a-z0-9]{4})-([a-z0-9]{4})-([a-z0-9]{12})'
                            ),
                            'defaults' => array(
                                'action' => 'confirm'
                            )
                        )
                    )
                )
            ), // End Account Invite Routes
            
            'users' => array( // User Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/pm/users',
                    'defaults' => array(
                        'controller' => 'HostManager\Controller\Users',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'view' => array(
                        'type' => 'segment',
                        'options' => array(
                            'defaults' => array()

                            
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
                    'invite' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/invite',
                            'defaults' => array(
                                'action' => 'invite'
                            )
                        )
                    ),
                    'remove-invite' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/remove-invite/:user_id',
                            'defaults' => array(
                                'action' => 'removeInvite'
                            )
                        )
                    ),
                    'roles' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/roles/:user_id',
                            'constraints' => array(
                                'user_id' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'action' => 'roles'
                            )
                        )
                    ),
                    'edit' => array(
                        'options' => array(
                            'route' => '/edit',
                            'defaults' => array()

                            
                        )
                    )
                )
            ), // End User Routes
            'projects' => array( // User Routes
                'type' => 'segment',
                'options' => array(
                    'route' => '/pm/projects'
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'manage-team' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/manage-team/:project_id',
                            'constraints' => array(
                                'slug' => '[0-9]+'
                            ),
                            'defaults' => array(
                                'controller' => 'HostManager\Controller\Projects',
                                'action' => 'manageTeam'
                            )
                        )
                    )
                )
            )
        ) // End User Routes

    ),
    
    'controllers' => array(
        'invokables' => array(
            'HostManager\Controller\Accounts' => 'HostManager\Controller\AccountsController',
            'HostManager\Controller\AccountsApi' => 'HostManager\Controller\AccountsApiController',
            'HostManager\Controller\Users' => 'HostManager\Controller\UsersController',
            'HostManager\Controller\Projects' => 'HostManager\Controller\ProjectsController',
            'HostManager\Controller\Cli' => 'HostManager\Controller\CliController'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'template_map' => array()
        // 'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
        ,
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    
    'view_helpers' => array(
        'invokables' => array(
            'AccountUrl' => 'HostManager\View\Helper\AccountUrl',
            'GetUserAccounts' => 'HostManager\View\Helper\GetUserAccounts'
        )
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
                'text_domain' => 'hm'
            )
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'hm-cron' => array(
                    'options' => array(
                        'route' => 'run account cron [--verbose|-v]:verbose',
                        'defaults' => array(
                            'controller' => 'HostManager\Controller\Cli',
                            'action' => 'cron'
                        )
                    )
                )
            )
        )
    )
);