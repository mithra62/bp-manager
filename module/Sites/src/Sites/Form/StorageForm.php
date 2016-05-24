<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Form/StorageForm.php
 */
namespace Sites\Form;

use Base\Form\BaseForm;

/**
 * Storage Location Form
 *
 * Generates the Storage Location form
 *
 * @package StorageForm
 * @author Eric Lamb <eric@mithra62.com>
 *            
 */
class StorageForm extends BaseForm
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
    
    /**
     * Sets the Platform dropbox options 
     * @param array $options
     * @return Sites\Form\SettingsForm
     */
    public function setPlatformOptions(array $options)
    {
        $this->platform_options = $options;
        return $this;
    }
    
    /**
     * Returns the Platform options
     * @param string $key
     * @return Ambigous <multitype:, array>|array
     */
    public function getPlatformOptions($key = false)
    {
        if($key) {
            return (isset($this->platform_options[$key]) ? $this->platform_options[$key] : array());
        }
        
        return $this->platform_options;
    }
    
    public function getIntegrityForm()
    {
        $this->add(array(
            'name' => 'db_verification_db_name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'db_verification_db_name'
            )
        ));
        
        $this->add(array(
            'name' => 'total_verifications_per_execution',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'total_verifications_per_execution'
            )
        ));
		
		$this->add(array(
			'name' => 'check_backup_state_cp_login',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'check_backup_state_cp_login',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));    
		
        $this->add(array(
            'name' => 'backup_missed_schedule_notify_email_interval',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'backup_missed_schedule_notify_email_interval'
            )
        ));

        $this->add(array(
            'type' => 'Textarea',
            'name' => 'backup_missed_schedule_notify_emails',
            'attributes' => array(
                'rows' => '7',
                'cols' => '40'
            )
        ));

		$this->add(array(
			'name' => 'backup_missed_schedule_notify_email_mailtype',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => array('text' => 'Plain Text', 'html' => 'HTML'),
			)
		));
        
        $this->add(array(
            'name' => 'backup_missed_schedule_notify_email_subject',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'backup_missed_schedule_notify_email_subject'
            )
        ));

        $this->add(array(
            'type' => 'Textarea',
            'name' => 'backup_missed_schedule_notify_email_message',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
        
        return $this;
    }
    
    public function getCronForm()
    {
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'cron_notify_emails',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));

		$this->add(array(
			'name' => 'cron_notify_email_mailtype',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => array('text' => 'Plain Text', 'html' => 'HTML'),
			)
		));

        $this->add(array(
            'name' => 'cron_notify_email_subject',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'cron_notify_email_subject'
            )
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'cron_notify_email_message',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
        
        return $this;
    }
    
    /**
     * Sets up the Files Settings form
     * @return \Sites\Form\SettingsForm
     */
    public function getFilesForm()
    {

        $this->add(array(
            'name' => 'max_file_backups',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'max_file_backups'
            )
        ));

        $this->add(array(
            'name' => 'file_backup_alert_threshold',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'file_backup_alert_threshold'
            )
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'backup_file_location',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'exclude_paths',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));    
		
		$this->add(array(
			'name' => 'regex_file_exclude',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'regex_file_exclude',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));    
        
        return $this;
    }
    
    /**
     * Sets up the Database Settings form 
     * @return \Sites\Form\SettingsForm
     */
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
			'name' => 'db_restore_method',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => $this->getPlatformOptions('available_db_restore_engines'),
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

		$this->add(array(
			'name' => 'db_backup_ignore_tables',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'multiple' => true
			),
			'options' => array(
				'value_options' => $this->getPlatformOptions('db_tables'),
			)
		));

		$this->add(array(
			'name' => 'db_backup_ignore_table_data',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'multiple' => true
			),
			'options' => array(
				'value_options' => $this->getPlatformOptions('db_tables'),
			)
		));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'db_backup_archive_pre_sql',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'db_backup_archive_post_sql',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'db_backup_execute_pre_sql',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'db_backup_execute_post_sql',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
		
        return $this;
    }
    
    /**
     * Sets up the General Settings form fields
     * @return \Sites\Form\SettingsForm
     */
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