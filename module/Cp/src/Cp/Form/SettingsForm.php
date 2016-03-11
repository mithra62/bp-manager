<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Form/SettingsForm.php
 */
namespace PM\Form;

use Application\Form\SettingsForm as AppSettingsForm;
use PM\Model\Options\Companies;
use PM\Model\Options\Projects;
use PM\Model\Options\Tasks;
use Application\Model\Options\Currencies;
use Application\Model\Options\Languages;

/**
 * PM - Settings Form
 *
 * Generates the Settings form
 *
 * @package Settings
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Form/SettingsForm.php
 *            
 */
class SettingsForm extends AppSettingsForm
{

    /**
     * Returns the System Settings form
     * 
     * @param string $options            
     */
    public function __construct($name, \PM\Model\Companies $companies, \PM\Model\Options $option)
    {
        parent::__construct($name);
        
        $this->add(array(
            'name' => 'master_company',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'master_company'
            ),
            'options' => array(
                'value_options' => Companies::companies($companies)
            )
        ));
        
        $this->add(array(
            'name' => 'enable_ip',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => 'checkbox',
                'id' => 'enable_ip'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        // companies
        $this->add(array(
            'name' => 'default_company_type',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_company_type'
            ),
            'options' => array(
                'value_options' => Companies::types()
            )
        ));
        
        $this->add(array(
            'name' => 'default_company_currency_code',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_company_currency_code'
            ),
            'options' => array(
                'value_options' => Currencies::codes()
            )
        ));
        
        $this->add(array(
            'name' => 'default_company_hourly_rate',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large',
                'id' => 'default_company_hourly_rate'
            )
        ));
        
        $this->add(array(
            'name' => 'default_company_client_language',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'client_language'
            ),
            'options' => array(
                'value_options' => Languages::langs()
            )
        ));
        
        // projects
        $this->add(array(
            'name' => 'default_project_type',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_project_type'
            ),
            'options' => array(
                'value_options' => Projects::types($option)
            )
        ));
        
        $this->add(array(
            'name' => 'default_project_priority',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_project_priority'
            ),
            'options' => array(
                'value_options' => Projects::priorities()
            )
        ));
        
        $this->add(array(
            'name' => 'default_project_status',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_project_status'
            ),
            'options' => array(
                'value_options' => Projects::status()
            )
        ));
        
        // tasks
        $this->add(array(
            'name' => 'default_task_type',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_task_type'
            ),
            'options' => array(
                'value_options' => Tasks::types($option)
            )
        ));
        
        $this->add(array(
            'name' => 'default_task_priority',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_task_priority'
            ),
            'options' => array(
                'value_options' => Tasks::priorities()
            )
        ));
        
        $this->add(array(
            'name' => 'default_task_status',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'default_task_status'
            ),
            'options' => array(
                'value_options' => Tasks::status()
            )
        ));
        
        $this->add(array(
            'name' => 'task_auto_archive_days',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'task_auto_archive_days'
            ),
            'options' => array(
                'value_options' => range(0, 30)
            )
        ));
    }
}