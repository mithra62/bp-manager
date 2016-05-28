<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/SettingsForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;

/**
 * Settings Form
 *
 * Generates the Password form
 *
 * @package Settings
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/SettingsForm.php
 *            
 */
class SettingsForm extends BaseForm
{

    /**
     * Returns the System Settings form
     * 
     * @param string $options            
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }
    
    public function getGeneralForm()
    {
        $this->add(array(
            'name' => 'site_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'site_name'
            )
        ));
        
        $this->add(array(
            'name' => 'site_url',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'site_url'
            )
        ));
        
        return $this;
    }
    
    public function getMailForm()
    {
        $this->add(array(
            'name' => 'mail_reply_to_email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'mail_reply_to_email'
            )
        ));
        
        $this->add(array(
            'name' => 'mail_reply_to_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'mail_reply_to_name'
            )
        ));
        
        $this->add(array(
            'name' => 'mail_sender_email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'mail_sender_email'
            )
        ));
        
        $this->add(array(
            'name' => 'mail_sender_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'mail_sender_name'
            )
        ));
        
        $this->add(array(
            'name' => 'mail_from_email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'mail_from_email'
            )
        ));
        
        $this->add(array(
            'name' => 'mail_from_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'mail_from_name'
            )
        ));
        
        return $this;
    }
}