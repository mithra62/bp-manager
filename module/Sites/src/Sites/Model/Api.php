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
            return $settings;
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
        
        $client = $this->getClient($config);
        $backups = $client->get('/backups');        
        if($backups instanceof Hal)
        {
            return $this->normalizeBackups($backups, $type);
        }
        
        return array();
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
                $return_backups[$key]['storage'] = $this->normalizeStorage($storage_resource['storage']);
            }
        }
        
        $return = array(
            'backup_meta' => $backups->getData(),
            'backups' => $return_backups
        );
        
        return $return;
    }
    
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