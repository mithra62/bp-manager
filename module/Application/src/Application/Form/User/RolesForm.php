<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/User/RolesForm.php
 */
namespace Application\Form\User;

use Base\Form\BaseForm;

/**
 * User Roles Form
 *
 * Generates the RolesForm form
 *
 * @package Users\Roles
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/Users/RolesForm.php
 *            
 */
class RolesForm extends BaseForm
{

    /**
     * Returns the Users form
     * 
     * @param string $name            
     */
    public function __construct($name, $role)
    {
        parent::__construct($name);
        
        $this->setAttribute('method', 'post');
        $roles = $role->getAllRoleNames();
        $role_fields = array();
        foreach ($roles as $role) {
            $role_fields[$role['id']] = $role['name'];
        }
        
        $this->add(array(
            'name' => 'user_roles',
            'type' => 'MultiCheckbox',
            'attributes' => array(
                'class' => 'input',
                'id' => 'user_roles'
            ),
            'options' => array(
                'value_options' => $role_fields
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
}