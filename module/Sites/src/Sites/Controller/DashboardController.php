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
        $backup_data = $site->getApi()->getBackups($site_data);
        
        
        $view['settings'] = $setting_data->getData();
        if(count($backup_data->getResources()) > $view['settings']['dashboard_recent_total'])
        {
            //we have to remove a few
            $count = 1;
            $view_backups = array();
            foreach($backup_data->getResources() AS $time => $backup)
            {
                $filtered_backups[$time] = $backup;
                if($count >= $this->settings['dashboard_recent_total'])
                {
                    break;
                }
                $count++;
            }
            $view_backups = $filtered_backups;
        }        
        
        //$view['backups'] = $backups;
        $view['backup_meta'] = $backup_data->getData();
        $view['site_data'] = $site_data;
        $view['section'] = 'view_sites';
        $view['active_sidebar'] = 'site_nav_'.$id;
        return $view;
    }
}