<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/DashboardController.php
 */
 
namespace Sites\Controller;

/**
 * Sites - Sites Dashboard Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
class DashboardController extends AbstractSitesController
{
    public function indexAction()
    {
        $backups = $this->backup_data['backups'];
        $backup_meta = $this->backup_data['backup_meta'];
        $view['settings'] = $this->site_data['settings'];
        if(count($backups) > $view['settings']['dashboard_recent_total'])
        {
            //we have to remove a few
            $count = 1;
            $view_backups = array();
            foreach($backups AS $time => $backup)
            {
                $filtered_backups[$time] = $backup;
                if($count >= $view['settings']['dashboard_recent_total'])
                {
                    break;
                }
                $count++;
            }
            $view_backups = $filtered_backups;
        }    
        
        $view['backup_meta'] = $backup_meta;
        $view['backups'] = $view_backups;
        $view['site_data'] = $this->site_data;
        $view['section'] = 'dashboard';
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
    
    public function databaseAction()
    {
        $id = $this->params()->fromRoute('site_id');
        if (! $id ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $site_data = $site->getSiteById($id, $hash);
        if (! $site_data ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $view = array();
        $setting_data = $site->getApi()->getSettings($site_data);
        $backup_data = $site->getApi()->getBackups($site_data, 'database');
        
        $backups = $backup_data['backups'];
        $backup_meta = $backup_data['backup_meta'];
        $view['settings'] = $setting_data->getData();
        $view['backup_meta'] = $backup_meta;
        $view['backups'] = $backups;
        $view['site_data'] = $site_data;
        $view['active_sidebar'] = 'site_nav_'.$id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
    
    public function fileAction()
    {
        $id = $this->params()->fromRoute('site_id');
        if (! $id ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $site_data = $site->getSiteById($id, $hash);
        if (! $site_data ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $view = array();
        $setting_data = $site->getApi()->getSettings($site_data);
        $backup_data = $site->getApi()->getBackups($site_data, 'file');
        
        $backups = $backup_data['backups'];
        $backup_meta = $backup_data['backup_meta'];
        $view['settings'] = $setting_data->getData();
        $view['backup_meta'] = $backup_meta;
        $view['backups'] = $backups;
        $view['site_data'] = $site_data;
        $view['active_sidebar'] = 'site_nav_'.$id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}