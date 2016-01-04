<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/View/Helper/getUserAccounts.php
 */

namespace HostManager\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * HostManager - Get User Accounts
 * 
 * Returns an array for a user's linked MojiTrac accounts
 *
 * @package 	ViewHelpers\Accounts
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/View/Helper/getUserAccounts.php
 */
class GetUserAccounts extends BaseViewHelper
{	
	/**
	 * @ignore
	 */
    public function __invoke($identity, $include_self = true)
    {
		$helperPluginManager = $this->getServiceLocator();
		$serviceManager = $helperPluginManager->getServiceLocator();
		$account = $serviceManager->get('HostManager\Model\Accounts');	
    	$accounts = $account->getUserAccounts(array('user_id' => $identity));
    	
    	$account_id = $account->getAccountId();
    	$return = array();
    	foreach($accounts AS $moji)
    	{
    		if($account_id == $moji['account_id'] && !$include_self)
    		{
    			continue;
    		}
    		
    		$account_details = $account->getAccountDetails($moji['account_id']);
			if(!$account_details)
			{
				continue;
			}
    		$url = $account->createAccountUrl($moji['account_id']);
    		$return[] = array_merge(array('joined_date' => $moji['created_date'], 'url' => $url), $account_details);
    	}
    	
		return $return;
    }
    
}