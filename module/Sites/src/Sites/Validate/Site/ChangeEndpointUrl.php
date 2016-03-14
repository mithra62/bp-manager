<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Validate/Site/ChangeEndpointUrl.php
 */
namespace Sites\Validate\Site;

use Zend\Validator\AbstractValidator;

/**
 * Sites - Site Change Endpoint Validation Rule
 *
 * @package mithra62\BackupPro
 * @author Eric Lamb
 */
class ChangeEndpointUrl extends AbstractValidator
{
    const MATCH = 'connect';
    
    protected $messageTemplates = array(
        self::MATCH=> 'Can\'t connect with the API key'
    );
    
    public function isValid($value, $context = null)
    {
        $options = $this->getOptions();
        $site_id = $options['id'];
        $site = $options['site'];
        $site_data = $site->getSiteById($site_id);
        
        if (isset($site_data['api_endpoint_url']) && $site_data['api_endpoint_url'] == $value) {
            return true;
        }
        
        //make sure we're unique on the url
        if(!$site->getSiteByEndpointUrl($value)) {
            return true;
        }
        
        $this->error(self::MATCH);
        return false;
    }
}