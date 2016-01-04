<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectType.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

 /**
 * PM - Project Type View Helper
 *
 * @package 	ViewHelpers\Projects
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectType.php
 */
class ProjectType extends BaseViewHelper
{
	public function __invoke($type)
	{
		$helperPluginManager = $this->getServiceLocator();
		$serviceManager = $helperPluginManager->getServiceLocator();

		$options = $serviceManager->get('PM\Model\Options');
		return Projects::translateTypeId($type, $options); 
	}
}