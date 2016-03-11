<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Cron/BaseCron.php
 */
namespace Base\Cron;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Cron\CronExpression;

/**
 * Base - Base Cron Model
 *
 * @package Cron
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Base/src/Base/Model/BaseCron.php
 *            
 */
abstract class BaseCron implements ServiceLocatorAwareInterface
{

    /**
     * The ZF Service Locator object
     * 
     * @var ServiceLocatorInterface
     */
    public $serviceLocator;

    /**
     * The calling Model object
     * 
     * @var \Base\Model\BaseModel
     */
    protected $context = null;

    /**
     * The Console output object
     * 
     * @var \Zend\Console\Adapter\AbstractAdapter
     */
    protected $console = null;

    /**
     * Date/Timestamp for when a Cron was last ran at
     * 
     * @var string
     */
    protected $last_ran = 'now';

    /**
     * The Cron expression to use for our method
     * 
     * @var string
     */
    protected $expression = '@yearly';

    /**
     * Determines whether a Cron should be ran
     */
    abstract public function shouldRun();

    /**
     * Executes the pending Crons
     */
    abstract public function run();

    /**
     * Sets the Cron expression we're using.
     *
     *
     * Should use standard Cron syntax and/or certain placeholders included in the CronExpression library
     * 
     * @see https://github.com/mtdowling/cron-expression
     * @param unknown $expression            
     * @return \Base\Cron\BaseCron
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Sets the last time a Cron was ran
     * 
     * @param string $date            
     * @return \Base\Cron\BaseCron
     */
    public function setLastRunDate($date)
    {
        $this->last_ran = $date;
        return $this;
    }

    /**
     * Determines whether a Cron should be ran based on when it was last ran
     * 
     * @return boolean
     */
    public function isDue()
    {
        $cron = CronExpression::factory($this->expression);
        return $cron->isDue();
    }

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator            
     * @return CustomHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Returns the next run date
     * 
     * @param string $format            
     * @return string
     */
    public function getNextRunDate($format = 'Y-m-d H:i:s')
    {
        $cron = CronExpression::factory($this->expression);
        return $cron->getNextRunDate()->format($format);
    }

    /**
     * Returns the previous run date
     * 
     * @param string $format            
     * @return string
     */
    public function getPreviousRunDate($format = 'Y-m-d H:i:s')
    {
        $cron = CronExpression::factory($this->expression);
        return $cron->getPreviousRunDate()->format($format);
    }

    /**
     * Get the service locator.
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Sets the console object for output
     * 
     * @param \Zend\Console\Adapter\AbstractAdapter $console            
     * @return \Base\Cron\BaseCron
     */
    public function setConsole(\Zend\Console\Adapter\AbstractAdapter $console)
    {
        $this->console = $console;
        return $this;
    }

    /**
     * Sets the calling Model
     * 
     * @param \Base\Model\BaseModel $context            
     * @return \Base\Cron\BaseCron
     */
    public function setContext(\Base\Model\BaseModel $context)
    {
        $this->context = $context;
        return $this;
    }
}