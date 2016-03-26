<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/SettingsController.php
 */
 
namespace Sites\Controller;

/**
 * Sites - Sites Settings Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
class SettingsController extends AbstractSitesController
{
    public function indexAction()
    {

    
        $view = array();
        $view['settings'] = $this->site_data['settings'];
        $view['section'] = 'dashboard';
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}