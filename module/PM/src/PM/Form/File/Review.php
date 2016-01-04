<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
 * @author		Eric Lamb <eric@mithra62.com> <eric@mithra62.com>
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
 * @filesource 	./moji/application/modules/pm/forms/File/Review.php
*/

/**
* PM - File Review Form
*
* Returns the form for reviewing files
*
* @package 		mithra62:Mojitrac
 * @author		Eric Lamb <eric@mithra62.com> <eric@mithra62.com>
 * @filesource 	./moji/application/modules/pm/forms/File/Review.php
*/
class PM_Form_File_Review extends Form_Abstract
{
	/**
	 * Returns the form for reviewing files
	 * @param string $options
	 */
	public function __construct($options = null) 
	{

		parent::__construct($options);

		$review = new Zend_Form_Element_Textarea('review');
		$review->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->removeDecorator('label')
				->removeDecorator('htmlTag')
				->removeDecorator('description')
				->setAttrib('class', 'styled_textarea');				

		$submit = new Zend_Form_Element_Submit('submit');

		$this->addElements(
			array(
				 $review,
				 $revision,
				 $submit
			)
		);	
	}
}