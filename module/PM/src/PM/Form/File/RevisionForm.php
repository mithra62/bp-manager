<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/RevisionForm.php
*/

namespace PM\Form\File;

use PM\Form\FileForm;
use PM\Model\Options\Files;

/**
 * PM - File Revision Form
 *
 * Returns the form for revising files
 *
 * @package 	Files\Revisions
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Forms/RevisionForm.php
*/
class RevisionForm extends FileForm
{
	/**
	 * Returns the form for revising files
	 * @param string $options
	 */
	public function __construct($name = null, \PM\Model\Files $file) 
	{
		parent::__construct($name, $file);

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
}