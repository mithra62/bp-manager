<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com> <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Validate/User/PreventSelfEmail.php
 */

namespace Application\Validate\User;

use Zend\Validator\AbstractValidator;

/**
 * Application - Ensure a user doesn't enter their email address
 *
 * Ensure a user doesn't enter their email address
 *
 * @package 	Validate
 * @author		Eric Lamb <eric@mithra62.com> <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Validate/User/PreventSelfEmail.php
 */
class PreventSelfEmail extends AbstractValidator
{
    const MATCH = 'match';
 
    protected $messageTemplates = array(
        self::MATCH => 'You can\'t use your email address'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->setValue($value);

        $options = $this->getOptions();
        $identity = $options['identity'];
	    $user = $options['user'];
	    $user_data = $user->getUserById($identity);
	    if(isset($user_data['email']) && $user_data['email'] != $value)
	    {
			return TRUE;
		}
        
        $this->error(self::MATCH);
        return false;
    }
}
