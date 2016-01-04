<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/DashboardTimeline.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Global Alerts View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/DashboardTimeline.php
 */
class DashboardTimeline extends BaseViewHelper
{
    	
	/**
	 * @ignore
	 * @return unknown
	 */
	public function __invoke()
	{
	    $helperPluginManager = $this->getServiceLocator();
	    $serviceManager = $helperPluginManager->getServiceLocator();
	    $activity = $serviceManager->get('PM\Model\ActivityLog');

	    $filter = $this->compileFilter($serviceManager);
		$identity = $this->getIdentity();
		$logs = $activity->getUsersProjectActivity($identity, $filter);
		if(count($logs) >= 1)
		{
			$count = 0;
			$html = array();
			foreach($logs AS $log)
			{
				$html[$count] = $log;
				$html[$count]['project_name'] = $log['project_name'];				
				$html[$count]['title'] = $this->determineTitle($log);
				$html[$count]['href'] = $this->determineURL($log);
				$html[$count]['image'] = $this->determineImage($log);
				$html[$count]['action_title'] = $this->determineActionTitle($log);
				$html[$count]['rel'] = $this->determineRel($log);
				$html[$count]['date'] = $log['date'];
				$count++; 
			}
			
			return $html;
		}
	}
	
	/**
	 * Compiles the SQL WHERE filtering to do for pulling activity log info
	 * @param \Zend\ServiceManager\ServiceManager $serviceManager
	 */
	private function compileFilter(\Zend\ServiceManager\ServiceManager $serviceManager)
	{
		$route = $serviceManager->get('Application')->getMvcEvent()->getRouteMatch();
		$routeMatch = $route->getMatchedRouteName();
		$route_params = $route->getParams();
		
		$return = false;
		switch($routeMatch)
		{
			case 'projects/edit':
			case 'tasks/add':
				$return = array('project_id' => $route_params['project_id']);
			break;

			case 'notes/add':
			case 'bookmarks/add':
			case 'files/add':
				$type = $route_params['type'];
				if($type == 'project')
				{
					$return = array('project_id' => $route_params['id']);
				}
			break;
		}
		
		return $return;
	}
	
	/**
	 * Determines the value for the rel parameter on hrefs
	 * @param array $data
	 * @return string|boolean
	 */
	private function determineRel(array $data = array())
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{	
				case 'note_add':
				case 'note_remove':
				case 'note_update':
				case 'bookmark_add':
				case 'bookmark_remove':
				case 'bookmark_update':
				case 'file_revision_remove':
				case 'file_review_remove':
				case 'file_review_add':
					return 'facebox';
				break;

				case 'project_add':				
				case 'project_remove':			
				case 'project_update':
				case 'project_team_add':
					if(isset($data['project_name']) && $data['project_name'] != '')
					{
						return FALSE;
					}
					return 'facebox';
				break;
				
				case 'task_add':				
				case 'task_remove':				
				case 'task_update':
				case 'task_assigned':
					if(isset($data['task_name']) && $data['task_name'] != '')
					{
						return FALSE;
					}
					//return 'facebox';
				break;
				
				case 'file_add':	
				case 'file_remove':	
				case 'file_update':
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return FALSE;
					} 
					return 'facebox';
				break;				
			}	
		}
	}
	
	/**
	 * Determines the title to use for the href
	 * @param array $data
	 * @return string|unknown
	 */
	private function determineActionTitle(array $data = array())
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{	
				case 'bookmark_add':
					return 'Bookmark Added';
				break;
				case 'bookmark_remove':
					return 'Bookmark Removed';
				break;
				case 'bookmark_update':
					return 'Bookmark Updated';
				break;
	
				case 'note_add':
					return 'Note Added';
				break;
				case 'note_remove':
					return 'Note  Removed';
				break;
				case 'note_update':
					return 'Note Updated';
				break;
	
				case 'project_add':
					return 'Project Added';
				break;					
				case 'project_remove':
					return 'Project Removed';
				break;					
				case 'project_update':
					return 'Project Updated';
				break;
				case 'project_team_add':
				case 'project_team_remove':
					return 'Project Team Updated';
				break;
				
				case 'task_add':
					return 'Task Added';
				break;					
				case 'task_remove':
					return 'Task Removed';
				break;					
				case 'task_update':
					return 'Task Updated';
				break;
				case 'task_assigned':
					return 'Task Assigned';
				break;
				
				case 'file_add':
					return 'File Added';
				break;					
				case 'file_remove':
					return 'File Removed';
				break;					
				case 'file_update':
					return 'File Updated';
				break;
				case 'file_revision_remove':
					return 'File Revision Removed';
				break;
				
				case 'file_revision_add':
					return 'File Revision Added';
				break;
				
				case 'file_review_remove':
					return 'File Review Removed';
				break;
				case 'file_review_add':
					return 'File Review Added';
				break;				
				
				default:
					return $data['type'];
				break;				
			}	
		}
	}
	
	/**
	 * Determines the URL to use
	 * @param array $data
	 */
	private function determineURL(array $data = array())
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{
				case 'bookmark_add':
				case 'bookmark_remove':
				case 'bookmark_update':
					if(isset($data['bookmark_name']) && $data['bookmark_name'] != '')
					{
						return $this->view->url('bookmarks/view', array('bookmark_id' => $data['bookmark_id']));
					}
				break;
	
				case 'note_add':
				case 'note_remove':
				case 'note_update':
					if(isset($data['note_subject']) && $data['note_subject'] != '')
					{
						return $this->view->url('notes/view', array('note_id' => $data['note_id']));
					}
				break;
	
				case 'project_add':				
				case 'project_remove':			
				case 'project_update':
				case 'project_team_add':
				case 'project_team_remove':
					if(isset($data['project_name']) && $data['project_name'] != '') {
						return $this->view->url('projects/view', array('project_id' => $data['project_id']));
					}
				break;
				
				case 'task_add':				
				case 'task_remove':				
				case 'task_update':
				case 'task_assigned':
					if(isset($data['task_name']) && $data['task_name'] != '') {
						return $this->view->url('tasks/view', array('task_id' => $data['task_id']));
					} else {
						$stuff = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
						if(!empty($stuff['name']) && !empty($stuff['id'])){
							return $this->view->url('tasks/view', array('task_id' => $stuff['id']));
						}
					}
				break;
				
				case 'file_add':	
				case 'file_remove':	
				case 'file_update':
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $this->view->url('files/view', array('file_id' => $data['file_id']));
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);

				break;
				
				case 'file_review_add':	
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $this->view->url(array('module'=> 'pm', 'controller'=>'files','action'=>'view-review', 'id' => $data['file_review_id']), null, TRUE);
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
				break;	

				case 'file_revision_add':	
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $this->view->url('files/view', array('file_id' => $data['file_id']));
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
				break;					
			}
			return $this->view->url('pm', array('module'=> 'pm', 'controller'=>'activity','action'=>'view', 'id' => $data['id']), null, TRUE);
		}
	}
	
	/**
	 * Determine's the icon image to use
	 * @param array $data
	 * @return string
	 */
	private function determineImage(array $data = array())
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{
				case 'bookmark_add':
					return 'bookmark_add.png';
				break;					
				case 'bookmark_remove':
					return 'bookmark_remove.png';
				break;					
				case 'bookmark_update':
					return 'bookmark_edit.png';
				break;

				case 'note_add':
					return 'note_add.png';
				break;					
				case 'note_remove':
					return 'note_remove.png';
				break;					
				case 'note_update':
					return 'note_edit.png';
				break;

				case 'project_add':
				case 'project_remove':
				case 'project_update':
					return 'database_32.png';
				break;

				case 'project_team_add':
				case 'project_team_remove':
					return 'users_business_32.png';
				break;
				
				case 'task_add':
					return 'task_add.png';
				break;					
				case 'task_remove':
					return 'task_remove.png';
				break;					
				case 'task_update':
					return 'task_edit.png';
				break;
				
				case 'file_add':
					return 'file_add.png';
				break;					
				case 'file_remove':
					return 'file_remove.png';
				break;					
				case 'file_update':
					return 'file_edit.png';
				break;
				
				case 'file_review_add':
					return 'comment_add.png';
				break;
				case 'file_review_remove':
					return 'comment_remove.png';
				break;				
				
				case 'file_revision_add':
					return 'version_history_add.png';
				break;
				case 'file_revision_remove':
					return 'version_history_remove.png';
				break;
				
				case 'task_assigned':
					return 'history_32.png';
				break;
				
				default:
					return 'home_32.png';
				break;
			}
		}
	}
	
	/**
	 * Determines the title to use
	 * @param array $data
	 * @return unknown|Ambigous <Ambigous <\Zend\Json\mixed, \Zend\Json\$_tokenValue, NULL>, Ambigous <\Zend\Json\mixed, \Zend\Json\$_tokenValue, multitype:, multitype:Ambigous <\Zend\Json\mixed, \Zend\Json\$_tokenValue, NULL> , NULL>>|string
	 */
	private function determineTitle(array $data = array())
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{
				case 'bookmark_add':
				case 'bookmark_remove':
				case 'bookmark_update':
					
					if(isset($data['bookmark_name']) && $data['bookmark_name'] != '')
					{
						return $data['bookmark_name']; 
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
					if(isset($data['stuff']['name']) && $data['stuff']['name'] != '')
					{
						return $data['stuff']['name'];
					}
					
				break;
	
				case 'note_add':
				case 'note_remove':
				case 'note_update':
					if(isset($data['note_subject']) && $data['note_subject'] != '')
					{
						return $data['note_subject']; 
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
					if(isset($data['stuff']['subject']) && $data['stuff']['subject'] != '')
					{
						return $data['stuff']['subject'];
					}
				break;
	
				case 'project_add':
					return 'Project Added';
				break;					
				case 'project_remove':
					return 'Project Removed';
				break;					
				case 'project_update':
					return 'Project Updated';
				break;
				case 'project_team_add':
				case 'project_team_remove':
					return 'Project Team Updated';
				break;
				
				case 'task_add':	
				case 'task_remove':					
				case 'task_update':
				case 'task_assigned':
					
					if(isset($data['task_name']) && $data['task_name'] != '')
					{
						return $data['task_name']; 
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
					if(isset($data['stuff']['name']) && $data['stuff']['name'] != '')
					{
						return $data['stuff']['name'];
					}
					return 'Nothing here...';
					
				break;
				
				case 'file_add':	
				case 'file_remove':	
				case 'file_update':
				case 'file_revision_remove':
				case 'file_revision_add':
				case 'file_review_remove':
				case 'file_review_add':
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $data['file_name']; 
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
					if(isset($data['stuff']['file_name']) && $data['stuff']['file_name'] != '')
					{
						return $data['stuff']['file_name'];
					}
				break;				
				
				default:
					return 'home_32.png';
				break;
			}
		}
	}
}