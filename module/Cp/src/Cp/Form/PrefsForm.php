<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Form/PrefsForm.php
 */
namespace PM\Form;

use Application\Form\PrefsForm as AppForm;
use Application\Model\Options\Datetime;

/**
 * PM - PrefsForm Form
 *
 * Generates the Preferences form
 *
 * @package Users\UserData
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Form/PrefsForm.php
 *            
 */
class PrefsForm extends AppForm
{

    /**
     * Returns the Preferences form
     * 
     * @param string $options            
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        $this->add(array(
            'name' => 'noti_assigned_task',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_assigned_task'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'noti_status_task',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_status_task'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'noti_file_uploaded',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_file_uploaded'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'noti_file_revision_uploaded',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_file_revision_uploaded'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'noti_remove_proj_team',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_remove_proj_team'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'noti_add_proj_team',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_add_proj_team'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'noti_daily_task_reminder',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_daily_task_reminder'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'daily_reminder_schedule',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'timezone'
            ),
            'options' => array(
                'value_options' => Datetime::hours()
            )
        ));
        
        $this->add(array(
            'name' => 'noti_priority_task',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'noti_priority_task'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
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