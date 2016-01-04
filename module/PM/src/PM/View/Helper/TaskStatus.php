<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/TaskStatus.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

 /**
 * PM - Task Status View Helper
 *
 * @package 	Tasks\ViewHelpers
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/TaskStatus.php
 */
class TaskStatus extends BaseViewHelper
{
	public function __invoke($status)
	{
		return Projects::translateStatusId($status); 
	}
}