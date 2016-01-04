<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/MakeLink.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Make Link View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/MakeLink.php
 */
class MakeLink extends BaseViewHelper
{
	/**
	 * Creates a link to a route wrapped in an href tag
	 * @param string $type
	 * @param array $info
	 * @return string
	 */
	public function __invoke($type, array $info)
	{
		switch($type)
		{
			case 'user':
				if($this->view->CheckPermission('view_users_data') || $this->identity == $info['id'])
				{
					return $this->createLink($this->makeUserLink($info), $this->makeUserLinkBody($info));
				}
				else
				{
					return $this->makeUserLinkBody($info);
				}
			break;
			
			case 'role':
				if($this->view->CheckPermission('manage_roles'))
				{
					return $this->createLink($this->makeRoleLink($info), $info['name']);
				}
				else
				{
					return $info['name'];
				}				
			break;
			
			case 'project':
				
			break;
			
			case 'task':
				
			break;
			
			case 'back':
				return $this->makeBackLink($info);
			break;
			
		}
	}
	
	/**
	 * Creates a URL to a Users route
	 * @param array $info
	 */
	private function makeUserLink(array $info)
	{
		return $this->view->url('users/view', array('user_id' => $info['id']));
	}

	/**
	 * Creates a URL to a Roles route
	 * @param array $info
	 */
	private function makeRoleLink(array $info)
	{
		return $this->view->url('roles/view',array('role_id' => $info['id']));
	}	
	
	/**
	 * Personalizes the link output 
	 * @param array $info
	 * @return string
	 */
	private function makeUserLinkBody(array $info)
	{
		if($info['id'] == $this->view->getIdentity())
		{
			return 'You';
		}
		
		return $info['first_name'].' '.$info['last_name'];
	}
	
	/**
	 * Creates the actual link and returns it
	 * @param string $url
	 * @param string $body_part
	 * @return string
	 */
	private function createLink($url, $body_part)
	{
		return '<a title="'.$body_part.'" href="'.$url.'">'.$body_part.'</a>';	
	}
}