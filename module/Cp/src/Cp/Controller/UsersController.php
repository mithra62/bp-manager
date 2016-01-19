<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Cp/src/Cp/Controller/UsersController.php
 */
namespace Cp\Controller;

use Cp\Controller\AbstractCpController;

/**
 * CP - Users Controller
 *
 * Routes the Users requests
 *
 * @package Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Controller/UsersController.php
 */
class UsersController extends AbstractCpController
{
    /**
     * Main Page
     * 
     * @return void
     */
    public function indexAction()
    {
        if (! $this->perm->check($this->identity, 'view_users_data')) {
            return $this->redirect()->toRoute('cp');
        }
        
        $order = $this->getRequest()->getQuery('order', false);
        $order_dir = $this->getRequest()->getQuery('order_dir', false);
        $limit = $this->getRequest()->getQuery('limit', 10);
        $page = $this->getRequest()->getQuery('page', 1);
        
        $users = $this->getServiceLocator()->get('Application\Model\Users');
        
        $users_data = $users->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAllUsers();
        
        $view = array(
            'section' => 'view_users',
            'active_sidebar' => 'manage_users',
            'users' => $users_data,
            'order' => $order,
            'order_dir' => $order_dir,
            'limit' => $limit,
            'page' => $page,
            'total_pages' => $users->total_pages,
            'total_results' => $users->total_results
        );
        return $view;
    }

    /**
     * User View Page
     * 
     * @return void
     */
    public function viewAction()
    {
        $id = $this->params()->fromRoute('user_id');
        if (! $this->perm->check($this->identity, 'view_users_data')) {
            return $this->redirect()->toRoute('cp');
        }
        
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $view['user'] = $user->getUserById($id);
        if (! $view['user']) {
            return $this->redirect()->toRoute('manage_users');
        }
        
        $view['user']['data'] = $user->user_data->getUsersData($id);
        $view['roles'] = $user->getUserRoles($id);
        $view['id'] = $id;
        $view['section'] = 'view_users';
        $view['active_sidebar'] = 'manage_users';
        return $view;
    }

    /**
     * User Edit Page
     * 
     * @return array
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('user_id');

        if (! $this->perm->check($this->identity, 'view_users_data')) {
            return $this->redirect()->toRoute('cp');
        }
        
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $view['user'] = $user->getUserById($id);
        if (! $view['user']) {
            return $this->redirect()->toRoute('manage_users');
        }        
        
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $user_form = $this->getServiceLocator()->get('Application\Form\UsersForm');
        $roles = $this->getServiceLocator()->get('Application\Model\User\Roles');
        
        $view['id'] = $id;
        $view['add_groups'] = $this->perm->check($this->identity, 'manage_users');
        
        $user_data = $user->getUserById($id);
        $user_data['user_roles'] = $view['user_roles'] = $user->getUserRolesArr($id);
        
        $user_form->rolesFields($roles)->verificationField();
        $user_form->setData($user_data);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost();
            $user_form->setInputFilter($user->getEditInputFilter($id));
            $user_form->setData($request->getPost());
            if ($user_form->isValid($formData)) {
                $formData = $formData->toArray();
                if ($user->updateUser($formData, $id)) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('user_updated', 'app'));
                    return $this->redirect()->toRoute('manage_users/view', array(
                        'user_id' => $id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'app')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                    $user_form->setData($formData);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'app')
                );
                $this->layout()->setVariable('errors', $view['errors']);
                $user_form->setData($formData);
            }
        }
        
        $view['user_data'] = $user_data;
        $view['section'] = 'view_users';
        
        $view['form'] = $user_form;
        $view['active_sidebar'] = 'manage_users';
        return $view;
    }

    /**
     * User Add Page
     * 
     * @return void
     */
    public function addAction()
    {
        if (! $this->perm->check($this->identity, 'manage_users')) {
            return $this->redirect()->toRoute('manage_users');
        }
        
        $user = $this->getServiceLocator()->get('Cp\Model\Users');
        $user_form = $this->getServiceLocator()->get('Application\Form\UsersForm');
        $roles = $this->getServiceLocator()->get('Application\Model\User\Roles');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        
        $view['form'] = $user_form->registrationForm()->rolesFields($roles)->verificationFields();
        $view['addPassword'] = TRUE;
        $view['user_roles'] = $roles->getAllRoleNames();
        $view['layout_style'] = 'right';
        $view['sidebar'] = 'dashboard';
        $view['addAction'] = TRUE;
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $formData = $request->getPost();
            $user_form->setInputFilter($user->getRegistrationInputFilter());
            $user_form->setData($request->getPost());
            if ($user_form->isValid($formData)) {
                $user_id = $id = $user->addCpUser($formData->toArray(), $hash, $this->getServiceLocator()->get('Application\Model\Mail'));
                if ($user_id) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('user_added', 'app'));
                    return $this->redirect()->toRoute('manage_users/view', array(
                        'user_id' => $id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'app')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'app')
                );
                $this->layout()->setVariable('errors', $view['errors']);
                $user_form->setData($formData);
            }
        }
        
        $view['section'] = 'view_users';
        $view['active_sidebar'] = 'manage_users';
        return $view;
    }

    /**
     * The Remove User Action
     * 
     * @return array
     */
    public function removeAction()
    {
        if (! $this->perm->check($this->identity, 'manage_users')) {
            return $this->redirect()->toRoute('manage_users');
        }
        
        $view = array();
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $id = $this->params()->fromRoute('user_id');
        if (! $id) {
            return $this->redirect()->toRoute('manage_users');
        }
        
        if ($this->identity == $id) {
            $this->flashMessenger()->addErrorMessage($this->translate('user_cant_remove_self', 'app'));
            return $this->redirect()->toRoute('manage_users/view', array(
                'user_id' => $id
            ));
        }
        
        $view['user'] = $user->getUserById($id);
        
        if (! $view['user']) {
            return $this->redirect()->toRoute('manage_users');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $formData = $formData->toArray();
                if ($user->removeUser($id)) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('user_removed', 'app'));
                    return $this->redirect()->toRoute('manage_users');
                }
            }
        }
        
        $view['id'] = $id;
        $view['form'] = $form;
        $view['section'] = 'view_users';
        $view['active_sidebar'] = 'manage_users';
        return $this->ajaxOutput($view);
    }
}