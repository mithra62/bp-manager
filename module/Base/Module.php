<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/Module.php
 */
namespace Base;

/**
 * Base - Module Loader
 *
 * Sets up all the global configuration data and focuses on Translations.
 * <br /><strong>Note that nothing in here should be considered callable</strong>
 *
 * @package BackupProServer
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Base/Module.php
 */
class Module
{

    /**
     * Sets the translation object up
     * 
     * @param \Zend\Mvc\MvcEvent $e            
     */
    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $translator = $e->getApplication()
            ->getServiceManager()
            ->get('translator');
        // $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))->setFallbackLocale('en_US');
        
        $e->getApplication()
            ->getServiceManager()
            ->get('ViewHelperManager')
            ->setAlias('_', 'translate');
        $e->getApplication()
            ->getServiceManager()
            ->get('ViewHelperManager')
            ->setAlias('plural', 'translateplural');
    }

    /**
     * Imports the configuration files for the module
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Returns the Autoloader Configuration
     * 
     * @return multitype:multitype:multitype:string
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    /**
     * Sets the language overrides up for form errors
     * 
     * @return multitype:multitype:string
     */
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'formelementerrors' => 'Base\Form\View\Helper\FormElementErrors'
            )
        );
    }
}
