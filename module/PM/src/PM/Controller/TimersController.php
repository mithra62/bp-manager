<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/TimersController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Timers Controller
 *
 * Routes the Timers requests
 *
 * @package 	TimeTracker\Timers
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/TimersController.php
*/
class TimersController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		parent::check_permission('track_time');
		//$this->layout()->setVariable('layout_style', 'single');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('sub_menu', 'tasks');
		$this->layout()->setVariable('active_nav', 'timers');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
		$this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');
	
		return $e;
	}
    
    public function indexAction()
    {
    	$date = $this->_request->getParam('date', date('F Y'));
    }
    
    public function viewAction()
    {	
    	$id = $this->params()->fromRoute('id');
    	$type = $this->params()->fromRoute('type');
    	$view = array('type' => $type, 'id' => $id);
    	
    	$where = array();
    	if($type == 'company')
    	{
    		parent::check_permission('view_companies');
		    $company_id = $id;
    		$company = $this->getServiceLocator()->get('PM\Model\Companies');
    		$company_data = $company->getCompanyById($company_id);
    		if(!$company_data)
    		{
    			return $this->redirect()->toRoute('companies');
    		}
    		
    		$view['company'] = $company_data;
    	}
    	 
    	if($type == 'project')
    	{
		    $project_id = $id;
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
			    return $this->redirect()->toRoute('projects');
			}
				
    		if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('projects');
    		}

    		$view['project'] = $project_data;
    	}
    	
    	if($type == 'task')
    	{
    		$task_id = $id;
    		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');
    		$task_data = $task->getTaskById($task_id);
    		if(!$task_data)
    		{
    			return $this->redirect()->toRoute('tasks');
    		}
    			
    		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('pm');
    		}

    		$view['task'] = $task_data;
    	}    	
    	
    	return $this->ajaxOutput($view);
    }
    
    /**
     * Action to remove a timer
     * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
     */
    public function removeAction()
    {
    	$timer = $this->getServiceLocator()->get('PM\Model\Timers');
		$timer->clearTimerData($this->identity);
        $this->flashMessenger()->addMessage($this->translate('timer_removed', 'pm'));
        return $this->redirect()->toRoute('pm');    	
    }
    
    /**
     * Action to start a timer
     * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|multitype:
     */
    public function startAction()
    {
    	if ($this->getRequest()->isPost()) 
        { 
    		$id = $this->params()->fromRoute('id');
    		$type = $this->params()->fromRoute('type');
    		$timer = $this->getServiceLocator()->get('PM\Model\Timers');
        	if($type == 'task')
        	{
        		$timer_data = $timer->startTaskTimer($this->identity, $id);
        		$return = array('route' => 'tasks/view', 'options' => array('task_id' => $id));
        	} 
        	elseif($type == 'project')
        	{
        		$timer_data = $timer->startProjectTimer($this->identity, $id);
        		$return = array('route' => 'projects/view', 'options' => array('project_id' => $id));
        	}
        	elseif($type == 'company')
        	{
        		$timer_data = $timer->startCompanyTimer($this->identity, $id);
        		$return = array('route' => 'companies/view', 'options' => array('company_id' => $id));
        	}
        	
        	
        	if($timer_data)
        	{
        		$this->flashMessenger()->addMessage($this->translate('timer_started', 'pm'));
        		return $this->redirect()->toRoute($return['route'], $return['options']);        	
        	}
        	else
        	{
        		$this->view->errors = array('Couldn\'t start timer...');
        	}
        } 

        return array();
    }
    
    public function stopAction()
    {
    	if($this->prefs['timer_data'] == '')
    	{
    		return $this->redirect()->toRoute('pm');
    	}

    	$task_id = $project_id = $company_id = 0;
    	$timer = $this->getServiceLocator()->get('PM\Model\Timers');
    	$timer_data = $timer->decodeTimerData($this->prefs['timer_data']);

    	$view = array();
    	if(!empty($timer_data['company_id']))
    	{
    		parent::check_permission('view_companies');
    		$company_id = $timer_data['company_id'];
    		$company = $this->getServiceLocator()->get('PM\Model\Companies');
    		$company_data = $company->getCompanyById($company_id);
    		if(!$company_data)
    		{
    			return $this->redirect()->toRoute('companies');
    		}
    	
    		$view['company'] = $company_data;
    	}
    	
    	if(!empty($timer_data['project_id']))
    	{
    		$project_id = $timer_data['project_id'];
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');
    		$project_data = $project->getProjectById($project_id);
    		$company_id = $project_data['company_id'];
    		if(!$project_data)
    		{
    			return $this->redirect()->toRoute('projects');
    		}
    	
    		if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('projects');
    		}
    	
    		$view['project'] = $project_data;
    	}
    	 
    	if(!empty($timer_data['task_id']))
    	{
    		$task_id = $timer_data['task_id'];
    		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');

    		$task_data = $task->getTaskById($task_id);
    		$project_id = $task_data['project_id'];
    		$project_data = $project->getProjectById($project_id);
    		$company_id = $project_data['company_id'];
    		if(!$task_data)
    		{
    			return $this->redirect()->toRoute('tasks');
    		}
    		 
    		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('pm');
    		}
    		$view['task'] = $task_data;
    	}   
    	
		$form = $this->getServiceLocator()->get('PM\Form\TimerForm');
       	if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			$form->setInputFilter($timer->getInputFilter());
    		$form->setData($formData->toArray());
    		if ($form->isValid($formData->toArray()))
    		{		
				$time = $this->getServiceLocator()->get('PM\Model\Times');
				$data = $timer->decodeTimerData($this->prefs['timer_data']);
				$timer_data = array_merge($formData->toArray(), $data);
				$timer_data['hours'] = $timer_data['time_running']['hours'];
				$timer_data['creator'] = $this->identity;
				$timer_data['user_id'] = $this->identity;
				$timer_data['company_id'] = $company_id;
				$timer_data['project_id'] = $project_id;
				$timer_data['task_id'] = $task_id;
				
				if($time->addTime($timer_data))
				{
					$timer->clearTimerData($this->identity);
					$this->flashMessenger()->addMessage($this->translate('timer_stopped', 'pm'));
					
					$date = strtotime($timer_data['date']);
					$options = array(
						'month' => date('n', $date),
						'year' => date('Y', $date),
						'day' => date('j', $date)
					);
					return $this->redirect()->toRoute('times/view-day', $options);
						   
				}
			} 
		}

    	$view['timer_data'] = $timer_data;
    	$view['form'] = $form; 
		$view['form_action'] = $this->getRequest()->getRequestUri(); 
    	return $this->ajaxOutput($view);
    }
}