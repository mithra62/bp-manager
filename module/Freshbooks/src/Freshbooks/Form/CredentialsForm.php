<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/Freshbooks/Form/CredentialsForm.php
*/

namespace Freshbooks\Form;

use Base\Form\BaseForm;

/**
 * Credentials Form
 *
 * Generates the Freshbooks Credentials Form for recording Auth connection details
 *
 * @package 	Freshbooks\Credentials
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Freshbooks/src/Freshbooks/Form/CredentialsForm.php
*/
class CredentialsForm extends BaseForm
{
	/**
	 * Generates the CredentialsForm form
	 * @param string $options
	 */
	public function __construct($name = null)
	{

		// we want to ignore the name passed
		parent::__construct($name);
		$this->setAttribute('method', 'post');
		$this->add(array(
				'name' => 'freshbooks_account_url',
				'type' => 'Text',
				'attributes' => array(
					'class' => 'input large',
					'id' => 'freshbooks_account_url',
				),
		));
		
		$this->add(array(
				'name' => 'freshbooks_auth_token',
				'type' => 'Password',
				'attributes' => array(
					'class' => 'input large',
					'id' => 'freshbooks_auth_token',
				),
		));
	
		$this->add(array(
				'name' => 'submit',
				'type' => 'Submit',
				'attributes' => array(
					'value' => 'Go',
					'id' => 'submitbutton',
				),
		));
    }
}