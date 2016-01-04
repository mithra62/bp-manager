<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/FileSize.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - FileSize View Helper
 * 
 * Converts a given string into a readable file size format 
 *
 * @param	string	The size to convert
 * @package 	ViewHelpers\Files
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/View/Helper/FileSize.php
 */
class FileSize extends BaseViewHelper
{
	/**
	 * @ignore
	 */
    public function __invoke($size)
    {
    	return $this->filesizeFormat($size);
    }
    
}