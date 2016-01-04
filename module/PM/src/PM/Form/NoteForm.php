<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/NoteForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;
use PM\Model\Options\Notes;

/**
 * Note Form
 *
 * Generates the Note form
 *
 * @package 	Notes
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Forms/NoteForm.php
*/
class NoteForm extends BaseForm
{
	/**
	 * Returns the Note form
	 * @param string $options
	 */	
	public function __construct($name = null) 
	{

		parent::__construct($name);
		
		$this->add(array(
			'name' => 'subject',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'subject'
			),
		));	

		$this->add(array(
			'name' => 'topic',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'topic'
			),
			'options' => array(
				'value_options' => Notes::topics(),
			)
		));	

		$this->add(array(
			'type' => 'Zend\Form\Element\Textarea',
			'name' => 'description',
			'attributes' => array(
				'class' => 'styled_textarea',
				'rows' => '7',
				'cols' => '40',
				'id' => 'description'
			),
		));

		$this->add(array(
			'name' => 'hashed',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox', 
				'id' => 'hashed',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
				
	}
}