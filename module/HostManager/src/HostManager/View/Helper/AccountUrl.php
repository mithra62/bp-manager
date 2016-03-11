<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/View/Helper/AccountUrl.php
 */
namespace HostManager\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * HostManager - Account URL View Helper
 *
 * Takes an account ID and creates the full URL domain
 *
 * @param
 *            string The size to convert
 * @package ViewHelpers\Accounts
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/View/Helper/AccountUrl.php
 */
class AccountUrl extends BaseViewHelper
{

    private $account_urls = array();

    /**
     *
     * @ignore
     *
     */
    public function __invoke($account_id, $route)
    {
        if (empty($this->account_urls[$account_id])) {
            $helperPluginManager = $this->getServiceLocator();
            $serviceManager = $helperPluginManager->getServiceLocator();
            $account = $serviceManager->get('HostManager\Model\Accounts');
            $this->account_urls[$account_id] = $account->createAccountUrl($account_id);
        }
        
        return $this->account_urls[$account_id] . $route;
    }
}