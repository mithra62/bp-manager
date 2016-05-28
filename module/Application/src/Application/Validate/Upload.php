<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Validate/Upload.php
 */
namespace Application\Validate;

use Zend\Validator\AbstractValidator;

/**
 * Application - Ensure a file was really uploaded
 *
 * Ensures a given password matches the encryped password value
 *
 * @package Validate
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Validate/Upload.php
 */
class Upload extends AbstractValidator
{

    const NOT_UPLOADED = 'notUploaded';

    protected $messageTemplates = array(
        self::NOT_UPLOADED => 'A file is required'
    );

    public function isValid($value, $context = null)
    {
        // $this->error(self::NOT_UPLOADED);
        return true;
        if ($value['error'] == '4') {
            $this->error(self::NOT_UPLOADED);
            return false;
        }
        echo 'fdsafdsa';
        print_r($value);
        exit();
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
