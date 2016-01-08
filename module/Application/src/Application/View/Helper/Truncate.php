<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/Truncate.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Task Status View Helper
 *
 * @package 	ViewHelpers\String
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/Truncate.php
 */
class Truncate extends BaseViewHelper
{
	public function __invoke($string, $length = 80, $etc = '...',
	                                  $break_words = false, $middle = false)
	{
	    if ($length == 0)
	        return '';
	
	    if (strlen($string) > $length) {
	        $length -= min($length, strlen($etc));
	        if (!$break_words && !$middle) {
	            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
	        }
	        if(!$middle) {
	            return substr($string, 0, $length) . $etc;
	        } else {
	            return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
	        }
	    } else {
	        return $string;
	    }
	}
}
