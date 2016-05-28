<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/View/Helper/UserInfo.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * Application - User Info View Helper
 *
 * @package ViewHelpers\Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/View/Helper/UserInfo.php
 */
class UserInfo extends BaseViewHelper
{

    /**
     * Container of user data
     * 
     * @var array
     */
    public $userInfo = FALSE;

    /**
     *
     * @ignore
     *
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
     * 
     * @param int $id            
     * @return array
     */
    private function getUserInfo($id)
    {
        if (! $this->userInfo) {
            $helperPluginManager = $this->getServiceLocator();
            $serviceManager = $helperPluginManager->getServiceLocator();
            $user = $serviceManager->get('Application\Model\Users');
            $this->userInfo = $user->getUserById($id);
        }
        return $this->userInfo;
    }
}