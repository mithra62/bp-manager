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
        $section = $this->params()->fromRoute('section');
        $form = $this->getServiceLocator()->get('Sites\Form\SettingsForm');
        
        $view = array();
        $view['form'] = $form;
        $view['settings'] = $this->site_data['settings'];
        $view['section'] = $section;
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}