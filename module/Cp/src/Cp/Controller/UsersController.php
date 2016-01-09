<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/UsersController.php
 */
namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Users Controller
 *
 * Routes the Users requests
 *
 * @package Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Controller/UsersController.php
 */
class UsersController extends AbstractPmController
{

    /**
     * (non-PHPdoc)
     * 
     * @see \PM\Controller\AbstractPmController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $e = parent::onDispatch($e);
        // $this->layout()->setVariable('layout_style', 'single');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('active_nav', 'users');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()
            ->getRequestUri());
        $this->layout()->setVariable('active_sub', 'users');
        
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
        return $e;
    }

    /**
     * Main Page
     * 
     * @return void
     */
    public function indexAction()
    {
        if (! $this->perm->check($this->identity, 'view_users_data')) {
            return $this->redirect()->toRoute('pm');
        }
        
        $users = $this->getServiceLocator()->get('Application\Model\Users');
        $view['users'] = $users->getAllUsers();
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
        if (! $id) {
            $this->layout()->setVariable('active_nav', '');
            $this->layout()->setVariable('sub_menu', 'settings');
            $id = $this->identity;
        }
        
        if (! $this->perm->check($this->identity, 'view_users_data')) {
            $this->layout()->setVariable('active_nav', '');
            $this->layout()->setVariable('sub_menu', 'settings');
            $id = $this->identity;
        }
        
        $user = $this->getServiceLocator()->get('Pm\Model\Users');
        $view['user'] = $user->getUserById($id);
        if (! $view['user']) {
            return $this->redirect()->toRoute('pm');
        }
        
        $view['roles'] = $user->getUserRoles($id);
        $view['projects'] = $user->getAssignedProjects($id);
        
        $task = $this->getServiceLocator()->get('PM\Model\Tasks');
        $view['tasks'] = $task->getTasksByUserId($id, TRUE, TRUE, TRUE);
        
        $file = $this->getServiceLocator()->get('PM\Model\Files');
        $view['files'] = $file->getFilesByUserId($id);
        
        $times = $this->getServiceLocator()->get('PM\Model\Times');
        $view['times'] = $times->getTimesByUserId($id);
        $view['hours'] = $times->getTotalTimesByUserId($id);
        $view['id'] = $id;
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
        if (! $id) {
            $this->layout()->setVariable('active_nav', '');
            $this->layout()->setVariable('sub_menu', 'settings');
            $id = $this->identity;
        }
        
        if (! $this->perm->check($this->identity, 'view_users_data')) {
            $this->layout()->setVariable('active_nav', '');
            $this->layout()->setVariable('sub_menu', 'settings');
            $id = $this->identity;
        }
        
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $user_form = $this->getServiceLocator()->get('Application\Form\UsersForm');
        $roles = $this->getServiceLocator()->get('Application\Model\Roles');
        
        $view['id'] = $id;
        $view['add_groups'] = $this->perm->check($this->identity, 'manage_users');
        
        $user_data = $user->getUserById($id);
        $user_data['user_roles'] = $view['user_roles'] = $user->getUserRolesArr($id);
        
        $user_form->rolesFields($roles);
        $user_form->setData($user_data);
        
        $view['form'] = $user_form;
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $request->getPost();
            $user_form->setInputFilter($user->getEditInputFilter());
            $user_form->setData($request->getPost());
            if ($user_form->isValid($formData)) {
                $formData = $formData->toArray();
                if ($user->updateUser($formData, $formData['id'])) {
                    $this->flashMessenger()->addMessage($this->translate('user_updated', 'pm'));
                    return $this->redirect()->toRoute('users/view', array(
                        'user_id' => $id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'pm')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                    $user_form->setData($formData);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'pm')
                );
                $this->layout()->setVariable('errors', $view['errors']);
                $user_form->setData($formData);
            }
        }
        
        $view['user_data'] = $user_data;
        $this->layout()->setVariable('layout_style', 'left');
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
            return $this->redirect()->toRoute('users');
        }
        
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $user_form = $this->getServiceLocator()->get('Application\Form\UsersForm');
        $roles = $this->getServiceLocator()->get('Application\Model\Roles');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        
        $view['form'] = $user_form->registrationForm()->rolesFields($roles);
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
                $user_id = $id = $user->addUser($formData->toArray(), $hash, $roles);
                if ($user_id) {
                    $this->flashMessenger()->addMessage($this->translate('user_added', 'pm'));
                    return $this->redirect()->toRoute('users/view', array(
                        'user_id' => $id
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
                $user_form->setData($formData);
            }
        }
        $this->layout()->setVariable('layout_style', 'left');
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
            return $this->redirect()->toRoute('users');
        }
        
        $view = array();
        $user = $this->getServiceLocator()->get('Application\Model\Users');
        $form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
        $id = $this->params()->fromRoute('user_id');
        if (! $id) {
            return $this->redirect()->toRoute('users');
        }
        
        if ($this->identity == $id) {
            $this->flashMessenger()->addMessage($this->translate('user_cant_remove_self', 'pm'));
            return $this->redirect()->toRoute('users/view', array(
                'user_id' => $id
            ));
        }
        
        $view['user'] = $user->getUserById($id . 'f');
        if (! $view['user']) {
            return $this->redirect()->toRoute('users');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $formData = $formData->toArray();
                if (! empty($formData['fail'])) {
                    return $this->redirect()->toRoute('users/view', array(
                        'user_id' => $id
                    ));
                }
                
                if ($user->removeUser($id)) {
                    $this->flashMessenger()->addMessage($this->translate('user_removed', 'pm'));
                    return $this->redirect()->toRoute('users');
                }
            }
        }
        
        $view['projects_owned_count'] = count($user->getAssignedProjectIds($id));
        $view['tasks_owned_count'] = count($user->getOpenAssignedTasks($id));
        $view['id'] = $id;
        $view['form'] = $form;
        return $this->ajaxOutput($view);
    }
}