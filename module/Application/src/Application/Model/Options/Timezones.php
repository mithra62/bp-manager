<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Options/Timezones.php
 */

namespace Application\Model\Options;

use DateTimeZone;

/**
 * Application - Timezones Options Model
 *
 * @package 	Localization\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Model/Options/Timezones.php
 */
class Timezones
{
	static public function tz()
	{
		$tz = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
		$return = array();
		
		foreach($tz AS $key => $value)
		{
		    $return[$value] = $value;
		}
		return $return;
	}
}