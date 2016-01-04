<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/ActionBlock.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Action Block View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/ActionBlock.php
 */
class ActionBlock extends BaseViewHelper
{
    
    public function __invoke($icon, $copy, $url, $rel = false)
    {
    	if($icon == 'help')
    	{
    		$helperPluginManager = $this->getServiceLocator();
    		$serviceManager = $helperPluginManager->getServiceLocator();
    		
    		$user = $serviceManager->get('PM\Model\Users');
    		$prefs = $user->user_data->getUsersData($this->view->getIdentity());
    		if(isset($prefs['enable_contextual_help']) && $prefs['enable_contextual_help'] == '0')
    		{
    			return;
    		}
    	}
    	
    	$str = '<div class="actions">';
    	if($rel)
    	{
    		$rel = 'rel = "'.$rel.'"';
    	}
    	$str .= '<a href="'.$url.'" '.$rel.' title="'.$copy.'">';
		$str .= $this->view->InteractIcon($icon, $copy);
		$str .= '<div class="action_text">'.$copy.'</div>';
		$str .= '</a>';
		$str .= '</div>';
		return $str;
    }
    
}