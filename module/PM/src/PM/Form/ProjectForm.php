<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/ProjectForm.php
 */

namespace PM\Form;

use PM\Model\Options\Projects;
use PM\Model\Options\Companies;
use Base\Form\BaseForm;

/**
 * PM - Project Form
 *
 * Generates the Project form
 *
 * @package 	Projects 
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Forms/ProjectForm.php
*/
class ProjectForm extends BaseForm
{
	/**
	 * Returns the ProjectForm form
	 * @param string $options
	 */	
	public function __construct($name, \PM\Model\Companies $companies, \PM\Model\Options $options) 
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
			'name' => 'priority',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input', 
			),
			'options' => array(
				'value_options' => Projects::priorities(),
			)
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

		$this->add(array(
			'name' => 'status',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => Projects::status(),
			)
		));

		$this->add(array(
			'name' => 'type',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => Projects::types($options),
			)
		));		

		$this->add(array(
			'name' => 'start_date',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small',
			)
		));

		$this->add(array(
			'name' => 'end_date',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small',
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