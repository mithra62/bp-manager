<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
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
     * (non-PHPdoc)
     * @see \Application\Controller\AbstractController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->layout()->setVariable('active_nav', 'sites');
        
        $sites = $this->getServiceLocator()->get('Sites\Model\Sites');
        $sites_data = $sites->getAllSites();     
        $this->layout()->setVariable('site_menu', $sites_data);
        return parent::onDispatch($e);
    }
}