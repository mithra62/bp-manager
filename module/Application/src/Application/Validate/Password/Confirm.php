<?php
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link			http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Validate/Password/Confirm.php
 */
namespace Application\Validate\Password;

use Zend\Validator\AbstractValidator;

/**
 * Application - Confirm Password Validator
 *
 * Ensures password confirmation values match
 *
 * @package mithra62:Mojitrac
 * @author Eric Lamb
 * @filesource ./module/Application/src/Application/Validate/Password/Confirm.php
 *            
 */
class Confirm extends AbstractValidator
{

    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);
        
        if (is_array($context) && array_key_exists('old_password', $context)) {
            if (isset($context['new_password']) && ($value == $context['new_password'])) {
                return true;
            }
        } elseif (is_array($context) && ! array_key_exists('old_password', $context) && array_key_exists('password', $context)) {
            if (isset($context['password']) && ($value == $context['password'])) {
                return true;
            }
        } elseif (is_array($context) && array_key_exists('new_password', $context) && ! array_key_exists('old_password', $context)) {
            if (isset($context['new_password']) && ($value == $context['new_password'])) {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }
        $this->_error(self::NOT_MATCH);
        return false;
    }
}
