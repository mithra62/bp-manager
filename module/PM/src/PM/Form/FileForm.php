<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/FileForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;
use PM\Model\Options\Files;

/**
 * PM - File Form
 *
 * Returns the form for the File system
 *
 * @package 	Files
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Forms/FileForm.php
*/
class FileForm extends BaseForm
{
	/**
	 * Returns the File form
	 * @param string $options
	 */	
	public function __construct($name = null, \PM\Model\Files $file) 
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
			'name' => 'status',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => Files::status(),
			)
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
	}
	
	public function addFileField()
	{
		$this->add(array(
			'name' => 'file_upload',
			'type' => 'File',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'file_upload'
			),
		));	
	}
}