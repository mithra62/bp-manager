<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectPriority.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

/**
 * PM - Project Priority View Helper
 *
 * @package 	ViewHelpers\Projects
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectPriority.php
 */
class ProjectPriority extends BaseViewHelper
{
	public function __invoke($priority)
	{
		$return = Projects::translatePriorityId($priority); 
		//$url = $this->view->StaticUrl();
		$return = '<img src="'.$this->view->serverUrl('/images/priorities/'.$priority.'.gif').'" alt="'.$return.'" title="'.$return.'" /> '.$return;
		return $return; 
	}
}