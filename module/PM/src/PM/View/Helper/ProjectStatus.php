<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectStatus.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

/**
 * PM - Project Status View Helper
 *
 * @package 	ViewHelpers\Projects
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectStatus.php
 */
class ProjectStatus extends BaseViewHelper
{
	/**
	 * Invokes the actual Helper
	 * @param int $status
	 * @uses Projects::translateStatusId()	to translate things up nicely
	 * @return string
	 */
	public function __invoke($status)
	{
		return Projects::translateStatusId($status); 
	}
}