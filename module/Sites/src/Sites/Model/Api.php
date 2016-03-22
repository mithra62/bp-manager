<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Model/Api.php
 */
namespace Sites\Model;

use \mithra62\BpApiClient\Client;
use \mithra62\BpApiClient\ApiProblem;
use \mithra62\BpApiClient\Hal;

/**
 * Sites - Sites Locker Model
 *
 * @package mithra62\BackupPro
 * @author Eric Lamb
 */
class Api
{
    /**
     * The API Client object
     * @var \mithra62\BpApiClient\Client
     */
    protected $client = null;
    
    /**
     * Returns the basic details about a Backup Pro installation 
     * @param string $key
     * @param string $secret
     * @param string $url
     * @return array
     */
    public function getSiteDetails($key, $secret, $url)
    {
        $config = array(
            'api_key' => $key,
            'api_secret' => $secret,
            'site_url' => $url
        );
        $client = $this->getClient($config);
        $site_details = $client->get('/info/site');
        
        if($site_details instanceof Hal) 
        {
            return $site_details->getData();
        }
        
        return array();
    }
    
    /**
     * Returns a site's Backup Pro settings
     * @param array $site_details
     * @return multitype:
     */
    public function getSettings(array $site_details)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $client = $this->getClient($config);
        $settings = $client->get('/settings');
        if($settings instanceof Hal)
        {
            return $settings->getData();
        }
        
        return array();
    }
    
    /**
     * Returns the backups data in a usable format array
     * @param array $site_details
     * @param string $type
     * @return array
     */
    public function getBackups(array $site_details, $type = 'all')
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $route = '/backups';
        $payload = array();
        switch($type)
        {
            case 'database':
                $payload = array('type' => 'db');
            break;
            
            case 'file':
                $payload = array('type' => 'file');
            break;
        }
        
        $client = $this->getClient($config);
        $backups = $client->get($route, $payload);
        
        if($backups instanceof Hal)
        {
            return $this->normalizeBackups($backups, $type);
        }
        
        return array();
    }
    
    /**
     * Executes a backup against $site_details
     * @param array $site_details
     * @param string $type
     * @return bool
     */
    public function execBackup(array $site_details, $type)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $route = '/backups';
        $client = $this->getClient($config);
        return $client->post($route);        
    }
    
    /**
     * Returns an instance of the client object
     * @param array $config
     * @param string $force
     * @return \mithra62\BpApiClient\Client
     */
    public function getClient(array $config = array(), $force = false)
    {
        if(is_null($this->client) || $force)
        {
            $this->client = new Client($config);
        }
        
        return $this->client;
    }
    
    /**
     * Takes the backup data and returns it into a standard format
     * @param Hal $backups
     * @return array
     */
    protected function normalizeBackups(Hal $backups)
    {
        $resources = $backups->getResources();
        if(is_array($resources['backups']))
        {
            $return_backups = array();
            foreach($resources['backups'] As $key => $value)
            {
                $storage_resource = $value->getResources();
                $return_backups[$key] = $value->getData();
                $return_backups[$key]['storage_locations'] = $this->normalizeStorage($storage_resource['storage']);
            }
        }
        
        $return = array(
            'backup_meta' => $backups->getData(),
            'backups' => $return_backups
        );
        
        return $return;
    }
    
    /**
     * Takes the storage details and returns an array
     * @param array $storage
     * @return array
     */
    protected function normalizeStorage(array $storage = array())
    {
        $return = array();
        foreach($storage AS $key => $value)
        {
            $return[] = $value->getData();
        }
        return $return;
    }
}