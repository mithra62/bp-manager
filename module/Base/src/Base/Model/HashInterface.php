<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Base/src/Base/Model/
 */
 
namespace Base\Model;

/**
 * Event Manager Interface Constants
 *
 * Contains all the Event Hook Names used within the Models
 *
 * @package BackupProServer\Model
 * @author Eric Lamb <eric@mithra62.com>
 */
interface HashInterface
{

    /**
     * Encrypts a string
     * @param string $string
     */
    public function encrypt($string);

    /**
     * Decrypts a string
     * @param strig $string
     */
    public function decrypt($string);
}