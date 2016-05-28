<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/LoginForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;

/**
 * Login Form
 *
 * Generates the Login Form
 *
 * @package Users\Login
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/LoginForm.php
 *            
 */
class LoginForm extends BaseForm
{

    /**
     * Generates the LoginForm form
     * 
     * @param string $options            
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
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'class' => 'form-control'
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