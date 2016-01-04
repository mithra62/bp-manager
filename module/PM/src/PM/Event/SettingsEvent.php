<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/PM/Event/SettingsEvent.php
 */

namespace PM\Event;

use Base\Event\BaseEvent;

/**
 * PM - Settings Event
 * 
 * Injects the needed PM settings into the MojiTrac settings system
 *
 * @package 	PM\Settings
 * @author		Eric Lamb
 * @filesource 	./module/Freshbooks/src/PM/Event/SettingsEvent.php
 */
class SettingsEvent extends BaseEvent
{	
	/**
	 * The default settings to append to the Moji settings array
	 * @var array
	 */	
	private $default_settings = array(
		'master_company' => '1', 
		'enable_ip' => '0', 
		'allowed_file_formats' => 'jpg,gif,png,txt,docx,doc,pdf,php,xls,xlsx,csv,psd,ppt,pptx,pot,potx,rar,zip,tar,gz,tgz,bz2,html,htm,avi,mov,fla,swf,asf,flv,sql,mp3', 
			 
		'default_company_type' => '1',  
		'default_company_client_language' => 'en_US',
		'default_company_currency_code' => 'USD',
		'default_company_hourly_rate' => '0.00',
			
		'default_project_type' => '',  
		'default_project_priority' => '3',  
		'default_project_status' => '3',
			
		'default_task_type' => '',  
		'default_task_priority' => '3',  
		'default_task_status' => '3',
			
		'task_auto_archive_days' => '6',
		'_task_auto_archive_last_ran' => ''
	);
	
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'settings.defaults.set.pre' => 'modSettingsDefaults'
    );

    /**
     * Registers the Event with ZF and our Application Model
     * @param \Zend\EventManager\SharedEventManager $ev
     */
    public function register( \Zend\EventManager\SharedEventManager $ev)
    {
    	foreach($this->hooks AS $key => $value)
    	{
    		$ev->attach('Base\Model\BaseModel', $key, array($this, $value));
    	}
    }
    
    /**
     * Merges our settings array into the MojiTrac Settings array 
     * @param \Zend\EventManager\Event $event
     * @return array
     */
	public function modSettingsDefaults(\Zend\EventManager\Event $event)
	{
		$defaults = $event->getParam('defaults');
		$defaults = array_merge($defaults, $this->default_settings);
		$event->setParam('defaults', $defaults);
		return $defaults;
		
	}
}