<?php
require_once( dirname(APPLICATION_PATH) . DS.'library'.DS.'HarvestAPI'.DS.'HarvestAPI.php');
spl_autoload_register(array('HarvestAPI', 'autoload') );

/**
 * Wrapper for the Harvest API Library
 * @author Eric
 *
 */
class PM_Model_Rest_Harvest extends HarvestAPI
{
	public function __construct($user, $pass, $account, $ssl = FALSE)
	{
		$this->setRetryMode( HarvestAPI::RETRY );
		$this->setUser( $user );
		$this->setPassword( $pass );
		$this->setAccount( $account );
		$this->setSSL($ssl);		
	}
	
	/**
	 * Grabs all the client entries from Harvest and converts it to an array
	 */
	public function getClients()
	{
    	$result = parent::getClients();
		if( $result->isSuccess() ) 
		{
			return $this->_translateClients($result->data);
		} 		
	}
	
	/**
	 * Parses the object and converts it to a simple array
	 * @param object $data
	 */
	private function _translateClients($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $client ) 
		{
			$arr[$i]['name'] = $client->name;
			$arr[$i]['harvest_client_id'] = $client->id;
			$arr[$i]['description'] = $client->details;
			$i++;
		}
		return $arr;	
	}
	
	public function getUsers()
	{
    	$result = parent::getUsers();
		if( $result->isSuccess() ) 
		{
			return $this->_translateUsers($result->data);
		} 
	}
	
	private function _translateUsers($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $user ) 
		{
			$arr[$i]['first_name'] = $user->get("first-name");
			$arr[$i]['last_name'] = $user->get("last-name");
			$arr[$i]['harvest_user_id'] = $user->id;
			$arr[$i]['email'] = $user->email;
			$arr[$i]['password'] = md5($user->email);
			$i++;
		}
		return $arr;	
	}

	public function getUserEntries($harvest_user_id, $range, $project = fALSE)
	{
    	$result = parent::getUserEntries($harvest_user_id, $range, $project);
		if( $result->isSuccess() ) 
		{
			return $this->_translateUserEntries($result->data);
		} 
	}
	
	private function _translateUserEntries($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $entry ) 
		{
			$arr[$i]['date'] = $entry->get("spent-at");
			$arr[$i]['hours'] = $entry->get("hours");
			$arr[$i]['harvest_task_id'] = $entry->get("task-id");
			$arr[$i]['harvest_project_id'] = $entry->get("project-id");
			$arr[$i]['harvest_user_id'] = $entry->get("user-id");
			$arr[$i]['is_closed'] = $entry->get("is-closed");
			$arr[$i]['notes'] = $entry->get("notes");
			$arr[$i]['is_billed'] = $entry->get("is-billed");
			$arr[$i]['harvest_time_id'] = $entry->get("id");
			$i++;
		}
		return $arr;	
	}
	
	public function getProjects()
	{
    	$result = parent::getProjects();
		if( $result->isSuccess() ) 
		{
			return $this->_translateProjects($result->data);
		} 		
	}
	
	private function _translateProjects($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $project ) 
		{

			$arr[$project->get("client-id")][$i]['name'] = $project->name;
			$arr[$project->get("client-id")][$i]['harvest_project_id'] = $project->id;
			$arr[$project->get("client-id")][$i]['description'] = $project->notes;
			$arr[$project->get("client-id")][$i]['assigned_tasks_count'] = $project->get("active-task-assignments-count");
			$arr[$project->get("client-id")][$i]['assigned_user_count'] = $project->get("active-user-assignments-count");	
			$arr[$project->get("client-id")][$i]['status'] = 6;	
			if((string)$project->active == 'true')
			{
				$arr[$project->get("client-id")][$i]['status'] = 3;
			}
			
			$arr[$project->get("client-id")][$i]['start_date'] = $this->_translateDate($project->get("created-at"));		
			$i++;
		}
		return $arr;	
	}
	
	public function getProjectTaskAssignments($project_id)
	{
    	$result = parent::getProjectTaskAssignments($project_id);
		if( $result->isSuccess() ) 
		{
			return $this->_translateProjectTaskAssignments($result->data);
		} 		
	}
	
	private function _translateProjectTaskAssignments($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $task_assign ) 
		{
			$arr[$i]['task_id'] = $task_assign->get("task-id");
			$i++;
		}
		return $arr;		
	}
	
	public function getProjectUserAssignments($project_id)
	{
    	$result = parent::getProjectUserAssignments($project_id);
		if( $result->isSuccess() ) 
		{
			return $this->_translateProjectUserAssignments($result->data);
		} 		
	}
	
	private function _translateProjectUserAssignments($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $user_assign ) 
		{
			$arr[$i]['harvest_project_id'] = $user_assign->get("project-id");
			$arr[$i]['harvest_user_id'] = $user_assign->get("user-id");
			$i++;
		}
		return $arr;		
	}
	
	public function getContacts()
	{
    	$result = parent::getContacts();
		if( $result->isSuccess() ) 
		{
			return $this->_translateContacts($result->data);
		} 		
	}
	
	private function _translateContacts($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $contact ) 
		{
			$arr[$contact->get("client-id")][$i]['email'] = $contact->email;
			$arr[$contact->get("client-id")][$i]['harvest_id'] = $contact->id;
			$arr[$contact->get("client-id")][$i]['first_name'] = $contact->first-name;
			$arr[$contact->get("client-id")][$i]['last_name'] = $contact->last-name;
			$arr[$contact->get("client-id")][$i]['mobile'] = $contact->phone-mobile;
			$arr[$contact->get("client-id")][$i]['title'] = $contact->title;
			$arr[$contact->get("client-id")][$i]['phone'] = $contact->phone-office;
			$i++;
		}
		return $arr;	
	}	
	
	public function getTasks()
	{
    	$result = parent::getTasks();
		if( $result->isSuccess() ) 
		{
			return $this->_translateTasks($result->data);
		} 		
	}
	
	private function _translateTasks($data)
	{
		$arr = array();
		$i = 0;
		foreach( $data as $task ) 
		{
			$arr[$task->get("id")]['harvest_task_id'] = $task->id;
			$arr[$task->get("id")]['name'] = $task->name;
			$arr[$task->get("id")]['description'] = 'Imported from Harvest';
			$i++;
		}
		return $arr;	
	}	

	private function _translateDate($date)
	{
		$date = date('Y-m-d H:i:s', strtotime($date));
		return $date;
	}
}