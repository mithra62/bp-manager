<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Options/Languages.php
 */
namespace Application\Model\Options;

use DateTimeZone;

/**
 * Application - Languages Options Model
 *
 * @package Localization\Options
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/Options/Languages.php
 */
class Languages
{

    static public function langs()
    {
        // $return = array('en_US' => 'English / US', 'es_ES' => 'Spanish');
        $return = array(
            'en_US' => 'English / US'
        );
        return $return;
    }
}