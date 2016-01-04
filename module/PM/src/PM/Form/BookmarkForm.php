<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Forms/BookmarkForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;

/**
* Bookmark Form
*
* Generates the Bookmark form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Forms/BookmarkForm.php
*/
class BookmarkForm extends BaseForm
{
	/**
	 * Returns the form for Bookmarks
	 * @param string $options
	 */
	public function __construct($name = 'bookmark_form') 
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