<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/HostManager/Controller/AccountsApiController.php
 */
namespace HostManager\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * HostManager - Accounts API Controller
 *
 * Accounts API Controller
 *
 * @package HostManager\Accounts
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Controller/OptionsController.php
 */
class AccountsApiController extends AbstractRestfulJsonController
{

    /**
     * Maps the available HTTP verbs we support for groups of data
     * 
     * @var array
     */
    protected $collectionOptions = array(
        'POST'
    );

    /**
     * Maps the available HTTP verbs for single items
     * 
     * @var array
     */
    protected $resourceOptions = array(
        'POST'
    );

    /**
     * (non-PHPdoc)
     * 
     * @see \Api\Controller\AbstractRestfulJsonController::create()
     */
    public function create($data)
    {
        $account = $this->getServiceLocator()->get('HostManager\Model\Accounts');
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $company = $this->getServiceLocator()->get('PM\Model\Companies');
        $setting = $this->getServiceLocator()->get('Application\Model\Settings');
        $option = $this->getServiceLocator()->get('PM\Model\Options');
        
        $translate = $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('_');
        
        $inputFilter = $account->getInputFilter($translate);
        $inputFilter->setData($data);
        if (! $inputFilter->isValid($data)) {
            return $this->setError(422, 'missing_input_data', null, null, array(
                'errors' => $inputFilter->getMessages()
            ));
        }
        
        $account_id = $account->createAccount($data, $user, $company, $hash, $setting, $option);
        if (! $account_id) {
            return $this->setError(500, 'account_create_failed');
        }
        
        $account_data = $account->getOptionById($account_id);
        $account_data = $this->cleanResourceOutput($account_data, $option->optionsOutputMap);
        return new JsonModel($this->setupHalResource($option_data, 'api-options', array(), 'options/view', 'option_id'));
    }
}
