<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/View/Helper/FormatDate.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * Application - Format Date View Helper
 *
 * @package ViewHelpers\Date
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/View/Helper/FormatDate.php
 */
class FormatDate extends BaseViewHelper
{
    /**
     * Returns the human readable date if under week old
     * @param string $date
     * @return string
     */
    function __invoke($date, $include_time = FALSE)
    {

        
    }
    
    private function c($str)
    {
        return '<!--'.$str.'-->';
    }
}