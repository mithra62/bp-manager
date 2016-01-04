<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Timers.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

/**
 * PM - Timers Model
 *
 * @package 	TimeTracker\Timers
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Timers.php
 */
class Timers
{
	/**
	 * The avaialble configuration options for a timer
	 * @var array
	 */
	private $options = array(
		'task_id' => 0, 
		'project_id' => 0, 
		'company_id' => 0, 
		'start_time' => FALSE
	);
	
	/**
	 * @ignore
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Application\Model\User\UserData $user_data)
	{
		$this->user_data = $user_data;
	}
	
	/**
	 * Wrapper to handle the task time functionality
	 * @param int $identity
	 * @param int $task_id
	 */
	public function startTaskTimer($identity, $task_id)
	{
		return $this->startTimer($identity, array('task_id' => $task_id));
	}
	
	/**
	 * Wrapper to handle the project time functionality
	 * @param int $identity
	 * @param int $project_id
	 */
	public function startProjectTimer($identity, $project_id)
	{
		return $this->startTimer($identity, array('project_id' => $project_id));
	}
	
	/**
	 * Wrapper to handle the company time functionality
	 * @param int $identity
	 * @param int $company_id
	 */
	public function startCompanyTimer($identity, $company_id)
	{
		return $this->startTimer($identity, array('company_id' => $company_id));
	}	
	
	/**
	 * Handles the actual starting of a timer
	 * @param array $options
	 */
	public function startTimer($identity, array $options)
	{
		$options['start_time'] = @mktime();
		return $this->user_data->updateUserDataEntry('timer_data', \Zend\Json\Json::encode($options), $identity);
	}
	
	/**
	 * Converts the timer string into an array
	 * @param string $str
	 * @return array
	 */
	public function decodeTimerData($str)
	{
		$data = \Zend\Json\Json::decode($str, 1);
		if($data && is_array($data))
		{
			$data['date'] = date('Y-m-d', $data['start_time']);
			$data['time_running'] = $this->makeTimeRunning($data['start_time']);
			return $data;
		}
	}
	
	public function getTimerData(array $where = array())
	{
		$user_data = $this->user_data->getUserData('timer_data', $where);
		return $this->decodeTimerData($user_data['option_value']);
	}
	
	/**
	 * Removes the entry for the timer
	 * @param int $identity
	 */
	public function clearTimerData($identity)
	{
		return $this->user_data->updateUserDataEntry('timer_data', '', $identity);
	}
	
	/**
	 * Returns an array for how long the timer has been running
	 * @param array $start_time
	 */
	public function makeTimeRunning($start_time)
	{
		$return = array();
		$diff = time()-$start_time;
		$return['minutes'] = round($diff/60, 2);
		$return['hours'] = round($return['minutes']/60, 2);
		return $return;
	}
	
	/**
	 * Returns the format needed for the view object's JavaScript display
	 * @param string $str
	 */
	public function makeCountdownDate($str)
	{
		return date('F d Y H:i:s', $str);
	}
	

	/**
	 * Returns the InputFilter
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'description',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}	
}