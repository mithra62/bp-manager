<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/IndexController.php
 */
namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Result as AuthenticationResult;

/**
 * Api - Index Controller
 *
 * General API Interaction Controller
 *
 * @package Users\Rest
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Controller/IndexController.php
 */
class LoginController extends AbstractRestfulJsonController
{

    /**
     * Maps the available HTTP verbs we support for groups of data
     * 
     * @var array
     */
    protected $collectionOptions = array(
        'GET',
        'POST',
        'OPTIONS'
    );

    /**
     * Maps the available HTTP verbs for single items
     * 
     * @var array
     */
    protected $resourceOptions = array(
        'GET',
        'POST',
        'OPTIONS'
    );

    public function logoutAction()
    {
        $login = $this->getServiceLocator()->get('Application\Model\Login');
        $login->logout($this->getSessionStorage(), $this->getAuthService());
        
        return new JsonModel(array());
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            $user = $this->getServiceLocator()->get('Api\Model\Users');
            $login = $this->getServiceLocator()->get('Application\Model\Login');
            $prefs = $this->getServiceLocator()->get('Application\Model\User\Data');
            $login->setAuthAdapter($this->getServiceLocator()
                ->get('Zend\Db\Adapter\Adapter'));
            
            $inputFilter = $login->getInputFilter();
            $inputFilter->setData($data);
            if (! $inputFilter->isValid($data)) {
                return $this->setError(422, 'missing_input_data', null, null, array(
                    'errors' => $inputFilter->getMessages()
                ));
            }
            
            $result = $login->procLogin($data['email'], $data['password'], $this->getServiceLocator()
                ->get('AuthService'));
            switch ($result) {
                case AuthenticationResult::SUCCESS:
                    
                    $identity = $this->getServiceLocator()
                        ->get('AuthService')
                        ->getIdentity();
                    $user->upateLoginTime($identity);
                    $this->getSessionStorage()->setRememberMe(1);
                    $this->getAuthService()->setStorage($this->getSessionStorage());
                    
                    $user_data = $user->getUserById($identity);
                    $user_data = $this->cleanResourceOutput($user_data, $user->usersOutputMap);
                    $embeds['user_roles'] = $user->getUserRoles($identity);
                    
                    $embeds['user_roles'] = $this->cleanCollectionOutput($embeds['user_roles'], $user->userRolesOutputMap);
                    $embeds['user_roles'] = $this->setupCollectionMeta($embeds['user_roles'], 'api-roles', 'roles/view', 'role_id');
                    
                    $times = $this->getServiceLocator()->get('PM\Model\Times');
                    $user_data['hours'] = $times->getTotalTimesByUserId($identity);
                    $user_data['prefs'] = $prefs->getUsersData($identity);
                    
                    return new JsonModel($this->setupHalResource($user_data, 'api-users', $embeds, 'users/view', 'user_id'));
                    
                    break;
                
                case AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND:
                case AuthenticationResult::FAILURE_CREDENTIAL_INVALID:
                default:
                    return $this->setError(422, 'invalid_credentials', null, null, array());
                    break;
            }
        }
        return $this->methodNotAllowed();
    }
}
