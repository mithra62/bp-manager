<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Application/src/Application/Forms/Confirm.php
*/

namespace Application\Form;

use Base\Form\BaseForm;

/**
* Confirm Form
*
* Generates a simple confirmation form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/Application/src/Application/Forms/Confirm.php
*/
class ConfirmForm extends BaseForm
{
	/**
	 * Returns a simple confirmation form
	 * @param string $options
	 */	
	public function __construct($name) 
	{	
		parent::__construct($name);
		$this->add(array(
			'name' => 'confirm',
			'type' => 'Submit',
			'attributes' => array(
				'class' => 'btn btn-primary',
				'id' => 'confirm',
				'value' => 'Yes'
			),
		));
		
		$this->add(array(
			'name' => 'fail',
			'type' => 'Submit',
			'attributes' => array(
				'class' => 'btn btn-primary',
				'id' => 'fail',
				'value' => 'No'
			),
		));
	}
}