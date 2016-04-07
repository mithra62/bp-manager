<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/View/Helper/m62DateTime.php
 */
namespace Sites\View\Helper;

use Base\View\Helper\BaseViewHelper;
/**
 * Sites - Backup Pro Format Date View Helper
 *
 * @package ViewHelpers\Date
 * @author Eric Lamb <eric@mithra62.com>
 */
class m62FormErrors extends BaseViewHelper
{
	/**
	 * Returns the human readable date if under week old
	 * @param string $date
	 * @return string
	 */
	public function __invoke($errors)
	{

	    $return = '';
	    if (is_array($errors) && count($errors) >= 1) {
	        $return = '<ul class="padding-top:5px; color:red;">';
	        foreach ($errors as $error) {
	            $return .= '<li class="notice">' . $this->getView()->EscapeHtml($error) . '</li>';
	        }
	        $return .= '</ul>';
	    }
	    
	    return $return;
	}
}