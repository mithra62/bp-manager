<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/TimerForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;

/**
 * PM - Timee Form
 *
 * Returns the Timer form 
 *
 * @package 	TimeTracker\Timers
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Forms/TimerForm.php
*/
class TimerForm extends BaseForm
{
	/**
	 * Returns the Timer form
	 * @param string $name The name for the generated form
	 */
	public function __construct($name = null) 
	{
		parent::__construct($name);

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'description',
            'attributes' => array(
                'class' => 'styled_textarea', 
                'rows' => '7',
                'cols' => '40',
            ),
        ));
		
		$this->add(array(
			'name' => 'billable',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox',
				'id' => 'billable',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
	}
}