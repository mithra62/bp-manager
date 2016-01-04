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

namespace PM\Controller\Files;

use PM\Controller\AbstractPmController;

/**
 * PM - File Revisions Controller
 *
 * Routes the File Revisions requests
 *
 * @package 	Files\Revisions
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/FilesController.php
 */
class FileRevisionsController extends AbstractPmController
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
	 * Forces a download of a given file revision
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|\Zend\Http\Response\Stream
	 */
	public function downloadAction()
	{
		$id = $this->params()->fromRoute('revision_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$rev_data = $file->revision->getRevision($id);
		
		if (!$rev_data) {
			return $this->redirect()->toRoute('pm');
		}

		$file_data = $file->getFileById($rev_data['file_id']);
		if (!$file_data) {
			return $this->redirect()->toRoute('pm');
		}
		
		if($file_data['project_id'] != 0)
		{
			//check if the user is on the project's team.
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $file_data['project_id']) && !$this->perm->check($this->identity, 'manage_files'))
			{
				return $this->redirect()->toRoute('pm');
			}		
		}
		
		$download_path = $file->checkMakeDirectory($file->getStoragePath(), $file_data['company_id'], $file_data['project_id'], $file_data['task_id']);
		$download_path  = $download_path.DS.$rev_data['stored_name'];
		if(file_exists($download_path) && is_readable($download_path))
		{
			return $this->downloadFile($download_path, $rev_data['file_name']);
		}
		
		$this->flashMessenger()->addErrorMessage($this->translate('file_not_found', 'pm'));
		return $this->redirect()->toRoute('files/view', array('file_id' => $rev_data['file_id']));  	  
	}
	
	public function previewAction()
	{
		$id = $this->params()->fromRoute('revision_id');
		$view_type = $this->params()->fromRoute('view-type', false);
		$view_size = $this->params()->fromRoute('view-size', false);
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}
		
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$rev_data = $file->revision->getRevision($id);
		if (!$rev_data) 
		{
			return $this->redirect()->toRoute('pm');
		}

		$file_data = $file->getFileById($rev_data['file_id']);
		if (!$file_data) 
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if($file_data['project_id'] != 0)
		{
			//check if the user is on the project's team.
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $file_data['project_id']) && !$this->perm->check($this->identity, 'manage_files'))
			{
				return $this->redirect()->toRoute('pm');
			}		
		}
		
		$view['preview_exists'] = true;
		$root_path = $file->checkMakeDirectory($file->getStoragePath(), $file_data['company_id'], $file_data['project_id'], $file_data['task_id']);
		if(file_exists($root_path.DS.$rev_data['stored_name']))
		{
			//check if we're dealing with an image
			$image_check = getimagesize($root_path.DS.$rev_data['stored_name']);
			if($image_check)
			{
				$image = $this->getServiceLocator()->get('Application\Model\Image');
				$view_size = $image->getPreviewSize($view_size);
				$download_path  = $root_path.DS.$view_size.$rev_data['stored_name'];
				if(!file_exists($download_path))
				{
					if($image_check && is_array($image_check))
					{
						if( !$image->processImage($rev_data['stored_name'], $root_path, $image_check) )
						{
							$view['preview_exists'] = false;
						}

						//we have to do special magic for PSD files
						if($view['preview_exists'] && 'image/psd' == $image_check['mime'])
						{
							$download_path = $root_path.DS.$view_size.str_replace('.psd', '.'.str_replace('.','', 'jpg'), $rev_data['stored_name']);
							if( !file_exists($download_path) )
							{
								$view['preview_exists'] = false;
							}
						}
					}
					else
					{
						$view['preview_exists'] = false;
					}
				}
			}
			else
			{
				$view['preview_exists'] = false;
			}
		}
		else
		{
			$view['preview_exists'] = false;
		}
		
		if($view_type == 'html')
		{
			header('Content-type: '.$rev_data['mime_type']);
			header("Content-Length: " . filesize($download_path));		
			
			$fp = fopen($download_path, 'rb');
			fpassthru($fp);
			exit;
		}
		
		$view['rev_data'] = $rev_data;
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Add a file revision action
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <\Zend\View\Model\ViewModel, boolean, array>
	 */
	public function addAction()
	{
		$file_id = $this->params()->fromRoute('file_id');
		if (!$file_id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$file_data = $file->getFileById($file_id);
		if(!$file_data)
		{
			return $this->redirect()->toRoute('pm');
		}
    	
		$form = $this->getServiceLocator()->get('PM\Form\File\RevisionForm');		
		$request = $this->getRequest();
		if ($request->isPost()) 
		 {
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($file->revision->getInputFilter(true));
			$formData = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);	
			
			$form->setData($formData);
			if ($form->isValid($formData)) 
			{
				$formData = $form->getData();
				$adapter = $file->getFileTransferAdapter($formData['file_upload']['name']);	
				if ($adapter->isValid())
				{
					if ($adapter->receive($formData['file_upload']['name']))
					{
						$file_info = $adapter->getFileInfo('file_upload');
						
						$formData['creator'] = $this->identity;	
						$formData['owner'] = $this->identity;
						$formData['uploaded_by'] = $this->identity;
						$formData['upload_file_data'] = $file_info['file_upload'];
						$formData['file_data'] = $file_data;
						
						$revision_id = $file->addRevision($file_id, $formData, true);
						if($revision_id)
						{
							$this->flashMessenger()->addMessage($this->translate('file_revision_added', 'pm'));
							return $this->redirect()->toRoute('files/view', array('file_id' => $file_id));
						}
						else
						{
							$view['file_errors'] = array('Couldn\'t upload file :(');
						}
					
					}
								
				} 
				else 
				{
					$view['file_errors'] = $adapter->getMessages();
				}
				
			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
			}

		}

		$this->layout()->setVariable('layout_style', 'left');
		$form->addFileField();
		$view['form_action'] = $this->getRequest()->getRequestUri();
		$view['file_data'] = $file_data;
		$view['form'] = $form;
		$view['file_data'] = $file_data;
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Remove a file revision action
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <\Zend\View\Model\ViewModel, boolean, array>
	 */
	public function removeAction()
	{
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('revision_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}
    	
    	$rev_data = $file->revision->getRevision($id);
    	if(!$rev_data)
    	{
			return $this->redirect()->toRoute('pm');
    	}
    	
    	$file_data = $file->getFileById($rev_data['file_id']);
	    if(!$file_data)
    	{
			return $this->redirect()->toRoute('pm');  		
    	}

    	$total_revisions = $file->revision->getTotalFileRevisions($rev_data['file_id']);
    	$view['total_file_revisions'] = $total_revisions;
    	
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
					return $this->redirect()->toRoute('files/view', array('file_id' => $rev_data['file_id']));
				}
				
	    	   	if($file->removeRevision($id))
	    		{	
					$this->flashMessenger()->addMessage($this->translate('file_revision_removed', 'pm'));
					return $this->redirect()->toRoute('files/view', array('file_id' => $rev_data['file_id']));
					
	    		} 
	    		
			}
    	}
    	
    	$view['form'] = $form;
    	$view['revision_data'] = $rev_data;
    	$view['file_data'] = $file_data;
		$view['id'] = $id;   
		return $this->ajaxOutput($view);
	}
}