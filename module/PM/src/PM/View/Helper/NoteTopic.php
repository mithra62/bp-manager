<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/NoteTopic.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Notes;

/**
 * PM - Note Topic View Helper
 * 
 * Translates a note topic_id into its human name
 *
 * @package 	ViewHelpers\Notes
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/NoteTopic.php
 */
class NoteTopic extends BaseViewHelper
{
	/**
	 * Invokes the actual Helper
	 * @param int $topic_id
	 * @return string
	 */
	public function __invoke($topic_id)
	{
		return Notes::translateTopicId($topic_id); 
	}
}