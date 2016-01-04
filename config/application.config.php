<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./config/application.config.php
*/

return array(
    'modules' => array(
        'Base',
        'Application',
        //'PM',
        //'Api',
        'ZF\ApiProblem',
    	//'HostManager', //keep this as last at all times if being hosted
    	'BjyProfiler',
        'ZendDeveloperTools',
    	'ZfSimpleMigrations'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php'
        )
    )
);
