<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/TimesController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Times Controller
 *
 * Routes the Times requests
 *
 * @package 	TimeTracker
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/TimesController.php
 */
class TimesController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        $this->layout()->setVariable('active_nav', 'time');
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		return $e;       
	}
    
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
    public function indexAction()
    {
    	$times = $this->getServiceLocator()->get('PM\Model\Times');
    	$month = $this->params()->fromRoute('month', date('m'));
    	$year = $this->params()->fromRoute('year', date('Y'));
    	
    	$view['month'] = $month;
    	$view['year'] = $year;

    	if($this->perm->check($this->identity, 'manage_time'))
    	{
    		$items = $times->getCalendarItems($month, $year);
    		$view['calendar_data'] = $items;
    	}
    	else
    	{
    		$view['calendar_data'] = $times->getCalendarItems($month, $year, $this->identity);
    	}
    	
    	return $view;
    }
    
    /**
     * Timer Tracker View Calendar Day View
     * @return unknown
     */
    public function viewDayAction()
    {
    	$times = $this->getServiceLocator()->get('PM\Model\Times');
    	$month = $this->params()->fromRoute('month', date('m'));
    	$year = $this->params()->fromRoute('year', date('Y'));
    	$day = $this->params()->fromRoute('day', date('d'));
		$view = $this->params()->fromRoute('view');
    	    		    	
		$form = $this->getServiceLocator()->get('PM\Form\TimeForm');

		$form->setData(array('date' => date('Y-m-d', mktime(0,0,0,$month, $day, $year)), 'billable' => 1));
		$request = $this->getRequest();
    	if ($request->isPost())
    	{
    		$formData = $this->getRequest()->getPost();
			$form->setInputFilter($times->getInputFilter());
    		$form->setData($formData->toArray());
    		if ($form->isValid($formData->toArray()))
    		{
    			$formData = $formData->toArray();
				$formData['creator'] = $this->identity;
				$formData['user_id'] = $this->identity;
				$time_id = $times->addTime($formData);
				if($time_id)
				{
					$this->flashMessenger()->addMessage($this->translate('time_added', 'pm'));
					return $this->redirect()->toRoute('times/view-day', array('month' => $month, 'year' => $year, 'day' => $day));
					
				} 
				else 
				{	
					$view['errors'] = array($this->translate('something_went_wrong', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
				}
				
			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
			}
		}    	
    	
		$view['month'] = $month;
		$view['year'] = $year;
		$view['day'] = $day;
		$view['active_sub'] = $view;
		$where = array('month' => $month, 'year' => $year, 'day' => $day);
		if($this->perm->check($this->identity, 'manage_time'))
    	{		
	    	$view['times'] = $times->getAllTimes($where); 
    	}
    	else
    	{
    		$where = array_merge($where, array('i.creator' => $this->identity));
    		$view['times'] = $times->getAllTimes($where); 
    	}

	    $view['form'] = $form;
	    return $view;    
    }

    /**
     * Remove Time Action
     */
    public function removeAction()
    {
    	$time = $this->getServiceLocator()->get('PM\Model\Times');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		
    	$id = $this->params()->fromRoute('time_id');
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('times');
    	}

    	if(!$this->perm->check($this->identity, 'manage_time'))
    	{
    		return $this->redirect()->toRoute('times');
    	}
    	
        $time_data = $time->getTimeById($id);
        $view = array();
    	$view['time_data'] = $time_data;
    	if(!$view['time_data'])
    	{
			return $this->redirect()->toRoute('times');
    	}
    	
    	$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('times/view-day', array('month' => $view['time_data']['month'], 'day' => $view['time_data']['day'], 'year' => $view['time_data']['year']));
				}

				$project = $this->getServiceLocator()->get('PM\Model\Projects');
    			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
	    		if($time->removeTime($id, $time_data, $project, $task))
	    		{	
					$this->flashMessenger()->addMessage($this->translate('time_removed', 'pm'));
					return $this->redirect()->toRoute('times/view-day', array('month' => $view['time_data']['month'], 'day' => $view['time_data']['day'], 'year' => $view['time_data']['year']));
	    		}
			}
    	}
    	
    	$view['form'] = $form;
    	$view['id'] = $id;
		return $this->ajaxOutput($view);
    }
    
    public function viewAction()
    {
    	$time = $this->getServiceLocator()->get('PM\Model\Times');
	    $id = $this->params()->fromRoute('id');
	    $type = $this->params()->fromRoute('type');
	    $bill_status = $this->params()->fromRoute('status');
	    $type = $this->params()->fromRoute('type');
	    $export = $this->params()->fromRoute('export');
	    $company_id = $project_id = $task_id = $user_id = false;
    	 
    	//we're downloading the timesheets so kill layout
    	if($export)
    	{
    		$this->view->layout()->disableLayout();
    	}
    	 
    	$view['sub_menu'] = 'time_status';
    	$view['active_sub'] = $bill_status;
		$this->layout()->setVariable('active_sub', $bill_status);
        $this->layout()->setVariable('sub_menu', 'time_status');
        $this->layout()->setVariable('id', $id);
        $this->layout()->setVariable('type', $type);
		
    	$where = array();
    	
    	if($bill_status)
    	{
    		if(!$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('times');
    		}
    			
    		$status_types = array('sent', 'unsent', 'paid', '');
    		if(in_array($bill_status, $status_types))
    		{
    			$view['bill_status'] = $bill_status;
    			if($bill_status == 'unsent')
    			{
    				$bill_status = '';
    			}
    
    			$where = array('bill_status' => $bill_status, 'billable' => '1');
    		}
    		elseif($bill_status == 'unbillable')
    		{
    			$where = array('billable' => '0');
    		}
    	}
    
    	if($type == 'company')
    	{
			$company_id = $id;
    		if(!$this->perm->check($this->identity, 'view_companies'))
    		{
    			return $this->redirect()->toRoute('times');
    		}
    		
    		$company = $this->getServiceLocator()->get('PM\Model\Companies');
    		$company_data = $company->getCompanyById($company_id);
    		if(!$company_data)
    		{
    			return $this->redirect()->toRoute('companies');
    		}
    		
    		$view['company_data'] = $company_data;
    		$view['times']  = $time->getTimesByCompanyId($company_id, $where);
    		$view['type'] = 'company';
    		$view['id'] = $company_id;
    	}
    	elseif($type == 'project')
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
    
    		$view['project_data'] = $project_data;
    		$view['times'] = $time->getTimesByProjectId($project_id, $where);
    		$view['type'] = 'project';
    		$view['id'] = $project_id;
    	}
    	elseif($type == 'task')
    	{
			$task_id = $id;
    		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');
    		$task_data = $task->getTaskById($task_id);
    		if(!$task_data)
    		{
    			return $this->redirect()->toRoute('projects');
    		}
    			
    		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('projects');
    		}
    
    		$view['task_data'] = $task_data;
    		$view['times'] = $time->getTimesByTaskId($task_id, $where);
    		$view['type'] = 'task';
    		$view['id'] = $task_id;
    	}
    	elseif($type == 'user')
    	{
			$user_id = $id;
    		$user = $this->getServiceLocator()->get('PM\Model\Users');
    		$user_data = $user->getUserById($user_id);
    		if(!$user_data)
    		{
    			return $this->redirect()->toRoute('users');
    		}
    
    		$view['user_data'] = $user_data;
    		$view['times'] = $time->getTimesByUserId($user_id, $where);
    		$view['type'] = 'user';
    		$view['id'] = $user_id;
    	}
    	else
    	{
    		return $this->redirect()->toRoute('pm');
    	}
    
    	if($export)
    	{
    		LambLib_Controller_Action_Helper_Utilities::downloadArray($this->view->times, TRUE, $this->view->bill_status.'_times.xls');
    	}
    	return $view;
    }    
}