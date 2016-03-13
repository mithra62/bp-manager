<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Validate/Site/Connect.php
 */
namespace Sites\Validate\Site;

require_once 'D:\ProjectFiles\mithra62\product-dev\backup-pro-rest-client\vendor\autoload.php';

use Zend\Validator\AbstractValidator;
use \mithra62\BpApiClient\Client;
use \mithra62\BpApiClient\ApiProblem;
use \mithra62\BpApiClient\Hal;

/**
 * Sites - Site Connection Validation Rule
 *
 * @package mithra62\BackupPro
 * @author Eric Lamb
 */
class Connect extends AbstractValidator
{
    const CONNECT = 'connect';
    const UNKNOWN = 'unknown';
    
    protected $messageTemplates = array(
        self::CONNECT=> 'Can\'t connect with the API key',
        self::UNKNOWN=> 'An unknown issue happened... '
    );
    
    public function isValid($value, $context = null)
    {
        $client = new Client(); 
        if(empty($context['api_endpoint_url']) || empty($context['api_secret']) || empty($value)) {
            return true; //we need all fields
        }
        
        $result = $client->setApiKey($value)->setApiSecret($context['api_secret'])->setSiteUrl($context['api_endpoint_url'])->get('/backups');
        if($result instanceof ApiProblem) 
        {
            if($result->getStatus() == 403) {
                $this->error(self::CONNECT);
                return false;
            }

            $this->error(self::UNKNOWN);
            return false;
        }
        if($result instanceof Hal) 
        {
            return true;
        }
        
        return false;
    }
}