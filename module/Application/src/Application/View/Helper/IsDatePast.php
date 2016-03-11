<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/View/Helper/IsDatePast.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * Application - Is Date Past View Helper
 *
 * @package ViewHelpers\DateTime
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/View/Helper/IsDatePast.php
 */
class IsDatePast extends BaseViewHelper
{

    /**
     * Checks if the passed date string is in the past
     * 
     * @param string $date            
     * @return void|boolean
     */
    public function __invoke($date = null)
    {
        if ($date == '') {
            return;
        }
        
        $d = strtotime($date);
        if ($d && $d < time()) {
            return TRUE;
        }
    }
}