<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Cron.php
 */
namespace Application\Model;

/**
 * Cron Model
 *
 * @package Cron
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/Cron.php
 *            
 */
class Cron extends AbstractModel
{

    /**
     * The path to where Cron objects are stored
     * 
     * @var string
     */
    private $path = null;

    /**
     * Sets everything up
     * 
     * @ignore
     *
     * @param \Zend\Db\Adapter\Adapter $db            
     * @param \Zend\Db\Sql\Sql $sql            
     * @param \Application\Model\Users $users            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $db, \Zend\Db\Sql\Sql $sql)
    {
        parent::__construct($db, $sql);
    }

    /**
     * Sets the directory where Cron scripts are stored
     * 
     * @param string $path            
     * @return \Application\Model\Cron
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function setServiceLocator($service_locator)
    {
        $this->serviceLocator = $service_locator;
        return $this;
    }

    /**
     * Sets the namespace all executed Crons will fall under
     * 
     * @param string $namespace            
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Executes any setup Crons
     * 
     * @param \Zend\Console\Adapter\AbstractAdapter $console            
     * @param string $namespace            
     * @return boolean
     */
    public function run(\Zend\Console\Adapter\AbstractAdapter $console, $namespace = null)
    {
        if (is_dir($this->path) && is_readable($this->path)) {
            $this->setTimezone('America/Los_Angeles');
            $d = dir($this->path);
            $ignore = array(
                '.',
                '..'
            );
            while (false !== ($entry = $d->read())) {
                if (! in_array($entry, $ignore)) {
                    $parts = explode('.php', $entry);
                    if ($parts['0'] != '') {
                        try {
                            
                            $class_name = '\\' . $this->namespace . '\Cron\\' . $parts['0'];
                            $class = new $class_name();
                            if ($class instanceof \Base\Cron\BaseCron) {
                                $class->setConsole($console);
                                $class->setContext($this);
                                $class->setServiceLocator($this->serviceLocator);
                                if ($class->shouldRun()) {
                                    $class->run();
                                }
                            }
                        } catch (\Exception $e) {
                            echo 'Caught exception: ', $e->getMessage(), "\n";
                            // ok, should probably log this so...
                            // @todo add Logging to failed execution
                        }
                    }
                }
            }
            $d->close();
        }
        return true;
    }
}