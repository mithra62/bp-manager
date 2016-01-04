<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Form/TaskForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;
use PM\Model\Options\Tasks;
use Application\Model\Options\Datetime;
use PM\Model\Options\Project\Team;

/**
* Note Form
*
* Generates the Note form
*
* @package 		Tasks
* @author		Eric Lamb <eric@mithra62.com>
* @filesource 	./module/PM/src/PM/Form/TaskForm.php
*/
class TaskForm extends BaseForm
{
	/**
	 * Returns the Task form 
	 * @param int $project_id
	 * @param string $options
	 * @param string $hidden
	 */
	public function __construct($name, \PM\Model\Options $option, \PM\Model\Projects $project) 
	{
		parent::__construct($name);
		$this->option = $option;
		$this->project = $project;
	}
	
	/**
	 * Sets the default form values
	 * @param int $project_id
	 */
	public function setup($project_id)
	{		
	    $this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'name'
			),
		));	
		
		$this->add(array(
			'name' => 'duration',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small',
				'id' => 'duration'
			),
		));	
		
		$this->add(array(
			'name' => 'start_date',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small',
				'id' => 'start_date',
			    'autocomplete' => 'off'
			),
		));	
		
		$this->add(array(
			'name' => 'end_date',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small',
				'id' => 'end_date',
			    'autocomplete' => 'off'
			),
		));	

		$this->add(array(
			'name' => 'start_hour',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'start_hour'
			),
			'options' => array(
				'value_options' => Datetime::hours(),
			)
		));	

		$this->add(array(
			'name' => 'start_minute',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'start_minute'
			),
			'options' => array(
				'value_options' => Datetime::minutes(),
			)
		));	

		$this->add(array(
			'name' => 'end_hour',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'end_hour'
			),
			'options' => array(
				'value_options' => Datetime::hours(),
			)
		));	

		$this->add(array(
			'name' => 'end_minute',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'end_minute'
			),
			'options' => array(
				'value_options' => Datetime::minutes(),
			)
		));	

		$this->add(array(
			'name' => 'status',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'status'
			),
			'options' => array(
				'value_options' => Tasks::status(),
			)
		));	

		$this->add(array(
			'name' => 'progress',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'progress'
			),
			'options' => array(
				'value_options' => Tasks::progress(),
			)
		));	

		$this->add(array(
			'name' => 'type',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'type'
			),
			'options' => array(
				'value_options' => Tasks::types($this->option),
			)
		));	

		$this->add(array(
			'name' => 'priority',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'priority'
			),
			'options' => array(
				'value_options' => Tasks::priorities(),
			)
		));	

		$this->add(array(
			'name' => 'assigned_to',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			    'id' => 'assigned_to'
			),
			'options' => array(
				'value_options' => Team::team($this->project, $project_id, TRUE),
			)
		));	

		$this->add(array(
			'type' => 'Zend\Form\Element\Textarea',
			'name' => 'description',
			'attributes' => array(
				'class' => 'styled_textarea',
				'rows' => '7',
				'cols' => '40',
			    'id' => 'description'
			),
		));

		$this->add(array(
			'type' => 'Zend\Form\Element\Textarea',
			'name' => 'assign_comment',
			'attributes' => array(
				'class' => 'styled_textarea',
				'rows' => '7',
				'cols' => '40',
			    'id' => 'assign_comment'
			),
		));
				
	    
	}
}