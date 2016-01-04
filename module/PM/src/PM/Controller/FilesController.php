<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/FilesController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Files Controller
 *
 * Routes the Files requests
 *
 * @package 	Files
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/FilesController.php
 */
class FilesController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        parent::check_permission('view_files');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('active_nav', 'projects');
        $this->layout()->setVariable('sub_menu', 'files');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');
		return $e;
	}
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
	public function indexAction()
	{

		$id = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');
		if( !$type )
		{
			return $this->redirect()->toRoute('pm');
		}
		
		$view = array();
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		if($type == 'company')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
				return $this->redirect()->toRoute('pm');
			}
						
			$company_id = $id;
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				return $this->redirect()->toRoute('companies');
			}
				
			$view['company_data'] = $company_data;
			$view['file_data'] = $file->getFilesByCompanyId($company_id);
			$this->layout()->setVariable('sub_menu', 'projects');
        	$this->layout()->setVariable('active_nav', 'projects');
        	$this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
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
			
			$view['project_data'] = $project_data;
			if(!$project->isUserOnProjectTeam($this->identity, $project_id))
			{
	        	return $this->redirect()->toRoute('pm');			
			}
			
			$view['file_data'] = $file->getFilesByProjectId($project_id);
			$this->layout()->setVariable('sub_menu', 'projects');
        	$this->layout()->setVariable('active_nav', 'projects');
        	$this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
		}
		
		if($type == 'task')
		{
			$task_id = $id;
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				return $this->residrect()->toRoute('pm');
			}
			
			if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']))
			{
	        	return $this->residrect()->toRoute('pm');				
			}
				
			$view['file_data'] = $file->getFilesByTaskId($task_id);
			$view['task_data'] = $task_data;
		}

		$view['id'] = $id;
		$view['type'] = $type;
		return $view;
	}
	
	/**
	 * File View Action
	 * @return \Zend\Http\Response
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('file_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$file_data = $file->getFileById($id);
		if(!$file_data)
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if($file_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $file_data['project_id']) && !$this->perm->check($this->identity, 'manage_files'))
			{
	        	return $this->redirect()->toRoute('projects/view', array('project_id' => $file_data['project_id']));				
			}			
		}
		
		if($file_data['company_id'] && $file_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('projects/view', array('project_id' => $file_data['project_id']));				
			}			
		}		
		
		$file_revisions = $file->revision->getFileRevisions($id);
		$file_reviews = array();//$file->getFileReviews($id);

		$view['file'] = $file_data;
		$view['revision_history'] = $file_revisions;
		$view['file_reviews'] = $file_reviews;
		$view['id'] = $id;
		return $view;
	}
	
	/**
	 * File Edit Page
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('file_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$file_data = $file->getFileById($id);
		if(!$file_data)
		{
			return $this->redirect()->toRoute('pm');
		}

		$form = $this->getServiceLocator()->get('PM\Form\FileForm');
        $form->setData($file_data);
		$request = $this->getRequest();
		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($file->getInputFilter());
			$form->setData($formData);
			if ($form->isValid()) 
			{	
				$formData = $formData->toArray();
				$formData['creator'] = $this->identity;
			    if($file->updateFile($formData, $id))
	            {
					$this->flashMessenger()->addMessage();
					return $this->redirect()->toRoute('files/view', array('file_id' => $id));  	        		
            	} 
            	else 
            	{
            		$view['errors'] = array($this->translate('cant_update_file', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
            	}
			}
		}
		
		$view = array();
		$view['id'] = $id;
		$view['file_data'] = $file_data;
		$view['form'] = $form;
		
		$this->layout()->setVariable('layout_style', 'left');
		$view['form_action'] = $this->getRequest()->getRequestUri();
		return $this->ajaxOutput($view);
	}
	
	/**
	 * File Add Page
	 * @return void
	 */
	public function addAction()
	{
		$id = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');
		$view = array();
		$view['file_errors'] = false;
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		
		if($type == 'company') 
		{
			$company_id = $id;
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				return $this->redirect()->toRoute('companies');
			}
			
			$view['company_data'] = $company_data;
		}

		if($type == 'project') 
		{
		    $project_id = $id;
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
			    return $this->redirect()->toRoute('projects');
			}
			$view['project_data'] = $project_data;
		}

		if($type == 'task') 
		{
		    $task_id = $id;
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				return $this->residrect()->toRoute('tasks');
			}
			
			$view['task_data'] = $task_data;
		}	
				
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$form = $this->getServiceLocator()->get('PM\Form\FileForm');
		$request = $this->getRequest();
		if ($request->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($file->getInputFilter(true));
			$formData = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);	
			$form->setData($formData);
			if ($form->isValid()) 
			{
				$formData = $form->getData();
				$adapter = $file->getFileTransferAdapter($formData['file_upload']['name']);			
				if ($adapter->isValid())
				{
					if ($adapter->receive($formData['file_upload']['name']))
					{
						if(isset($company_data))
						{
							$formData['company_id']	= $company_data['id'];
						}
						
						if(isset($project_data))
						{
							$formData['company_id'] = $project_data['company_id'];
							$formData['project_id'] = $project_data['id'];
						}
						
						if(isset($task_data))
						{
							$project = $this->getServiceLocator()->get('PM\Model\Projects');
							$formData['project_id'] = $task_data['project_id'];
							$formData['task_id'] = $task_data['id'];
							$temp = $project->getCompanyIdById($task_data['project_id']);
							$formData['company_id'] = $temp['company_id'];
						}

						$file_info = $adapter->getFileInfo('file_upload');
						$formData['creator'] = $this->identity;	
						$formData['owner'] = $this->identity;
						$formData['uploaded_by'] = $this->identity;					
						$file_id = $file->addFile($formData, $file_info['file_upload'], $project, $task);
						if($file_id)
						{
							$this->flashMessenger()->addMessage($this->translate('file_added', 'pm'));
							return $this->redirect()->toRoute('files/view', array('file_id' => $file_id));
						}
						else
						{
							$view['file_errors'] = array($this->translate('cant_upload_file', 'pm'));
							$this->layout()->setVariable('errors', $view['errors']);
						}
					}		
				} 
				else 
				{
					$view['file_errors'] = $adapter->getMessages();
					$this->layout()->setVariable('errors', $view['file_errors']);
				}
			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
			}
		}

		$this->layout()->setVariable('layout_style', 'left');
		$form->addFileField();
		$view['form_action'] = $this->getRequest()->getRequestUri();
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Removes a file
	 */
	public function removeAction()
	{   
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');

		$id = $this->params()->fromRoute('file_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}
    	
    	$file_data = $file->getFileById($id);
    	if(!$file_data)
    	{
			return $this->redirect()->toRoute('pm');
    	}

    	$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid())
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('files/view', array('file_id' => $id));
				}
				
	    	   	if($file->removeFile($id))
	    		{	
					$formData['task'] = $file_data['task_id'];
					$formData['company'] = $file_data['company_id'];
					$formData['project'] = $file_data['project_id'];
					
					$this->flashMessenger()->addMessage($this->translate('file_removed', 'pm'));
					if($file_data['task_id'] > 0)
					{
						return $this->redirect()->toRoute('tasks/view', array('task_id' => $file_data['task_id']));
					}
					
	    			if($file_data['project_id'] > 0)
					{
						return $this->redirect()->toRoute('projects/view', array('project_id' => $file_data['project_id']));
					}
	
	    			if($file_data['company_id'] > 0)
					{
						return $this->redirect()->toRoute('companies/view', array('company_id' => $file_data['company_id']));
					}
					
					return $this->redirect()->toRoute('pm');
	    		} 
	    		else
	    		{
	    			$view['errors'] = array($this->translate('cant_remove_file', 'pm'));
	    		}
    		}
    	
		}

		$view['file_data'] = $file_data;
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);		
	}
}