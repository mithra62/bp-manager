<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/UsersForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;

/**
 * UserForm Form
 *
 * Generates the UsersForm form
 *
 * @package Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/UsersForm.php
 *            
 */
class UsersForm extends BaseForm
{

    /**
     * Returns the Users form
     * 
     * @param string $name            
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'email'
            )
        ));
    }

    /**
     * Includes the fields needed for Registration to the form
     * 
     * @return \Application\Form\UsersForm
     */
    public function registrationForm()
    {
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'password'
            )
        ));
        
        $this->add(array(
            'name' => 'confirm_password',
            'type' => 'Password',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'confirm_password'
            )
        ));
        
        return $this;
    }

    /**
     * Adds the Role selection fields to the form
     * 
     * @param \Application\Model\Roles $roles            
     * @return \Application\Form\UsersForm
     */
    public function rolesFields(\Application\Model\User\Roles $roles)
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
                'class' => 'input large',
                'id' => 'user_roles'
            ),
            'options' => array(
                'value_options' => $role_fields
            )
        ));
        
        return $this;
    }
    
    public function verificationField()
    {	
		$this->add(array(
			'name' => 'verified',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'verified',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
		
		return $this;
    }
    
    public function verificationFields()
    {
		
		$this->add(array(
			'name' => 'send_verification_email',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'send_verification_email',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
		
		$this->add(array(
			'name' => 'auto_verify',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'auto_verify',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
		
		return $this;
    }
}