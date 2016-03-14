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
    protected $client = null;
    
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
    
    public function getClient(array $config = array(), $force = false)
    {
        if(is_null($this->client) || $force)
        {
            $this->client = new Client($config);
        }
        
        return $this->client;
    }
}