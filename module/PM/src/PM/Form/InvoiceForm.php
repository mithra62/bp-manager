<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/InvoiceForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;
use PM\Model\Options\Invoices;

/**
 * Invoice Form
 *
 * Generates the invoice form
 *
 * @package 	Companies\Invoices
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Forms/InvoiceForm.php
*/
class InvoiceForm extends BaseForm
{
	/**
	 * Returns the Invoice form
	 * @param string $options
	 */	
	public function __construct($name) 
	{

		parent::__construct($name);

		$this->add(array(
			'name' => 'invoice_number',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'invoice_number'
			),
		));	

		$this->add(array(
			'name' => 'status',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => Invoices::status(),
			)
		));	

		$this->add(array(
			'name' => 'date',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'date'
			),
		));
		
		$this->add(array(
			'name' => 'po_number',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'po_number'
			),
		));
		
		$this->add(array(
			'name' => 'discount',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'discount'
			),
		));
		
		$this->add(array(
			'name' => 'currency_code',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'currency_code'
			),
		));
		
		$this->add(array(
			'name' => 'language',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'language'
			),
		));
		
		$this->add(array(
			'name' => 'override_total',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'override_total'
			),
		));
		
		$this->add(array(
			'name' => 'terms_conditions',
			'type' => 'Textarea',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'terms_conditions'
			),
		));
		
		$this->add(array(
			'name' => 'notes',
			'type' => 'Textarea',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'notes'
			),
		));
			
	}
}