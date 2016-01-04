<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/ActivityLogEvent.php
 */

namespace PM\Event;

use Base\Event\BaseEvent;

 /**
 * PM - Event Activity Log
 *
 * @package 	Events
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/ActivityLogEvent.php
 */
class ActivityLogEvent extends BaseEvent
{	
    /**
     * Event Manager Model
     * @var \PM\Model\ActivityLog
     */
    public $al = false;
    
    /**
     * User Identity
     * @var int
     */
    public $identity = false;
    
    /**
     * The project data that's to be removed
     * @var array
     */
    private $removed_project_data = array();
    
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'project.update.pre' => 'logProjectUpdate',
        'project.add.post' => 'logProjectAdd',
        'project.addteam.post' => 'logProjectTeamAdd',
    	'project.removeteammember.pre' => 'logProjectTeamRemove',
        'task.add.post' => 'logTaskAdd',
        'task.update.pre' => 'logTaskUpdate',
    	'task.assign.post' => 'logTaskAssignment',
    	'task.remove.pre' => 'logTaskRemove',
    	'note.add.post' => 'logNoteAdd',
    	'note.update.post' => 'logNoteUpdate',
    	'note.remove.pre' => 'logNoteRemove',
    	'bookmark.add.post' => 'logBookmarkAdd',
    	'bookmark.update.post' => 'logBookmarkUpdate',
    	'bookmark.remove.pre' => 'logBookmarkRemove',
    	'file.add.post' => 'logFileAdd',
    	'file.update.post' => 'logFileUpdate',
    	'file.remove.pre' => 'logFileRemove',
    	'file.revision.add.post' => 'logFileRevisionAdd',
    	'file.revision.remove.pre' => 'logFileRevisionRemove',
    );
    
    /**
     * Activity Log Event
     * @param \PM\Model\ActivityLog $al
     * @param int $identity
     */
    public function __construct( \PM\Model\ActivityLog $al, $identity)
    {
        $this->al = $al;
        $this->identity = $identity;
    }
    
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
	 * Wrapper to log a task add entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logTaskAdd(\Zend\EventManager\Event $event)
	{
		$data = $event->getParam('data');
		$task_id = $event->getParam('task_id');
		$project_id = $data['project_id'];
		$data = array('stuff' => $data, 'project_id' => $project_id, 'task_id' => $task_id, 'type' => 'task_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a task update entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logTaskUpdate(\Zend\EventManager\Event $event)
	{
		$data = $event->getParam('data');
		$task_id = $event->getParam('task_id');
		$project_id = $data['project_id'];
		$data = array('stuff' => $data, 'project_id' => $project_id, 'type' => 'task_update', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}	
	
	/**
	 * Wrapper to log a task assignment entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logTaskAssignment(\Zend\EventManager\Event $event)
	{
		$task_id = $event->getParam('task_id');
		$assigned_to = $event->getParam('assigned_to');
		$task = $event->getTarget();
		$task_data = $task->getTaskById($task_id);
		$project_id = $task_data['project_id'];
		$data = array('stuff' => $task_data, 'project_id' => $task_data['project_id'], 'type' => 'task_assigned', 'performed_by' => $this->identity, 'user_id' => $assigned_to);
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a task removal
	 * @param \Zend\EventManager\Event $event
	 */
	public function logTaskRemove(\Zend\EventManager\Event $event)
	{
		$data = $event->getParam('data');
		$task_id = $event->getParam('task_id');
		$project_id = $data['project_id'];
		$data = array('stuff' => $data, 'project_id' => $project_id, 'type' => 'task_remove', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}	
	
	/**
	 * Wrapper to log a project update entry
	 * @todo Check for existance of a project add log before creating a new one
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function logProjectAdd(\Zend\EventManager\Event $event)
	{
		$data = $event->getParam('data');
		$project_id = $event->getParam('project_id');
		$company_id = $data['company_id'];
		$data = array('stuff' => $data, 'project_id' => $project_id, 'company_id' => $company_id, 'type' => 'project_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}
	
	/**
	 * Wrapper to log a project update entry
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function logProjectUpdate(\Zend\EventManager\Event $event)
	{
	    $data = $event->getParam('data');
	    $project_id = $event->getParam('project_id');
	    $data = array('stuff' => $data, 'project_id' => $project_id, 'type' => 'project_update', 'performed_by' => $this->identity);    
		$this->al->logActivity($data);
	}
	
	/**
	 * Prepares the project data for use by the logger after the project is actually removed
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function prepLogProjectRemove(\Zend\EventManager\Event $event)
	{
	    $project_id = $event->getParam('id');
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$this->removed_project_data = $project->getProjectById($project_id);
	}
	
	/**
	 * Wrapper to log a project removal
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function logProjectRemove(\Zend\EventManager\Event $event)
	{
	    $project_id = $event->getParam('id');
	    $project = $this->getServiceLocator()->get('PM\Model\Projects');
	    $data = (!empty($this->removed_project_data) ? $this->removed_project_data : '');
	    $data = array('stuff' => $data, 'project_id' => $project_id, 'type' => 'project_remove', 'performed_by' => $this->identity);    
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a team member removal from a project
	 * @param \Zend\EventManager\Event $event
	 */
	public function logProjectTeamRemove(\Zend\EventManager\Event $event)
	{
		$user_id = $event->getParam('user_id');
		$project_id = $event->getParam('project_id');
		$project = $event->getTarget();
		$stuff = $project_team = $project->getProjectTeamMemberIds($project_id);
		$data = array('stuff' => $stuff, 'user_id' => $user_id, 'project_id' => $project_id, 'type' => 'project_team_remove', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}

	/**
	 * Wrapper to log a team member being added to a project
	 * @param \Zend\EventManager\Event $event
	 */
	public function logProjectTeamAdd(\Zend\EventManager\Event $event)
	{
		$user_id = $event->getParam('id');
		$project_id = $event->getParam('project');
		$data = array('user_id' => $user_id, 'project_id' => $project_id, 'type' => 'project_team_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a note update creation
	 * @param \Zend\EventManager\Event $event
	 */
	public function logNoteAdd(\Zend\EventManager\Event $event)
	{	
		$note_id = $event->getParam('note_id');
		$data = $event->getParam('data');
		$data = $this->filterForKeys($data);
		$data = array('stuff' => $data, 'note_id' => $note_id, 'project_id' => $data['project_id'], 'company_id' => $data['company_id'], 'task_id' => $data['task_id'], 'type' => 'note_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}
	
	/**
	 * Wrapper to log a note update entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logNoteUpdate(\Zend\EventManager\Event $event)
	{
		$note_id = $event->getParam('note_id');
		$data = $event->getParam('data');
		$data = $this->filterForKeys($data);
		$data = array('stuff' => $data, 'note_id' => $note_id, 'project_id' => $data['project_id'], 'company_id' => $data['company_id'], 'task_id' => $data['task_id'], 'type' => 'note_update', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}
	
	/**
	 * Wrapper to log a note removal
	 * @param \Zend\EventManager\Event $event
	 */
	public function logNoteRemove(\Zend\EventManager\Event $event)
	{
		$note_id = $event->getParam('note_id');
		$note = $event->getTarget();
		$note_data = $note->getNoteById($note_id);
		$data = array('stuff' => $note_data, 'note_id' => $note_id, 'project_id' => $note_data['project_id'], 'company_id' => $note_data['company_id'], 'task_id' => $note_data['task_id'], 'type' => 'note_remove', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}
	
	/**
	 * Wrapper to log a bookmark creation
	 * @param \Zend\EventManager\Event $event
	 */
	public function logBookmarkAdd(\Zend\EventManager\Event $event)
	{
		$bookmark_id = $event->getParam('bookmark_id');
		$bookmark = $event->getTarget();
		$bookmark_data = $bookmark->getBookmarkById($bookmark_id);
		$data = array('stuff' => $bookmark_data, 'bookmark_id' => $bookmark_id, 'project_id' => $bookmark_data['project_id'], 'company_id' => $bookmark_data['company_id'], 'task_id' => $bookmark_data['task_id'], 'type' => 'bookmark_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}
	
	/**
	 * Wrapper to log a bookmark update entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logBookmarkUpdate(\Zend\EventManager\Event $event)
	{
		$bookmark_id = $event->getParam('bookmark_id');
		$bookmark = $event->getTarget();
		$bookmark_data = $bookmark->getBookmarkById($bookmark_id);
		$data = array('stuff' => $bookmark_data, 'bookmark_id' => $bookmark_id, 'project_id' => $bookmark_data['project_id'], 'company_id' => $bookmark_data['company_id'], 'task_id' => $bookmark_data['task_id'], 'type' => 'bookmark_update', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}
	
	/**
	 * Wrapper to log a bookmark removal
	 * @param \Zend\EventManager\Event $event
	 */
	public function logBookmarkRemove(\Zend\EventManager\Event $event)
	{
		$bookmark_id = $event->getParam('bookmark_id');
		$bookmark = $event->getTarget();
		$bookmark_data = $bookmark->getBookmarkById($bookmark_id);
		$data = array('stuff' => $bookmark_data, 'bookmark_id' => $bookmark_id, 'project_id' => $bookmark_data['project_id'], 'company_id' => $bookmark_data['company_id'], 'task_id' => $bookmark_data['task_id'], 'type' => 'bookmark_remove', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}

	/**
	 * Wrapper to log a file entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logFileAdd(\Zend\EventManager\Event $event)
	{
		$file_id = $event->getParam('file_id');
		$file = $event->getTarget();
		$file_data = $file->getFileById($file_id);
		$data = array('stuff' => $file_data, 'file_id' => $file_id, 'project_id' => $file_data['project_id'], 'company_id' => $file_data['company_id'], 'task_id' => $file_data['task_id'], 'type' => 'file_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);	
	}
	
	/**
	 * Wrapper to log a file update entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logFileUpdate(\Zend\EventManager\Event $event)
	{
		$file_id = $event->getParam('file_id');
		$file = $event->getTarget();
		$file_data = $file->getFileById($file_id);
		$data = array('stuff' => $file_data, 'file_id' => $file_id, 'project_id' => $file_data['project_id'], 'company_id' => $file_data['company_id'], 'task_id' => $file_data['task_id'], 'type' => 'file_update', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a file removal
	 * @param \Zend\EventManager\Event $event
	 */
	public function logFileRemove(\Zend\EventManager\Event $event)
	{
		$file_id = $event->getParam('file_id');
		$file = $event->getTarget();
		$file_data = $file->getFileById($file_id);
		$data = array('stuff' => $file_data, 'file_id' => $file_id, 'project_id' => $file_data['project_id'], 'company_id' => $file_data['company_id'], 'task_id' => $file_data['task_id'], 'type' => 'file_remove', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a file revision entry
	 * @param \Zend\EventManager\Event $event
	 */
	public function logFileRevisionAdd(\Zend\EventManager\Event $event)
	{
		$revision_id = $event->getParam('revision_id');
		$data = $event->getParam('data');
		$file = $event->getTarget();
		
		//we don't want to log the first entry since we're already logging the master file being added
		$total_revisions = $file->revision->getTotalFileRevisions($data['file_data']['id']);
		if($total_revisions > 1)
		{
			$data = array('stuff' => $data, 'file_rev_id' => $revision_id, 'file_id' => $data['file_data']['id'], 'project_id' => $data['file_data']['project_id'], 'company_id' => $data['file_data']['company_id'], 'task_id' => $data['file_data']['task_id'], 'type' => 'file_revision_add', 'performed_by' => $this->identity);
			$this->al->logActivity($data);
		}
	}
	
	/**
	 * Wrapper to log a file revision removal
	 * @param \Zend\EventManager\Event $event
	 */
	public function logFileRevisionRemove(\Zend\EventManager\Event $event)
	{
		$revision_id = $event->getParam('revision_id');
		$file = $event->getTarget();
		$revision_data = $file->revision->getRevision($revision_id);
		$file_data = $file->getFileById($revision_data['file_id']);
		$data = array('stuff' => $data, 'file_rev_id' => $revision_id, 'file_id' => $revision_data['file_id'], 'project_id' => $file_data['project_id'], 'company_id' => $file_data['company_id'], 'task_id' => $file_data['task_id'], 'type' => 'file_revision_remove', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}	
	
	/**
	 * Takes the array and verifies the existance of the primary keys
	 * @param $data
	 */
	private function filterForKeys(array $data)
	{
		$data['company_id'] = $data['project_id'] = $data['task_id'] = 0;
		if(array_key_exists('company', $data))
		{
			$data['company_id'] = $data['company'];
		}
	
		if(array_key_exists('project', $data))
		{
			$data['project_id'] = $data['project'];
		}
	
		if(array_key_exists('task', $data))
		{
			$data['task_id'] = $data['task'];
		}
	
		return $data;
	}	
}