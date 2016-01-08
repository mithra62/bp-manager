<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/UserInfo.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - User Info View Helper
 *
 * @package 	ViewHelpers\Users
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/UserInfo.php
 */
class UserInfo extends BaseViewHelper
{
	/**
	 * Container of user data
	 * @var array
	 */
	public $userInfo = FALSE;
	
	/**
	 * @ignore
	 * @param int $id
	 * @param string $all
	 * @return multitype:
	 */
	public function __invoke($id, $all = false)
	{
		return $this->getUserInfo($id);
	}
	
	/**
	 * Returns an array of $id info
	 * @param int $id
	 * @return array
	 */
	private function getUserInfo($id)
	{
		if(!$this->userInfo)
		{
			$helperPluginManager = $this->getServiceLocator();
			$serviceManager = $helperPluginManager->getServiceLocator();
			$user = $serviceManager->get('Application\Model\Users');
			$this->userInfo = $user->getUserById($id);
		}
		return $this->userInfo;
	}
}