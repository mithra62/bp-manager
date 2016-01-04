<?php 
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/FormatHtml.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use Michelf\MarkdownExtra;

/**
 * PM - Format HTML View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/FormatHtml.php
 */
class FormatHtml extends BaseViewHelper
{
	public function __invoke($str)
	{
		return '<div class="md_wrap">'.MarkdownExtra::defaultTransform($str).'</div>';
	}
}