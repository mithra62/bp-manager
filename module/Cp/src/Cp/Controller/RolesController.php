<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Cp/src/Cp/Controller/RolesController.php
 */
namespace Cp\Controller;

use Cp\Controller\AbstractCpController;

/**
 * PM - Roles Controller
 *
 * Routes the Roles requests
 *
 * @package Users\Roles
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Controller/RolesController.php
 *            
 */
class RolesController extends AbstractCpController
{
    /**
     * Main Page
     * 
     * @return void
     */
    public function indexAction()
    {
        $roles = $this->getServiceLocator()->get('Application\Model\User\Roles');
        $view['roles'] = $roles->getAllRoles();
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
        return $view;
    }

    /**
     * Role View Page
     * 
     * @return void
     */
    public function viewAction()
    {
        $id = $this->params()->fromRoute('role_id');
        if (! $id) {
            return $this->redirect()->toRoute('roles');
        }
        
        $roles = $this->getServiceLocator()->get('Application\Model\Roles');
        $view['role'] = $roles->getRoleById($id);
        if (! $view['role']) {
            return $this->redirect()->toRoute('roles');
        }
        
        $view['users'] = $roles->getUsersOnRole($id);
        $view['role_permissions'] = $roles->getRolePermissions($id);
        $view['permissions'] = $roles->getAllPermissions();
        $view['id'] = $id;
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
        return $view;
    }

    /**
     * User Role Edit Page
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('role_id');
        if (! $id) {
            return $this->redirect()->toRoute('roles');
        }
        
        $role = $this->getServiceLocator()->get('Application\Model\Roles');
        $form = $this->getServiceLocator()->get('Application\Form\RolesForm');
        
        $role_data = $role->getRoleById($id);
        $role_perms = $role->getRolePermissions($id, 'assoc');
        $role_data = array_merge($role_data, $role_perms);
        $view['permissions'] = $role->getAllPermissions();
        $view['id'] = $id;
        $form->setData($role_data);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost();
            $form->setInputFilter($role->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $formData = $formData->toArray();
                if ($role->updateRole($formData, $formData['id'])) {
                    $this->flashMessenger()->addMessage($this->translate('role_updated', 'pm'));
                    return $this->redirect()->toRoute('roles/view', array(
                        'role_id' => $id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('update_role_fail', 'pm')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                    $form->setData($formData);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'pm')
                );
                $this->layout()->setVariable('errors', $view['errors']);
                $form->setData($formData);
            }
        }
        
        $view['form'] = $form;
        $this->layout()->setVariable('layout_style', 'left');
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
        return $view;
    }

    /**
     * User Role Add Page
     * 
     * @return void
     */
    public function addAction()
    {
        $role = $this->getServiceLocator()->get('Application\Model\Roles');
        $form = $this->getServiceLocator()->get('Application\Form\RolesForm');
        
        $view['permissions'] = $role->getAllPermissions();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost();
            $form->setInputFilter($role->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $formData = $formData->toArray();
                $role_id = $id = $role->addRole($formData);
                if ($role_id) {
                    $this->flashMessenger()->addMessage($this->translate('role_added', 'pm'));
                    return $this->redirect()->toRoute('roles/view', array(
                        'role_id' => $role_id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'pm')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'pm')
                );
                $this->layout()->setVariable('errors', $view['errors']);
            }
        }
        
        $view['form'] = $form;
        $this->layout()->setVariable('layout_style', 'left');
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
        
        return $view;
    }

    public function removeAction()
    {
        $role = $this->getServiceLocator()->get('Application\Model\Roles');
        $form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
        $id = $this->params()->fromRoute('role_id');
        if (! $id) {
            return $this->redirect()->toRoute('roles');
        }
        
        // don't allow deletion of the user or administrator permissions.
        $deny_remove = FALSE;
        if ($id == '1' || $id == '2') {
            $deny_remove = TRUE;
            $view['deny_remove'] = TRUE;
        }
        
        $view['role'] = $role->getRoleById($id);
        if (! $view['role']) {
            return $this->redirect()->toRoute('roles');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $formData = $formData->toArray();
                if (! empty($formData['fail'])) {
                    return $this->redirect()->toRoute('roles/view', array(
                        'role_id' => $id
                    ));
                }
                
                if ($role->removeRole($id)) {
                    $this->flashMessenger()->addMessage($this->translate('role_removed', 'pm'));
                    return $this->redirect()->toRoute('roles');
                }
            }
        }
        
        $view['id'] = $id;
        $view['form'] = $form;
        return $this->ajaxOutput($view);
    }
}