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
        $backup_data = $this->site->getApi()->getBackups($this->site_data);
        $backups = $backup_data['backups'];
        $backup_meta = $backup_data['backup_meta'];
        if(count($backups) > $this->site_data['settings']['dashboard_recent_total'])
        {
            //we have to remove a few
            $count = 1;
            $view_backups = array();
            foreach($backups AS $time => $backup)
            {
                $filtered_backups[$time] = $backup;
                if($count >= $this->site_data['settings']['dashboard_recent_total'])
                {
                    break;
                }
                $count++;
            }
            $view_backups = $filtered_backups;
        }   
        else
        {
            $view_backups = $backups;
        }
        
        $view = array();
        $view['settings'] = $this->site_data['settings'];
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
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        
        $view = array();
        $backup_data = $this->site->getApi()->getBackups($this->site_data, 'database');
        
        $backups = $backup_data['backups'];
        $backup_meta = $backup_data['backup_meta'];
        $view['settings'] = $this->site_data['settings'];
        $view['form'] = $form;
        $view['backup_meta'] = $backup_meta;
        $view['backups'] = $backups;
        $view['site_data'] = $this->site_data;
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
    
    public function fileAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\ConfirmForm');
        $view = array();
        $backup_data = $this->site->getApi()->getBackups($this->site_data, 'file');
        
        $backups = $backup_data['backups'];
        $backup_meta = $backup_data['backup_meta'];
        $view['form'] = $form;
        $view['settings'] = $this->site_data['settings'];
        $view['backup_meta'] = $backup_meta;
        $view['backups'] = $backups;
        $view['site_data'] = $this->site_data;
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}