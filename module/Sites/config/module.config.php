<?php
return array(
    'router' => array(
        'routes' => array(
            'sites' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/sites',
                    'defaults' => array(
                        'controller' => 'Sites\Controller\Index',
                        'action' => 'index'
                    )
                )
            ),
        )

    ),
    'controllers' => array(
        'invokables' => array(
            'Sites\Controller\Index' => 'Sites\Controller\IndexController',
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