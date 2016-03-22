<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/AbstractSitesController.php
 */
namespace Sites\Controller;

use Application\Controller\AbstractController;

/**
 * Sites - Abstract Sites Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
abstract class AbstractSitesController extends AbstractController
{
    /**
     * Mark all requests as requiring login
     * @var bool
     */
    protected $admin_only = false;
    
    /**
     * The ID for the site we're manipulating
     * @var unknown
     */
    protected $site_id = false;
    
    /**
     * The data associated with the site
     * @var array
     */
    protected $site_data = array();
    
    /**
     * The backup data return from the site
     * @var array
     */
    protected $backup_data = array();
    
    /**
     * The actions that will require site_id processing
     * @var array
     */
    protected $bypass_id = array();
    
    /**
     * (non-PHPdoc)
     * @see \Application\Controller\AbstractController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->prepareSitesData();
        $this->layout()->setVariable('active_nav', 'sites');
        $sites = $this->getServiceLocator()->get('Sites\Model\Sites');
        $sites_data = $sites->getAllSites();     
        $this->layout()->setVariable('site_menu', $sites_data);
        return parent::onDispatch($e);
    }
    
    /**
     * Prepares the site data for use in the actions
     * @return void
     */
    protected function prepareSitesData()
    {
        if(in_array($this->params('action'), $this->bypass_id)) {
            return;
        }
        
        $this->site_id = $this->params()->fromRoute('site_id');
        if (! $this->site_id ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $site = $this->getServiceLocator()->get('Sites\Model\Sites');
        $hash = $this->getServiceLocator()->get('Application\Model\Hash');
        $this->site_data = $site->getSiteById($this->site_id, $hash);
        if (! $this->site_data ) {
            return $this->redirect()->toRoute('sites');
        }
        
        $this->site_data['settings'] = $site->getApi()->getSettings($this->site_data);
        if(!$this->site_data['settings']) {
            //we can't get data so we have to update keys most likely
            $this->flashMessenger()->addErrorMessage($this->translate('api_access_invlaid', 'sites'));
            return $this->redirect()->toRoute('sites/edit', array('site_id' => $this->site_id));
        }
        
        
        $this->backup_data = $site->getApi()->getBackups($this->site_data);
    }
}