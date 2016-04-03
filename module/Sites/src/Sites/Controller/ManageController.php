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
        
        $view = array();
        $view['backups'] = $backups;
        $view['form'] = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $view['settings'] = $this->site_data['settings'];
        $view['section'] = $form_data['backup_type'];
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}