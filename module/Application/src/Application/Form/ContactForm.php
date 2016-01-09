<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/ContactForm.php
*/

namespace PM\Form;

use Application\Model\Options\Us\States;
use PM\Model\Options\Companies;
use Base\Form\BaseForm;

/**
 * Company Contact Form
 *
 * Generates the Company Contact form
 *
 * @package 	Companies\Contacts
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Forms/ContactForm.php
*/
class ContactForm extends BaseForm
{
	/**
	 * Returns the Contact form
	 * @param string $name
	 * @param \PM\Model\Companies $companies
	 * @param \PM\Model\Options $options
	 */
	public function __construct($name, \PM\Model\Companies $companies, \PM\Model\Options $options) 
	{

		parent::__construct($name);

		$this->add(array(
			'name' => 'job_title',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'job_title'
			),
		));		

		$this->add(array(
			'name' => 'first_name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'first_name'
			),
		));
		
		$this->add(array(
			'name' => 'last_name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'last_name'
			),
		));
		
		$this->add(array(
			'name' => 'email',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'email'
			),
		));
		
		$this->add(array(
			'name' => 'email2',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'email2'
			),
		));
		
		$this->add(array(
			'name' => 'phone_home',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone_home'
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
			'name' => 'mobile',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'mobile'
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
			'name' => 'jabber',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'jabber'
			),
		));
		
		$this->add(array(
			'name' => 'icq',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'icq'
			),
		));
		
		$this->add(array(
			'name' => 'aol',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'aol'
			),
		));
		
		$this->add(array(
			'name' => 'msn',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'msn'
			),
		));
		
		$this->add(array(
			'name' => 'google_talk',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'google_talk'
			),
		));
		
		$this->add(array(
			'name' => 'yahoo',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'yahoo'
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
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'state'
			),
			'options' => array(
				'value_options' => States::states(TRUE),
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
			'name' => 'url',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'url'
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
			'name' => 'company_id',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => Companies::companies($companies, TRUE),
			)
		));	
		
			
	}
}