<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/Freshbooks/Validate/Upload.php
 */

namespace Freshbooks\Validate;

use Zend\Validator\AbstractValidator;
use Freshbooks\FreshBooksApi;

/**
 * Freshbooks - Validate Freshbooks Connection Details
 *
 * Ensures a Freshbook credential set is valid and works
 *
 * @package 	Validate
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Freshbooks/src/Freshbooks/Validate/Upload.php
 */
class Credentials extends AbstractValidator
{
	/**
	 * The message to return on bad credentials
	 * @var string
	 */
    const INVALID_CREDENTIALS = 'invalidCredentials';
 
    /**
     * The mapping of error message constants to plain english
     * @var unknown
     */
    protected $messageTemplates = array(
        self::INVALID_CREDENTIALS => 'Invalid credentials; please try again.'
    );

    /**
     * (non-PHPdoc)
     * @see \Zend\Validator\ValidatorInterface::isValid()
     */
    public function isValid($value, $context = null)
    {
    	FreshBooksApi::init($context['freshbooks_account_url'], $context['freshbooks_auth_token']);
    	$fb = new FreshBooksApi('staff.list');
    	$fb->request();
    	if(!$fb->success())
    	{
    		$this->error(self::INVALID_CREDENTIALS);
    		return false;
    	}	
    	
    	return true;
    }
}
