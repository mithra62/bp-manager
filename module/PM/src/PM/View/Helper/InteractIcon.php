<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/InteractIcon.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Interact Icon View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/InteractIcon.php
 */
class InteractIcon extends BaseViewHelper
{
    public $view;
    
    private $return = array('img','url');

    /**
     * Creates an icon image HTML tag (<img />) based on $type
     * @param string $type
     * @param string $alt
     * @param string $atts
     * @param string $return
     * @return string
     */
	function __invoke($type, $alt, $atts = FALSE, $return = 'img')
	{
		if(!in_array($return, $this->return))
		{
			$return = 'img';
		}
		
		switch($type)
		{
			case 'folder':
				$icon = 'folder.png';
			break;
			case 'user':
				$icon = 'user.png';
			break;
			case 'pencil':
				$icon = 'pencil.png';
			break;
			case 'cross':
				$icon = 'cross.png';
			break;
			case 'email':
				$icon = 'email.png';
			break;
			case 'www':
				$icon = 'www.png';
			break;
			case 'preview':
				$icon = 'preview.gif';
			break;
			case 'save_disk':
				$icon = 'save_disk.png';
			break;						
			case 'view-all':
				$icon = 'view-all.png';
			break;											
			case 'add':
			default:
				$icon = 'add.png';
			break;
			case 'map':
				$icon = 'map.png';
			break;
			case 'left-arrow':
				$icon = 'icons/arrow_left_16.png';
			break;
			case 'right-arrow':
				$icon = 'icons/arrow_right_16.png';
			break;			
			case 'help':
				$icon = 'icons/info_button_16.png';
			break;			
			case 'tick':
				$icon = 'tick.png';
			break;	
			case 'chart':
				$icon = 'icons/chart_16.png';
			break;	
			case 'timer':
				$icon = 'icons/tool_timer_16.png';
			break;	
			case 'user_roles':
				$icon = 'icons/user_roles_16.png';
			break;							
		}
		
		$url = $this->view->StaticUrl().'/images/'.$icon;
		if($return == 'img')
		{
			return '<img src="'.$url.'" width="16" height="16" class="png_bg" alt="'.strip_tags($alt).'" title="'.strip_tags($alt).'" '.$atts.' />';
		}
		
		return $url;
	}
}