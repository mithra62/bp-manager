<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/RolesController.php
 */
namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * Api - User Roles Controller
 *
 * User Roles REST API Controller
 *
 * @package Users\Roles\Rest
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Controller/RolesController.php
 */
class RolesController extends AbstractRestfulJsonController
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
        'DELETE',
        'PUT',
        'OPTIONS'
    );

    /**
     * Class preDispatch
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $e = parent::onDispatch($e);
        return $e;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Api\Controller\AbstractRestfulJsonController::getList()
     */
    public function getList()
    {
        $order = $this->getRequest()->getQuery('order', false);
        $order_dir = $this->getRequest()->getQuery('order_dir', false);
        $limit = $this->getRequest()->getQuery('limit', 10);
        $page = $this->getRequest()->getQuery('page', 1);
        
        $role = $this->getServiceLocator()->get('Api\Model\Roles');
        $role_data = $role->setLimit($limit)
            ->setOrderDir($order_dir)
            ->setOrder($order)
            ->setPage($page)
            ->getAllRoles();
        if (! $role_data) {
            return $this->setError(404, 'not_found');
        }
        
        $role_data['data'] = $this->cleanCollectionOutput($role_data['data'], $role->userRolesOutputMap);
        return new JsonModel($this->setupHalCollection($role_data, 'api-roles', 'roles', 'roles/view', 'role_id'));
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Api\Controller\AbstractRestfulJsonController::get()
     */
    public function get($id)
    {
        $role = $this->getServiceLocator()->get('Api\Model\Roles');
        $role_data = $role->getRoleById($id);
        if (! $role_data) {
            return $this->setError(404, 'not_found');
        }
        
        $role_data = $this->cleanResourceOutput($role_data, $role->userRolesOutputMap);
        $role_permissions = $role->getRolePermissions($id);
        $permissions = $role->getAllPermissions();
        foreach ($permissions as $permission) {
            if (in_array($permission['id'], $role_permissions)) {
                $role_data['permissions'][] = $permission;
            }
        }
        
        if (! empty($role_data['permissions'])) {
            $role_data['permissions'] = $this->cleanCollectionOutput($role_data['permissions'], $role->rolePermissionOutputMap);
        }
        
        return new JsonModel($this->setupHalResource($role_data, 'api-roles', array(), 'roles/view', 'role_id'));
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Api\Controller\AbstractRestfulJsonController::create()
     */
    public function create($data)
    {
        if (! parent::check_permission('manage_roles')) {
            return $this->setError(403, 'unauthorized_action');
        }
        
        $role = $this->getServiceLocator()->get('Api\Model\Roles');
        $inputFilter = $role->getInputFilter();
        $inputFilter->setData($data);
        if (! $inputFilter->isValid($data)) {
            return $this->setError(422, 'missing_input_data', null, null, array(
                'errors' => $inputFilter->getMessages()
            ));
        }
        
        $role_id = $role->addRole($data);
        if (! $role_id) {
            return $this->setError(500, 'role_create_failed');
        }
        
        $role_data = $role->getRoleById($role_id);
        if (! $role_data) {
            return $this->setError(404, 'not_found');
        }
        
        $role_data = $this->cleanResourceOutput($role_data, $role->userRolesOutputMap);
        $role_permissions = $role->getRolePermissions($role_id);
        $permissions = $role->getAllPermissions();
        foreach ($permissions as $permission) {
            if (in_array($permission['id'], $role_permissions)) {
                $role_data['permissions'][] = $permission;
            }
        }
        
        if (! empty($role_data['permissions'])) {
            $role_data['permissions'] = $this->cleanCollectionOutput($role_data['permissions'], $role->rolePermissionOutputMap);
        }
        
        return new JsonModel($this->setupHalResource($role_data, 'api-roles', array(), 'roles/view', 'role_id'));
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Api\Controller\AbstractRestfulJsonController::delete()
     */
    public function delete($id)
    {
        if (! parent::check_permission('manage_roles')) {
            return $this->setError(403, 'unauthorized_action');
        }
        
        if ($id == '1' || $id == '2') {
            return $this->setError(403, 'unauthorized_action');
        }
        
        $role = $this->getServiceLocator()->get('Api\Model\Roles');
        $role_data = $role->getRoleById($id);
        if (! $role_data) {
            return $this->setError(404, 'not_found');
        }
        
        if (! $role->removeRole($id)) {
            return $this->setError(500, 'role_remove_failed');
        }
        
        return new JsonModel();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Api\Controller\AbstractRestfulJsonController::update()
     */
    public function update($id, $data)
    {
        if (! parent::check_permission('manage_roles')) {
            return $this->setError(403, 'unauthorized_action');
        }
        
        $role = $this->getServiceLocator()->get('Api\Model\Roles');
        $role_data = $role->getRoleById($id);
        if (! $role_data) {
            return $this->setError(404, 'not_found');
        }
        
        $inputFilter = $role->getInputFilter();
        
        $perms = $role->getRolePermissions($id, 'assoc');
        $data = array_merge($perms, $role_data, $data);
        $inputFilter->setData($data);
        if (! $inputFilter->isValid($data)) {
            return $this->setError(422, 'missing_input_data', null, null, array(
                'errors' => $inputFilter->getMessages()
            ));
        }
        
        try {
            
            $role->updateRole($data, $id);
        } catch (Zend_Exception $e) {
            return $this->setError(500, 'option_update_failed');
        }
        
        $role_data = $role->getRoleById($id);
        $role_data = $this->cleanResourceOutput($role_data, $role->userRolesOutputMap);
        $role_permissions = $role->getRolePermissions($id);
        $permissions = $role->getAllPermissions();
        foreach ($permissions as $permission) {
            if (in_array($permission['id'], $role_permissions)) {
                $role_data['permissions'][] = $permission;
            }
        }
        
        if (! empty($role_data['permissions'])) {
            $role_data['permissions'] = $this->cleanCollectionOutput($role_data['permissions'], $role->rolePermissionOutputMap);
        }
        
        return new JsonModel($this->setupHalResource($role_data, 'api-roles', array(), 'roles/view', 'role_id'));
    }
}
