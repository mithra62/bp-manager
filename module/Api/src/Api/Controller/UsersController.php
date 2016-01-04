<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link			http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/UsersController.php
*/

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * Api - Users Controller
 *
 * Users REST API Controller
 *
 * @package 	Users\Rest
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Controller/UsersController.php
 */
class UsersController extends AbstractRestfulJsonController
{
	/**
	 * Maps the available HTTP verbs we support for groups of data
	 * @var array
	 */
	protected $collectionOptions = array(
		'GET', 'POST', 'OPTIONS'
	);
	
	/**
	 * Maps the available HTTP verbs for single items
	 * @var array
	 */
	protected $resourceOptions = array(
		'GET', 'POST', 'DELETE', 'PUT', 'OPTIONS'
	);
		
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::getList()
	 */
	public function getList()
	{
		$order = $this->getRequest()->getQuery('order', false);
		$order_dir = $this->getRequest()->getQuery('order_dir', false);
		$limit = $this->getRequest()->getQuery('limit', 10);
		$page = $this->getRequest()->getQuery('page', 1);
		
		if(!parent::check_permission('view_users_data'))
		{
			return $this->setError(403, 'unauthorized_action');
		}		
		
		$user = $this->getServiceLocator()->get('Api\Model\Users');
		$users_data = $user->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAllUsers();

		$users_data['data'] = $this->cleanCollectionOutput($users_data['data'], $user->usersOutputMap);
		return new JsonModel( $this->setupHalCollection($users_data, 'api-users', 'users', 'users/view', 'user_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::get()
	 */
	public function get($id)
	{
		$user = $this->getServiceLocator()->get('Api\Model\Users');
		$prefs = $this->getServiceLocator()->get('Application\Model\User\Data');
		
		//if we can't view all users than we can only view ourselves
		if(!parent::check_permission($this->identity, 'view_users_data'))
		{
			$id = $this->identity;
		}		
		
		$user_data = $user->getUserById($id);
		if(!$user_data)
		{
			return $this->setError(404, 'not_found');
		}

		$user_data = $this->cleanResourceOutput($user_data, $user->usersOutputMap);
		$embeds['user_roles'] = $user->getUserRoles($id);
		

		$embeds['user_roles'] = $this->cleanCollectionOutput($embeds['user_roles'], $user->userRolesOutputMap);
		$embeds['user_roles'] = $this->setupCollectionMeta($embeds['user_roles'], 'api-roles', 'roles/view', 'role_id'); 

		$times = $this->getServiceLocator()->get('PM\Model\Times');
		$user_data['hours'] = $times->getTotalTimesByUserId($id);	
		$user_data['prefs']	= $prefs->getUsersData($id);
		
		return new JsonModel( $this->setupHalResource($user_data, 'api-users', $embeds, 'users/view', 'user_id') );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::create()
	 */
	public function create($data)
	{
		if(!parent::check_permission('manage_users'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$user = $this->getServiceLocator()->get('Api\Model\Users');

		//we have to validate the data has everything we need
		$inputFilter = $user->getRegistrationInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
		
		//ZF2 doesn't really have a validator for array style inputs so we have manually verify those here
		if(empty($data) || !is_array($data['user_roles']) || count($data['user_roles']) == 0)
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => array('user_roles' => array('isEmpty' => $this->translate('isEmpty', 'api')))));
		}
		
		//and now make sure we're dealing with a valid group_id set
		$data['user_roles'] = $user->filterUserRoles($data['user_roles']);
		if(count($data['user_roles']) == 0)
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => array('user_roles' => array('invalidValue' => $this->translate('invalidValue', 'api')))));
		}
		
		//ok; now let's add this bitch
		$hash = $this->getServiceLocator()->get('Application\Model\Hash');
		$data['creator'] = $this->identity;
		$user_id = $user->addUser($data, $hash);
		if(!$user_id)
		{
			return $this->setError(500, 'user_create_failed');
		}	
		
		$user_data = $user->getUserById($user_id);
		$user_data = $this->cleanResourceOutput($user_data, $user->usersOutputMap);
		$embeds['user_roles'] = $user->getUserRoles($user_id);
		$embeds['user_roles'] = $this->cleanCollectionOutput($embeds['user_roles'], $user->userRolesOutputMap);
		$embeds['user_roles'] = $this->setupCollectionMeta($embeds['user_roles'], 'api-roles', 'roles/view', 'role_id'); 

		$times = $this->getServiceLocator()->get('PM\Model\Times');
		$user_data['hours'] = $times->getTotalTimesByUserId($user_id);		
		
		return new JsonModel( $this->setupHalResource($user_data, 'api-users', $embeds, 'users/view', 'user_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::delete()
	 */
	public function delete($id)
	{
		if(!parent::check_permission('manage_users') || $this->identity == $id) //ensure they aren't removing themselves
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$user = $this->getServiceLocator()->get('Api\Model\Users');
		$user_data = $user->getUserById($id);
		if(!$user_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		if(!$user->removeUser($id))
		{
			return $this->setError(500, 'user_remove_failed');
		}
	
		return new JsonModel( );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::update()
	 */
	public function update($id, $data)
	{
		if(!parent::check_permission('manage_users'))
		{
			$id = $this->identity; //if they can't manage all users they can only update themselves
		}
		
		$user = $this->getServiceLocator()->get('Api\Model\Users');
		$user_data = $user->getUserById($id);
	
		if (!$user_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		$inputFilter = $user->getEditInputFilter();
		$inputFilter->setData($data);	
		$data = array_merge($user_data, $data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
	
		try {
				
			$user->updateUser($data, $id);
				
		} catch(Zend_Exception $e)
		{
			return $this->setError(500, 'user_update_failed');
		}

		$user_data = $user->getUserById($id);
		$user_data = $this->cleanResourceOutput($user_data, $user->usersOutputMap);
		$embeds['user_roles'] = $user->getUserRoles($id);
		$embeds['user_roles'] = $this->cleanCollectionOutput($embeds['user_roles'], $user->userRolesOutputMap);
		$embeds['user_roles'] = $this->setupCollectionMeta($embeds['user_roles'], 'api-roles', 'roles/view', 'role_id'); 

		$times = $this->getServiceLocator()->get('PM\Model\Times');
		$user_data['hours'] = $times->getTotalTimesByUserId($id);		
		
		return new JsonModel( $this->setupHalResource($user_data, 'api-users', $embeds, 'users/view', 'user_id') );
	}
}
