<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/ForgotPasswordForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;

/**
 * Forgot Password Form
 *
 * Generates the Forgot Password form
 *
 * @package Users\Login\ForgotPassword
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/ForgotPasswordForm.php
 *            
 */
class ForgotPasswordForm extends BaseForm
{

    /**
     * Generates the Forgot Password form
     * 
     * @param string $options            
     */
    public function __construct($name = null)
    {
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
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'form-control'
            )
        ));
    }
}