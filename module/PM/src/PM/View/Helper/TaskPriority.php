<?php 
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/TaskPriority.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

/**
 * PM - Task Priority View Helper
 *
 * @package 	Tasks\ViewHelpers
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/TaskPriority.php
 */
class TaskPriority extends BaseViewHelper
{   
	/**
	 * @ignore
	 * @param unknown $priority
	 * @return string
	 */	
	public function __invoke($priority)
	{
		$return = Projects::translatePriorityId($priority); 
		$return = '<img src="'.$this->view->serverUrl().'/images/priorities/'.$priority.'.gif" alt="'.$return.'" title="'.$return.'" /> '.$return;
		return $return;
	}
}