<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Form/SignUpForm.php
 */
namespace HostManager\Form;

use Base\Form\BaseForm;

/**
 * Account Sign Up Form
 *
 * Generates the Account Sign Up Form
 *
 * @package HostManager\SignUp
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/Form/SignUpForm.php
 *            
 */
class SignUpForm extends BaseForm
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
            'name' => 'organization',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large'
            )
        ));
        
        $this->add(array(
            'name' => 'subdomain',
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
            'name' => 'email',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large'
            )
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
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
}