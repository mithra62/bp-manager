<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/View/Helper/GetApiKey.php
 */
namespace Api\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * View Helper - Get REST API Key
 *
 * @package ViewHelpers\Routes
 * @author Eric Lamb
 * @filesource ./module/Api/src/Api/View/Helper/GetApiKey.php
 */
class GetApiKey extends BaseViewHelper
{

    /**
     *
     * @ignore
     *
     * @return Ambigous <number, \Base\View\Helper\unknown>
     */
    public function __invoke()
    {
        $helperPluginManager = $this->getServiceLocator();
        $serviceManager = $helperPluginManager->getServiceLocator();
        $key = $serviceManager->get('Api\Model\Key');
        
        return $key->getKey($this->getIdentity());
    }
}