<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
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
 * @package 	Users
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Form/UsersForm.php
*/
class UsersForm extends BaseForm
{
	/**
	 * Returns the Users form 
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
				'class' => 'input large',
				'id' => 'email'
			),
		));		
		$this->add(array(
			'name' => 'first_name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'first_name'
			),
		));		
		$this->add(array(
			'name' => 'last_name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'last_name'
			),
		));		
		$this->add(array(
			'name' => 'job_title',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'job_title'
			),
		));		
		$this->add(array(
			'name' => 'phone_home',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone_home'
			),
		));		
		$this->add(array(
			'name' => 'phone_work',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone_work'
			),
		));		
		$this->add(array(
			'name' => 'phone_fax',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone_work'
			),
		));		
		$this->add(array(
			'name' => 'phone_mobile',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone_work'
			),
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Go',
				'id' => 'submitbutton',
			),
		));
		
		$this->add(array(
			'type' => 'Textarea',
			'name' => 'description',
			'attributes' => array(
				'class' => 'styled_textarea',
				'rows' => '7',
				'cols' => '40',
			),
		));		
    }
    
    /**
     * Includes the fields needed for Registration to the form
     * @return \Application\Form\UsersForm
     */
    public function registrationForm()
    {
		$this->add(array(
			'name' => 'password',
			'type' => 'Password',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'password'
			),
		));
		
		$this->add(array(
			'name' => 'confirm_password',
			'type' => 'Password',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'confirm_password'
			),
		));		
		
    	return $this;
    }
    
    /**
     * Adds the Role selection fields to the form
     * @param \Application\Model\Roles $roles
     * @return \Application\Form\UsersForm
     */
    public function rolesFields(\Application\Model\Roles $roles)
    {
    	$roles = $roles->getAllRoleNames();
    	$role_fields = array();
    	foreach($roles AS $role)
    	{
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
				'value_options' => $role_fields,
			)
		));		
		
    	return $this;
    	
    }
}