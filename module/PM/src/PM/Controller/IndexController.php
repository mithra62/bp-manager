<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/IndexController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;
/**
 * PM - Index Controller
 *
 * Routes the Home requests
 *
 * @package 	Dashboard
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/IndexController.php
 */
class IndexController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(\Zend\Mvc\MvcEvent $e)
	{
		$e = parent::onDispatch($e);
		$this->layout()->setVariable('active_nav', 'home');	
		return $e;
	}
		
    public function indexAction()
    {
        $user = $this->getServiceLocator()->get('PM\Model\Users');
		
        $view = array();
		$view['projects'] = $user->getAssignedProjects($this->identity);
		$task_data = $user->getAssignedTasks($this->identity, 30);
		if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
    		if(array_key_exists('task_completed', $formData) && is_array($formData['task_completed']))
    		{
    			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    			$looped = FALSE;
    			foreach($formData['task_completed'] AS $task_id => $value)
	    		{
		    		foreach($task_data AS $task_group)
	    			{
	    				foreach($task_group AS $t)
	    				{
	    					if($t['id'] == $task_id)
	    					{    						
	    						if($task->markCompleted($task_id, $this->identity))
	    						{
	    							$looped = TRUE;  							
	    						}
	    					}
	    				}
	    				
	    			}
	    		}
	    		if($looped)
	    		{
			    	$this->flashMessenger()->addMessage($this->translate('tasks_updated', 'pm'));
					return $this->redirect()->toRoute('home');	
					exit;	    			
	    		}
    		}
		}
		
		$view['user_data'] = $user->getUserById($this->identity);
		$view['tasks'] = $task_data;
		$view['identity'] = $this->identity;
		$this->layout()->setVariable('layout_style', 'left');
		
		return $view;
    }
    
    public function infoAction()
    {
    	phpinfo();
    	exit;
    }
}
