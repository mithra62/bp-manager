<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/EmailForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;

/**
 * Email Form
 *
 * Generates the Change Email form
 *
 * @package Users\Email
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/EmailForm.php
 *            
 */
class EmailForm extends BaseForm
{

    /**
     * Creates the Password Change Form
     * 
     * @param string $name            
     * @param string $confirm            
     */
    public function __construct($name = 'password', $confirm = TRUE)
    {
        parent::__construct($name);
        $this->add(array(
            'name' => 'new_email',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'new_email'
            )
        ));
    }

    /**
     * Appends the confirm_password form field if called
     * 
     * @return \Application\Form\EmailForm
     */
    public function confirmPasswordField()
    {
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
}