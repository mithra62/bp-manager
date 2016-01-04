<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/ReportsController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Reports Controller
*
* Routes the Reports requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/ReportsController.php
*/
class Pm_ReportsController extends PM_Abstract
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        $this->view->headTitle('Reports', 'PREPEND');
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'admin';
        $this->view->active_nav = 'admin';
		$this->view->active_sub = 'None';
		$this->view->title = FALSE; 
		$this->view->company = $this->_request->getParam('company', FALSE);
		$this->view->project = $this->_request->getParam('project', FALSE);
		$this->view->date = $this->_request->getParam('date', FALSE);
		$this->view->chart = $this->_request->getParam('chart', FALSE);		
	}
    
    public function indexAction()
    {

    }
    
    public function tasksAction()
    {
    	
    }
    
    public function projectsAction()
    {
    	
    }
    
    public function timesAction()
    {
    	
    }  

    public function ganttAction()
    {
    	
    }
}