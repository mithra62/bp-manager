<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/CompanyType.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Companies;

/**
 * PM - Company Type View Helper
 *
 * @package 	ViewHelpers\Companies
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/CompanyType.php
 */
class CompanyType extends BaseViewHelper
{
	public function __invoke($type)
	{
		return Companies::translateTypeId($type); 
	}
}