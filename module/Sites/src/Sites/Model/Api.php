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
        $client = $this->getClient($config, true);
        $site_details = $client->get('/info/site');
        if($site_details instanceof Hal) 
        {
            return $site_details->getData();
        }
        
        return array();
    }
    
    /**
     * Updates a Site's Backup Pro Settings
     * @param array $site_details
     * @param array $form_data
     * @return bool
     */
    public function updateSettings(array $site_details, array $form_data)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        $client = $this->getClient($config);
        $site_details = $client->put('/settings', $form_data);
        if($site_details instanceof Hal)
        {
            return $site_details->getData();
        }
        
        return array();
    }
    
    /**
     * Validates the settings data without updating anything
     * @param array $site_details
     * @param array $form_data
     * @return multitype:
     */
    public function validateSettings(array $site_details, array $form_data)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        $client = $this->getClient($config);
        $site_details = $client->post('/validate', $form_data);
        if($site_details instanceof Hal)
        {
            return $site_details->getData();
        }
        
        return array();
    }

    /**
     * Returns the option arrays for settings form
     * @param array $site_details
     * @return multitype:
     */
    public function getOptions(array $site_details)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $client = $this->getClient($config);
        $settings = $client->get('/info/options');
        if($settings instanceof Hal)
        {
            return $settings->getData();
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
     * Remotes a set of backups
     * @param array $site_details
     * @param array $remove
     * @param string $type
     */
    public function removeBackups(array $site_details, array $remove, $type = 'database')
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $payload = array('id' => $remove, 'type' => $type);
        $client = $this->getClient($config);
        $backups = $client->delete('/backups', $payload);
        
        if($backups instanceof Hal)
        {
            return $this->normalizeBackups($backups, $type);
        }
        
        return array();
    }
    
    /**
     * Updates a backup note
     * @param array $site_details
     * @param string $note_text
     * @param string $file_name
     * @param string $type
     */
    public function updateBackupNote(array $site_details, $note_text, $file_name, $type = 'database')
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $payload = array('id' => $file_name, 'type' => $type, 'backup_note' => $note_text);
        $client = $this->getClient($config);
        $backups = $client->put('/backups', $payload);
        
        if($backups instanceof Hal)
        {
            return $this->normalizeBackups($backups, $type);
        }
        
        return array();
    }
    
    /**
     * Returns the created storage location data
     * @param array $site_details
     * @return multitype:
     */
    public function getStorageLocations(array $site_details)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $client = $this->getClient($config);
        $storage = $client->get('/storage');
        if($storage instanceof Hal)
        {
            $storage_locations = $storage->getResources();
            $return = array();
            foreach($storage_locations['storage'] AS $location) {
                $return[] = $location->getData();
                
            }
            
            return $return;
        }
        
        return array();
    }
    
    /**
     * Returns a single Storage Loccation
     * @param array $site_details
     * @param string $storage_id
     * @return multitype:
     */
    public function getStorageLocation(array $site_details, $storage_id)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $client = $this->getClient($config);
        $storage = $client->get('/storage/'.$storage_id);
        if($storage instanceof Hal)
        {
            return $storage->getData();
        }
        
        return array();
    }
    
    public function deleteStorageLocation(array $site_details, $storage_id)
    {
        $config = array(
            'api_key' => $site_details['api_key'],
            'api_secret' => $site_details['api_secret'],
            'site_url' => $site_details['api_endpoint_url'],
        );
        
        $client = $this->getClient($config);
        $storage = $client->delete('/storage/'.$storage_id);
        
        if($storage instanceof Hal)
        {
            return $storage->getData();
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
        $payload = array('type' => $type);
        
        $client = $this->getClient($config);
        return $client->post($route, $payload);        
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
                $return_backups[$key]['storage_locations'] = (isset($storage_resource['storage']) ? $this->normalizeStorage($storage_resource['storage']) : array());
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