<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/FileStatus.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Files;

/**
 * PM - FileStatus View Helper
 *
 * @package 	ViewHelpers\Files
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/FileStatus.php
 */
class FileStatus extends BaseViewHelper
{
	public function __invoke($status)
	{
		return Files::translateStatusId($status); 
	}
}