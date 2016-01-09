<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./config/autoload/global.php
 */
return array(
    'config_cache_enabled' => false,
    'config_cache_key' => 'module_config_cache',
    'cache_dir' => './data/cache',
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=moji;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    ),
    // see http://framework.zend.com/manual/2.1/en/modules/zend.mail.smtp.options.html for complete options
    'email' => array(
        'type' => 'php', // choose between `php` and `smtp`
        'smtp_options' => array( // if `smtp` chosen above, this must be completed and accurate
            
            'name' => 'localhost.localdomain',
            'host' => '127.0.0.1',
            'connection_class' => 'login',
            'connection_config' => array(
                'ssl' => 'tls',
                'username' => 'user',
                'password' => 'pass'
            ),
            'port' => '25'
        )
    ),
    'image_handling' => array(
        'driver' => 'imagick'
    ),
    'email_logging' => array(
        'type' => 'file',
        'file_options' => array(
            'path' => 'data/mail/',
            'callback' => function (\Zend\Mail\Transport\File $transport) {
                return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
            }
        )
    ),
    'service_manager' => array(
        'invokables' => array(
            'Zend\Authentication\AuthenticationService' => 'Zend\Authentication\AuthenticationService'
        )
    ),
    'moji_session' => array(
        'remember_me_seconds' => '1209600',
        'cookie_httponly' => true
    ),
    'setting_defaults' => array(
        'site_name' => 'Eric',
    )
    // 'cookie_domain' => 'moji2.com',
    
);
