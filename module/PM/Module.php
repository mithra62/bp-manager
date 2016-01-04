<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/Module.php
 */

namespace PM;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use DateTime;

use PM\Model\Projects;
use PM\Model\Companies;
use PM\Model\Timers;
use PM\Model\Charts;
use PM\Model\Files;
use PM\Model\Files\Revisions;
use PM\Model\Tasks;
use PM\Model\Times;
use PM\Model\Bookmarks;
use PM\Model\Notes;
use PM\Model\Options;
use PM\Model\Contacts;
use PM\Model\ActivityLog;
use PM\Model\Calendar;
use PM\Model\Ips;
use PM\Model\Users;
use PM\Model\FusionCharts;
use PM\Model\Invoices;
use PM\Model\Invoices\LineItems;

use PM\Form\ProjectForm;
use PM\Form\CompanyForm;
use PM\Form\BookmarkForm;
use PM\Form\NoteForm;
use PM\Form\ContactForm;
use PM\Form\TaskForm;
use PM\Form\IpForm;
use PM\Form\ConfirmForm;
use PM\Form\OptionForm;
use PM\Form\TimeForm;
use PM\Form\TimerForm;
use PM\Form\FileForm;
use PM\Form\File\RevisionForm;
use PM\Form\InvoiceForm;
use PM\Form\PrefsForm;
use PM\Form\SettingsForm;

use PM\Event\ActivityLogEvent;
use PM\Event\NotificationEvent;
use PM\Event\SettingsEvent;
use PM\Event\UserDataEvent;

/**
 * PM - Module Object
 *
 * @package 	MojiTrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/Module.php
 */
class Module implements 
    ConsoleUsageProviderInterface,
    ConsoleBannerProviderInterface
{
	/**
	 * Sets up the module layout
	 * @param ModuleManager $moduleManager
	 */
	public function init(ModuleManager $moduleManager)
	{
		//sets the layout
		$this->sharedEvents = $moduleManager->getEventManager()->getSharedManager();
		$this->sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
			$controller = $e->getTarget();
			$controller->layout('layout/pm');
		}, 100);
			
	}

	/**
	 * Setup the Events we're gonna piggyback on
	 *
	 * @param \Zend\Mvc\MvcEvent $e
	 * @todo Abstract the registering of events
	 */	
	public function onBootstrap(\Zend\Mvc\MvcEvent $e)
	{
		//have to ensure we do settings and user data BEFORE any other events
		$event = $e->getApplication()->getServiceManager()->get('PM\Event\SettingsEvent');
		$event->register($this->sharedEvents);
		
		$event = $e->getApplication()->getServiceManager()->get('PM\Event\UserDataEvent');
		$event->register($this->sharedEvents);

		$event = $e->getApplication()->getServiceManager()->get('PM\Event\NotificationEvent');
		$event->register($this->sharedEvents);

		$event = $e->getApplication()->getServiceManager()->get('PM\Event\ActivityLogEvent');
		$event->register($this->sharedEvents);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface::getConsoleUsage()
	 */
	public function getConsoleUsage(Console $console)
	{
		return array(
			// Describe available commands
			'archive tasks [--verbose|-v] [--days=] [--status=]',
			array('Updates all tasks to --status that have been marked completed more than --days'),

			// Describe expected parameters
			array( '--days',           'How many days you want tasks to be in since Complete status was given' ),
			array( '--status',         'What status you want to set tasks that are Complete past --days' ),
			array( '--verbose|-v',     '(optional) turn on verbose mode'),
			'---------------------------------------',
			'',

			// Describe available commands
			'send task reminder [--verbose|-v] [--email=] [--member_id=]',
			array('Sends the Daily Task Reminder email(s). If --email and --member_id are empty everyone gets the email. '),

			// Describe expected parameters
			array( '--member_id',      'The member_id for the user you want to trigger' ),
			array( '--email',          'The email address for the user you want to trigger' ),
			array( '--verbose|-v',     '(optional) turn on verbose mode'),
			'---------------------------------------'
		);
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ModuleManager\Feature\ConsoleBannerProviderInterface::getConsoleBanner()
	 */
	public function getConsoleBanner(Console $console)
	{
		return 'PM 2.X';
	}	

	/**
	 * @ignore
	 */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @ignore
     * @return multitype:multitype:multitype:string
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * @ignore
     */
    public function getServiceConfig()
    {
    	return array(
			'factories' => array(
					
				//models
				'Timezone' => function($sm) {
					$auth = $sm->get('AuthService');
					$user = $sm->get('PM\Model\Users');
					$data = $user->user_data->getUsersData($auth->getIdentity());
					date_default_timezone_set($data['timezone']);
						
					$dt = new DateTime();
					$offset = $dt->format('P');
					$user->user_data->query("SET time_zone='$offset'");
					return true;
				},
				'PM\Model\Projects' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Projects($adapter, $db);
				},
				'PM\Model\Companies' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Companies($adapter, $db);
				},				
				'PM\Model\Timers' => function($sm) {
					$ud = $sm->get('Application\Model\User\Data');
					return new Timers($ud);
				},
				'PM\Model\Charts' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Charts($adapter, $db);
				},
				'PM\Model\Files' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$rev = $sm->get('PM\Model\Files\Revisions');
					return new Files($adapter, $db, $rev);
				},
				'PM\Model\Files\Revisions' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Revisions($adapter, $db);
				},
				'PM\Model\Tasks' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Tasks($adapter, $db);
				},
				'PM\Model\Times' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					
					$project = $sm->get('PM\Model\Projects');
					$task = $sm->get('PM\Model\Tasks');
					return new Times($adapter, $db, $project, $task);
				},
				'PM\Model\Bookmarks' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$hash = $sm->get('Application\Model\Hash');
					return new Bookmarks($adapter, $db, $hash);
				},
				'PM\Model\Notes' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$hash = $sm->get('Application\Model\Hash');
					return new Notes($adapter, $db, $hash);
				},
				'PM\Model\Options' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Options($adapter, $db);
				},
				'PM\Model\ActivityLog' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new ActivityLog($adapter, $db);
				},
				'PM\Model\Contacts' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Contacts($adapter, $db);
				},
				'PM\Model\Calendar' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$project = $sm->get('PM\Model\Projects');
					$task = $sm->get('PM\Model\Tasks');
					return new Calendar($adapter, $db, $project, $task);
				},
				'PM\Model\Ips' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Ips($adapter, $db);
				},
				'PM\Model\Users' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$role = $sm->get('Application\Model\Roles');
					$ud = $sm->get('Application\Model\User\Data');
					return new Users($adapter, $db, $role, $ud);
				},
				'PM\Model\FusionCharts' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new FusionCharts($adapter, $db);
				},
				'PM\Model\Invoices' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$li = $sm->get('PM\Model\Invoices\LineItems');
					return new Invoices($adapter, $db, $li);
				},
				'PM\Model\Invoices\LineItems' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new LineItems($adapter, $db);
				},
				
				//forms
				'PM\Form\ProjectForm' => function($sm) {
					return new ProjectForm('project', $sm->get('PM\Model\Companies'), $sm->get('PM\Model\Options'));
				},	
				'PM\Form\CompanyForm' => function($sm) {
					return new CompanyForm('company');
				},	
				'PM\Form\BookmarkForm' => function($sm) {
					return new BookmarkForm('bookmark');
				},	
				'PM\Form\NoteForm' => function($sm) {
					return new NoteForm('note');
				},	
				'PM\Form\ContactForm' => function($sm) {
					return new ContactForm('contact', $sm->get('PM\Model\Companies'), $sm->get('PM\Model\Options'));
				},
				'PM\Form\TaskForm' => function($sm) {
					return new TaskForm('task', $sm->get('PM\Model\Options'), $sm->get('PM\Model\Projects'));
				},
				'PM\Form\IpForm' => function($sm) {
					return new IpForm('ip');
				},
				'PM\Form\ConfirmForm' => function($sm) {
					return new ConfirmForm('confirm');
				},
				'PM\Form\OptionForm' => function($sm) {
					$options = $sm->get('PM\Model\Options');
					return new OptionForm('options', $options);
				},
				'PM\Form\TimeForm' => function($sm) {
					
					$auth = $sm->get('AuthService');
					$perm = $sm->get('Application\Model\Permissions');
					$companies = $sm->get('PM\Model\Companies');
					if($perm->check($auth->getIdentity(), 'view_companies'))
					{
						$types = array('1', '6');
						$options = \PM\Model\Options\Companies::companies($companies, TRUE, FALSE, $types);
					}
					else
					{
						$user = $sm->get('PM\Model\Users');
						$projects = $user->getAssignedProjects($auth->getIdentity());
						$ids = array();
						foreach($projects AS $project)
						{
							$ids[$project['company_id']] = $project['company_id'];
						}
							
						$options = \PM\Model\Options\Companies::companies($companies, TRUE, FALSE, FALSE, $ids);
					}	
					
					return new TimeForm('time', $options);
				},
				'PM\Form\TimerForm' => function($sm) {
					return new TimerForm('timer');
				},
				'PM\Form\FileForm' => function($sm) {
					$file = $sm->get('PM\Model\Files');
					return new FileForm('files', $file);
				},
				'PM\Form\File\RevisionForm' => function($sm) {
					$file = $sm->get('PM\Model\Files'); 
					return new RevisionForm('file_revisions', $file);
				},	
				'PM\Form\PrefsForm' => function($sm) {
					return new PrefsForm('preferences');
				},	
				'PM\Form\InvoiceForm' => function($sm) {
					return new InvoiceForm('invoice_form');
				},	
				'PM\Form\SettingsForm' => function($sm) {
					return new SettingsForm('settings', $sm->get('PM\Model\Companies'), $sm->get('PM\Model\Options'));
				},		
				
				//events
				'PM\Event\ActivityLogEvent' => function($sm) {
				    $auth = $sm->get('AuthService');
				    $al = $sm->get('PM\Model\ActivityLog');
					return new ActivityLogEvent($al, $auth->getIdentity());
				},
				'PM\Event\NotificationEvent' => function($sm) {
				    $auth = $sm->get('AuthService');
				    $mail = $sm->get('Application\Model\Mail');
				    $user = $sm->get('PM\Model\Users');
				    $task = $sm->get('PM\Model\Tasks');
				    $project = $sm->get('PM\Model\Projects');
					return new NotificationEvent($mail, $user, $project, $task, $auth->getIdentity());
				},	
				'PM\Event\SettingsEvent' => function($sm) {
					return new SettingsEvent();
				},	
				'PM\Event\UserDataEvent' => function($sm) {
					return new UserDataEvent(); 
				},								
			),
    	);
    }    
}
