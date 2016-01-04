<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/CalendarController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Bookmarks Controller
 *
 * Routes the Calendar requests
 *
 * @package 	Calendar
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/CalendarController.php
 */
class CalendarController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );  
		return $e;       
	}
    
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
    public function indexAction()
    {
    	$cal = $this->getServiceLocator()->get('PM\Model\Calendar');
    	$month = $this->params()->fromRoute('month', date('m'));
    	$year = $this->params()->fromRoute('year', date('Y'));
    	
    	$view['month'] = $month;
    	$view['year'] = $year;
    	if($this->perm->check($this->identity, 'manage_projects'))
    	{
    		$view['calendar_data'] = $cal->getAllItems($month, $year);
    	}
    	else
    	{
    		$view['calendar_data'] = $cal->getUserItems($month, $year, $this->identity);
    	} 

    	return $view;
    }
    
    /**
     * View Day Action
     * @return Ambigous <unknown, \Base\Model\array:, multitype:, \Zend\EventManager\mixed, NULL, mixed>
     */
    public function viewDayAction()
    {
    	$month = $this->params()->fromRoute('month');
    	$year = $this->params()->fromRoute('year');
    	$day = $this->params()->fromRoute('day');
    	
    	$view['month'] = $month;
    	$view['day'] = $day;
    	$view['year'] = $year;
    	if($this->perm->check($this->identity, 'manage_projects'))
    	{
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');
    		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    		$view['project_data'] = $project->getProjectsByStartDate($year, $month, $day);
    		$view['task_data'] = $task->getTasksByStartDate($year, $month, $day);
    	}
    	else
    	{
    		$user = $this->getServiceLocator()->get('PM\Model\Users');
    		$view['project_data'] = $user->getAssignedProjects($this->identity, $year, $month, $day);
    		$view['task_data'] = $user->getAssignedTaskByDate($this->identity, $year, $month, $day);
    	}   

    	return $view;
    }
}