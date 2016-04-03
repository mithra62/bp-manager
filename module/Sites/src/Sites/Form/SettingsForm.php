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
     * Contains the various data pieces to replicate Backup Pro forms
     * @var array
     */
    protected $platform_options = array();
    
    /**
     * Returns the Settings form
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }
    
    public function setPlatformOptions(array $options)
    {
        $this->platform_options = $options;
        return $this;
    }
    
    public function getPlatformOptions($key = false)
    {
        if($key) {
            return (isset($this->platform_options[$key]) ? $this->platform_options[$key] : array());
        }
        
        return $this->platform_options;
    }
    
    public function getDbForm()
    {

        $this->add(array(
            'name' => 'max_db_backups',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'max_db_backups'
            )
        ));

        $this->add(array(
            'name' => 'db_backup_alert_threshold',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'db_backup_alert_threshold'
            )
        ));

		$this->add(array(
			'name' => 'db_backup_method',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => $this->getPlatformOptions('available_db_backup_engines'),
			)
		));

        $this->add(array(
            'name' => 'php_backup_method_select_chunk_limit',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'php_backup_method_select_chunk_limit'
            )
        ));

        $this->add(array(
            'name' => 'mysqldump_command',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'mysqldump_command'
            )
        ));

        $this->add(array(
            'name' => 'mysqlcli_command',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'mysqlcli_command'
            )
        ));
        return $this;
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
			'name' => 'auto_threshold',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => $this->getPlatformOptions('threshold_options'),
			)
		));
        
        $this->add(array(
            'name' => 'auto_threshold_custom',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'auto_threshold_custom'
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
		
		$this->add(array(
			'name' => 'allow_duplicates',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'allow_duplicates',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
		
		$this->add(array(
			'name' => 'relative_time',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'relative_time',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
        
        return $this;
    }
}