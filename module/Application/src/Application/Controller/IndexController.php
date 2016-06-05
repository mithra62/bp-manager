<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Controller/IndexController.php
 */
namespace Application\Controller;

use Application\Controller\AbstractController;

/**
 * Application - Index Controller
 *
 * Just placeholder and redirect
 *
 * @package BackupProServer
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Controller/IndexController.php
 */
class IndexController extends AbstractController
{

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        //$this->layout()->setVariable('active_nav', 'home');
        if(!$this->getIdentity()) {
            return $this->redirect()->toRoute('login');
        }
        return $this->redirect()->toRoute('sites');
    }

    public function aboutAction()
    {}
}
