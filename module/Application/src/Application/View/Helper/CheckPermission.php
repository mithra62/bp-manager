<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/CheckPermission.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Check Permission View Helper
 *
 * @package ViewHelpers\Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/View/Helper/CheckPermission.php
 */
class CheckPermission extends BaseViewHelper
{

    /**
     * Contains the permissions
     * 
     * @var array
     */
    private $permissions = false;

    /**
     * Checks a given permission
     * 
     * @param unknown $permission            
     */
    public function __invoke($permission)
    {
        return $this->getPermissions()->check($this->getIdentity(), $permission);
    }

    /**
     * Returns the Permissions object
     * 
     * @return \Application\Model\Permissions:
     */
    public function getPermissions()
    {
        if (! $this->permissions) {
            $helperPluginManager = $this->getServiceLocator();
            $serviceManager = $helperPluginManager->getServiceLocator();
            $this->permissions = $serviceManager->get('Application\Model\User\Permissions');
        }
        return $this->permissions;
    }
}