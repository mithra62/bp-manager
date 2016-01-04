<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/GlobalAlerts.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Global Alerts View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/GlobalAlerts.php
 */
class GlobalAlerts extends BaseViewHelper
{   
	public function __invoke($id)
	{
		if(!$id)
		{
			return FALSE;
		}
		
		$return = '';
		
		$helperPluginManager = $this->getServiceLocator();
		$serviceManager = $helperPluginManager->getServiceLocator();
		
		$user = $serviceManager->get('PM\Model\Users');
		$overdue_tasks = $user->userHasOverdueTasks($id);
		if($overdue_tasks && is_array($overdue_tasks) && $overdue_tasks['total_count'] >= 1)
		{
			$return .= '<div class="global-alert global-fail"><div>You have <a href="'.$this->view->url('pm', array('module' => 'pm','controller'=>'index','action'=>'index'), null, TRUE).'" style="text-decoration:none; color: #CC3300">'.$overdue_tasks['total_count'].' overdue tasks</a>.</div></div>';
		}
		
		$timer = $serviceManager->get('PM\Model\Timers');
		$timer_data = $timer->getTimerData(array('user_id' => $id));
		if($timer_data)
		{
			if(isset($timer_data['task_id']))
			{
				$task = $serviceManager->get('PM\Model\Tasks');
				$task_data = $task->getTaskById($timer_data['task_id']);
				$timer_data = array_merge($timer_data, $task_data);
			}
			elseif(isset($timer_data['project_id']))
			{
				$project = $serviceManager->get('PM\Model\Projects');
				$project_data = $project->getProjectById($timer_data['project_id'], array('name', 'company_id'));
				$timer_data = array_merge($timer_data, $project_data);
			}
			elseif(isset($timer_data['company_id']))
			{
				$company = $serviceManager->get('PM\Model\Companies');
				$company_data = $company->getCompanyById($timer_data['company_id'], array('c.name AS name'));
				$timer_data = array_merge($timer_data, $company_data);
			}
			
			if(isset($timer_data['name']))
			{
				$return .= '<div class="global-alert global-information"><div class="timer-alert"> <a href="'.$this->view->url('timers/stop', array('module' => 'pm','controller'=>'timers','action'=>'stop'), null, TRUE).'" rel="facebox" style="text-decoration:none; color: #0033FF">Timer running for '.$timer_data['name'].': <span id="timer_countdown"></span></a></div></div>';
				$return .= "<script>$('#timer_countdown').countdown({since: new Date('".$timer->makeCountdownDate($timer_data['start_time'])."'), compact: true, format: 'yowdhmS', description: ''});</script>";
			}					
		}
		
		return $return;
		//return $return;
	}
}