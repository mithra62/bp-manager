<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/FormatDate.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Format Date View Helper
 *
 * @package 	ViewHelpers\DateTime
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/View/Helper/FormatDate.php
 */
class FormatDate extends BaseViewHelper
{   	
	/**
	 * Returns the human readable date if under week old
	 * @param string $date
	 * @return string
	 */
	function __invoke($date, $include_time = FALSE)
	{	
		if ( '0000-00-00 00:00:00' == $date || '0000-00-00' == $date || null == $date) 
		{
			return 'N/A';
		} 
		else 
		{	
			$str_date = strtotime($date);
			$settings = $prefs = $this->getUserData();
			if($settings['date_format'] == 'custom')
			{
				$settings['date_format'] = $settings['date_format_custom'];
			}
			
			if($settings['time_format'] == 'custom')
			{
				$settings['time_format'] = $settings['time_format_custom'];
			}

			if($settings['time_format'] == '')
			{
				$settings['time_format'] = 'g:i A';
			}
			
			if($settings['date_format'] == '')
			{
				$settings['date_format'] = 'F j, Y';
			}

			if(!$include_time)
			{
				$settings['time_format'] = '';
			}
			return $this->c($str_date).$this->util->formatDate($date, $settings['date_format'].' '.$settings['time_format']);
		}
	}
	
	private function c($str)
	{
		return '<!--'.$str.'-->';
	}
}