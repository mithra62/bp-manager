<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/SettingsForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;

/**
 * Settings Form
 *
 * Generates the Password form
 *
 * @package Settings
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/SettingsForm.php
 *            
 */
class SettingsForm extends BaseForm
{

    /**
     * Returns the System Settings form
     * 
     * @param string $options            
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }
}