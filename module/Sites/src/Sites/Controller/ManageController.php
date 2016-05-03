<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/ManageController.php
 */
 
namespace Sites\Controller;

/**
 * Sites - Manage Backup Dashboard Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
class ManageController extends AbstractSitesController
{
    public function removeAction()
    {   
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->flashMessenger()->addErrorMessage($this->translate('backups_remove_failed', 'sites'));
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }

        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $form_data = $this->getRequest()->getPost();
        $form->setData($request->getPost());
        if (!$form->isValid($form_data) || !isset($form_data['backups']) || !$form_data['backups']) {
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }
        
        $backups = $this->validateBackups($form_data['backups'], $form_data['backup_type']);
        if (!$backups) {
            $this->flashMessenger()->addErrorMessage($this->translate('backups_remove_failed', 'sites'));
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }
        
        $view = array();
        $view['backups'] = $backups;
        $view['form'] = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $view['settings'] = $this->site_data['settings'];
        $view['backup_type'] = $form_data['backup_type'];
        $view['section'] = $form_data['backup_type'];
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
    
    public function removeBackupsAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }
        
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $form_data = $this->getRequest()->getPost();
        $form->setData($request->getPost());
        if (!$form->isValid($form_data) || !isset($form_data['backups']) || !$form_data['backups']) {
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }
        
        $backups = $this->validateBackups($form_data['backups'], $form_data['backup_type']);
        if (!$backups) {
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }    
        
        $remove = array();
        foreach($backups AS $backup) {
            $remove[] = $backup['file_name'];
        }
        
        if(!$this->site->getApi()->removeBackups($this->site_data, $remove, $form_data['backup_type'])) {
            $this->flashMessenger()->addErrorMessage($this->translate('backups_remove_failed', 'sites'));
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }
        
        $this->flashMessenger()->addSuccessMessage($this->translate('backups_removed', 'sites'));
        return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));        
        
    }
    
    public function backupNoteAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->redirect()->toRoute('dashboard/view', array('site_id' => $this->site_id));
        }
        
        $form_data = $this->getRequest()->getPost();
        $file_name =  $form_data['backup'];
        $backup_type = $form_data['backup_type'];
        $note_text = $form_data['note_text'];
        if($note_text && $file_name)
        {
            if( $this->site->getApi()->updateBackupNote($this->site_data, $note_text, $file_name, $form_data['backup_type']) ) {
                echo json_encode(array('success'));
            }
        }        
        exit;
    }
}