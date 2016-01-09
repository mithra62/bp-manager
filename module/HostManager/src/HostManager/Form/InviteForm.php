<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Form/InviteForm.php
 */
namespace HostManager\Form;

use Base\Form\BaseForm;

/**
 * User Invite Form
 *
 * Generates the User Invite Up Form
 *
 * @package HostManager\Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/Form/InviteForm.php
 *            
 */
class InviteForm extends BaseForm
{

    /**
     *
     * @param string $name            
     */
    public function __construct($name = null)
    {
        
        // we want to ignore the name passed
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large'
            )
        ));
        
        $this->add(array(
            'name' => 'first_name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large'
            )
        ));
        $this->add(array(
            'name' => 'last_name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large'
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton'
            )
        ));
    }

    /**
     * Adds the Role selection fields to the form
     * 
     * @param \Application\Model\Roles $roles            
     * @return \Application\Form\UsersForm
     */
    public function rolesFields(\Application\Model\Roles $roles)
    {
        $roles = $roles->getAllRoleNames();
        $role_fields = array();
        foreach ($roles as $role) {
            $role_fields[$role['id']] = $role['name'];
        }
        
        $this->add(array(
            'name' => 'user_roles',
            'type' => 'MultiCheckbox',
            'attributes' => array(
                'id' => 'user_roles'
            ),
            'options' => array(
                'value_options' => $role_fields
            )
        ));
        
        return $this;
    }
}