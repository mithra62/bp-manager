<?php
/**
 * mithra62 - Backup Pro Server
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
 * Application - Validates a user's new email address doesn't exist
 *
 * Validates a user's new email address doesn't exist
 *
 * @package Validate
 * @author Eric Lamb <eric@mithra62.com> <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Validate/User/PreventSelfEmail.php
 */
class ChangeEmail extends AbstractValidator
{

    const MATCH = 'match';

    protected $messageTemplates = array(
        self::MATCH => 'Email is already registered'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->setValue($value);
        
        $options = $this->getOptions();
        $identity = $options['identity'];
        $user = $options['users'];
        $user_data = $user->getUserById($identity);
        if (isset($user_data['email']) && $user_data['email'] == $value) {
            return true;
        }
        
        //make sure we're unique on the email
        if(!$user->getUserByemail($value)) {
            return true;
        }
        $this->error(self::MATCH);
        return false;
    }
}
