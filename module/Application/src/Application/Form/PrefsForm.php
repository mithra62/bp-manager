<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/PrefsForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;
use Application\Model\Options\Timezones;
use Application\Model\Options\Languages;

/**
 * PrefsForm Form
 *
 * Generates the Preferences form
 *
 * @package Users\UserData
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/PrefsForm.php
 *            
 */
class PrefsForm extends BaseForm
{

    /**
     * Returns the Preferences form
     * 
     * @param string $options            
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        $this->add(array(
            'name' => 'timezone',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'timezone'
            ),
            'options' => array(
                'value_options' => Timezones::tz()
            )
        ));
        
        $this->add(array(
            'name' => 'locale',
            'type' => 'Select',
            'attributes' => array(
                'class' => 'select input',
                'id' => 'locale'
            ),
            'options' => array(
                'value_options' => Languages::langs()
            )
        ));
        
        $this->add(array(
            'name' => 'enable_rel_time',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'enable_rel_time'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'enable_contextual_help',
            'type' => 'Checkbox',
            'attributes' => array(
                'class' => '',
                'id' => 'enable_contextual_help'
            ),
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton'
            )
        ));
    }
}