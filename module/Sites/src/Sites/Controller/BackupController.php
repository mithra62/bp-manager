<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/BackupController.php
 */
 
namespace Sites\Controller;

/**
 * Sites - Sites Backup Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
class BackupController extends AbstractSitesController
{
    public function indexAction()
    {
        $id = $this->params()->fromRoute('site_id');
        if (! $id ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $type = $this->params()->fromRoute('type', 'database');
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $site_data = $site->getSiteById($id, $hash);
        if (! $site_data ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                if ($site->execBackup($site_data, $type)) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('user_removed', 'app'));
                    return $this->redirect()->toRoute('manage_users');
                }
            }
        }        
        
        $view = array();
        $view['form'] = $form;
        $view['backup_type'] = $type;
        $view['site_data'] = $site_data;
        $view['section'] = 'dashboard';
        $view['active_sidebar'] = 'site_nav_'.$id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}