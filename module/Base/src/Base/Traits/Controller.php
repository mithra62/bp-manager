<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Base/src/Base/Traits/Controller.php
 */
namespace Base\Traits;

/**
 * Base - Controller Trait
 *
 * Contains the global goodies for the Base module and others
 *
 * @package BackupProServer\Traits
 * @author Eric Lamb
 */
trait Controller
{

    /**
     * The database adapter connection
     * 
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    protected $authservice;

    protected $storage;

    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        
        return $this->authservice;
    }

    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()->get('Application\Model\Auth\AuthStorage');
        }
        
        return $this->storage;
    }

    public function getAdapter()
    {
        if (! $this->adapter) {
            $sm = $this->getServiceLocator();
            $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return $this->adapter;
    }

    /**
     * Helper method for translating items in a Controller
     * 
     * @param string $lang            
     * @param string $domain            
     * @return string
     */
    public function translate($lang, $domain = 'app')
    {
        $translate = $this->getServiceLocator()
            ->get('viewhelpermanager')
            ->get('_');
        return $translate($lang, $domain);
    }

    public function downloadFile($file, $filename = null)
    {
        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($file, 'r'));
        $response->setStatusCode(200);
        
        $headers = new \Zend\Http\Headers();
        $headers->addHeaderLine('Content-Type', 'application/octet-stream')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->addHeaderLine('Content-Length', filesize($file));
        
        $response->setHeaders($headers);
        return $response;
    }
}