<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/BackToLink.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * PM - Back To Link View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/View/Helper/BackToLink.php
 */
class BackToLink extends AbstractHelper
{
	public function __invoke(array $options)
	{	
		if($this->view->ajax_mode)
		{
			return;
		}
		
		$this->options = $options;
		$return = '<div class="back_link_content">';
		$data = $this->_parseOptions();
		$return .= $this->template($data['route'], $data['options'], $data['name']);
		$return .= '<div>'.$this->view->InteractIcon('left-arrow', 'Back').'</div>';
		$return .= '</div><br clear="all" />';
		
		return $return;
		//return $return;
	}
	
	private function _parseOptions()
	{
		$return = array();
		if($this->options['task'])
		{
			$return['name'] = $this->options['task']['name'];
			$return['options'] = array('task_id' => $this->options['task']['id']);
			$return['route'] = 'tasks/view';
		}
		elseif($this->options['project'])
		{
			$return['name'] = $this->options['project']['name'];
			$return['options'] = array('project_id' => $this->options['project']['id']);	
			$return['route'] = 'projects/view';	
		}
		elseif($this->options['company'])
		{
			$return['name'] = $this->options['company']['name'];
			$return['options'] = array('company_id' => $this->options['company']['id']);
			$return['route'] = 'companies/view';
		}
		elseif($this->options['user'])
		{
			$return['name'] = $this->options['user']['first_name'].' '.$this->options['user']['last_name'];
			$return['options'] = array('user_id' => $this->options['user']['id']);
			$return['route'] = 'users/view';
		}	
		elseif($this->options['file'])
		{
			$return['name'] = $this->options['file']['name'];
			$return['options'] = array('file_id' => $this->options['file']['id']);
			$return['route'] = 'files/view';
		}	
		
		return $return;		
	}
    private function template($route, $options, $name)
    {
    	$return = '';
		$return .= '<a href="'.$this->view->url($route, $options).'" title="Back to '.$name.'">';
		$return .= 'Back to '.$name;
		$return .= '</a>';
		return $return;
    }	
}