<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Event/BaseEvent.php
 */
namespace Base\Event;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Base - Event
 *
 * Contains all the global logic for Events
 * <br /><strong>The Base Event should be the parent of any Events within the system</strong>
 *
 * @package BackupProServer\Events
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Base/src/Base/Event/BaseEvent.php
 */
abstract class BaseEvent extends AbstractPluginManager
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\ServiceManager\AbstractPluginManager::validatePlugin()
     */
    public function validatePlugin($plugin)
    {}

    /**
     * Registers the Event with the system
     *
     * This method should handle attaching the events to
     * the Base\Model\BaseModel identifier.
     *
     * @param \Zend\EventManager\SharedEventManager $ev            
     */
    abstract public function register(\Zend\EventManager\SharedEventManager $ev);
}