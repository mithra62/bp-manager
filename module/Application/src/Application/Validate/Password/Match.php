<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com> <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Validate/Password/Match.php
 */
namespace Application\Validate\Password;

use Zend\Validator\AbstractValidator;

/**
 * Application - Match Password Validator
 *
 * Ensures a given password matches the encryped password value
 *
 * @package Validate
 * @author Eric Lamb <eric@mithra62.com> <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Validate/Password/Match.php
 */
class Match extends AbstractValidator
{

    const NOT_MATCH = 'notMatch';

    protected $messageTemplates = array(
        self::NOT_MATCH => 'Current password does not match'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->setValue($value);
        
        $options = $this->getOptions();
        if (strlen($value) >= 2) {
            $identity = $options['identity'];
            $users = $options['users'];
            if ($users->verifyCredentials($identity, $value, 'id')) {
                return TRUE;
            }
        }
        
        $this->error(self::NOT_MATCH);
        return false;
    }
}
