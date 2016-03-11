<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/View//Helper/BaseViewHelper.php
 */
namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper as ZFAbstract;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DateTime, IntlDateFormatter, DateInterval;

/**
 * Base - View Helper
 *
 * Contains all the global logic for ViewHelpers
 * <br /><strong>The Base View Helper should be the parent of any ViewHelpers within the system</strong>
 *
 * @package BackupProServer\ViewHelper
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Base/src/Base/View//Helper/BaseViewHelper.php
 */
class BaseViewHelper extends ZFAbstract implements ServiceLocatorAwareInterface
{

    /**
     * The users ID
     * 
     * @var unknown
     */
    public $identity;

    /**
     * The ZF Service Locator object
     * 
     * @var ServiceLocatorInterface
     */
    public $serviceLocator;

    /**
     * The users custom data including preferences
     * 
     * @var array
     */
    public $userData;

    /**
     * Returns the user_id for the currenlty logged in user
     * 
     * @return int
     */
    public function getIdentity()
    {
        if (! $this->identity) {
            $helperPluginManager = $this->getServiceLocator();
            $serviceManager = $helperPluginManager->getServiceLocator();
            $this->identity = $serviceManager->get('AuthService')->getIdentity();
        }
        return $this->identity;
    }

    /**
     * Grabs the users_data for the currently logged in user
     * 
     * @return multitype:
     */
    public function getUserData()
    {
        if (! $this->userData) {
            $helperPluginManager = $this->getServiceLocator();
            $serviceManager = $helperPluginManager->getServiceLocator();
            $ud = $serviceManager->get('Application\Model\UserData');
            $this->userData = $ud->getUsersData($this->getIdentity());
        }
        return $this->userData;
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
     * Get the service locator.
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Takes a time stamp (string) and converts it to a different format using date() strings
     *
     * @param string $oldDate
     *            date string
     * @param string $format
     *            date string
     * @return string new time stamp string
     */
    public function formatDate($oldDate, $format)
    {
        $newDate = date($format, strtotime($oldDate));
        return $newDate;
    }

    /**
     * Format a number of bytes into a human readable format.
     * Optionally choose the output format and/or force a particular unit
     *
     * @param int $bytes
     *            The number of bytes to format. Must be positive
     * @param string $format
     *            Optional. The output format for the string
     * @param string $force
     *            Optional. Force a certain unit. B|KB|MB|GB|TB
     * @return string The formatted file size
     */
    public function filesizeFormat($val, $digits = 3, $mode = "SI", $bB = "B") // $mode == "SI"|"IEC", $bB == "b"|"B"
    {
        $si = array(
            "",
            "k",
            "M",
            "G",
            "T",
            "P",
            "E",
            "Z",
            "Y"
        );
        $iec = array(
            "",
            "Ki",
            "Mi",
            "Gi",
            "Ti",
            "Pi",
            "Ei",
            "Zi",
            "Yi"
        );
        switch (strtoupper($mode)) {
            case "SI":
                $factor = 1000;
                $symbols = $si;
                break;
            case "IEC":
                $factor = 1024;
                $symbols = $iec;
                break;
            default:
                $factor = 1000;
                $symbols = $si;
                break;
        }
        switch ($bB) {
            case "b":
                $val *= 8;
                break;
            default:
                $bB = "B";
                break;
        }
        for ($i = 0; $i < count($symbols) - 1 && $val >= $factor; $i ++) {
            $val /= $factor;
        }
        $p = strpos($val, ".");
        if ($p !== false && $p > $digits) {
            $val = round($val);
        } elseif ($p !== false) {
            $val = round($val, $digits - $p);
        }
        
        return round($val, $digits) . " " . $symbols[$i] . $bB;
    }
}