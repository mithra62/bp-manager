<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Form/SettingsForm.php
 */
namespace Sites\Form;

use Base\Form\BaseForm;

/**
 * SiteForm Form
 *
 * Generates the UsersForm form
 *
 * @package Users
 * @author Eric Lamb <eric@mithra62.com>
 *            
 */
class SettingsForm extends BaseForm
{

    /**
     * Returns the Settings form
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }
    
    public function getGeneralForm()
    {
        $this->add(array(
            'name' => 'working_directory',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'working_directory'
            )
        ));
        
        $this->add(array(
            'name' => 'cron_query_key',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'cron_query_key'
            )
        ));
        
        $this->add(array(
            'name' => 'dashboard_recent_total',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'dashboard_recent_total'
            )
        ));
        
        $this->add(array(
            'name' => 'date_format',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'date_format'
            )
        ));
        
        return $this;
    }
}