<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/CompanyForm.php
*/

namespace PM\Form;

use PM\Model\Options\Companies;
use Application\Model\Options\Languages;
use Application\Model\Options\Currencies;
use Base\Form\BaseForm;

/**
 * Compnany Form
 *
 * Generates the Company form
 *
 * @package 	Companies
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Forms/CompanyForm.php
*/
class CompanyForm extends BaseForm
{
	/**
	 * Returns the Company form
	 * @param string $name
	 */	
	public function __construct($name) 
	{
		parent::__construct($name);

		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'name'
			),
		));
			
		$this->add(array(
			'name' => 'phone1',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone1'
			),
		));

		$this->add(array(
			'name' => 'phone2',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone2'
			),
		));

		$this->add(array(
			'name' => 'fax',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'fax'
			),
		));

		$this->add(array(
			'name' => 'address1',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'address1'
			),
		));

		$this->add(array(
			'name' => 'address2',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'address2'
			),
		));

		$this->add(array(
			'name' => 'city',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'city'
			),
		));
		
		$this->add(array(
			'name' => 'state',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'state'
			)
		));	

		$this->add(array(
			'name' => 'zip',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'zip'
			),
		));

		$this->add(array(
			'name' => 'primary_url',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'primary_url'
			),
		));

		$this->add(array(
			'name' => 'type',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'type'
			),
			'options' => array(
				'value_options' => Companies::types(),
			)
		));

		$this->add(array(
			'name' => 'currency_code',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'currency_code'
			),
			'options' => array(
				'value_options' => Currencies::codes(),
			)
		));

		$this->add(array(
			'name' => 'client_language',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'client_language'
			),
			'options' => array(
				'value_options' => Languages::langs(),
			)
		));

		$this->add(array(
			'name' => 'default_hourly_rate',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'default_hourly_rate'
			),
		));
		
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
				'name' => 'submit',
				'type' => 'Submit',
				'attributes' => array(
						'value' => 'Go',
						'id' => 'submitbutton',
				),
		));
				
	}
}