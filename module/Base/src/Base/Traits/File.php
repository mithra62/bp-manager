<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Traits/File.php
 */
namespace Base\Traits;

/**
 * Base - File System Trait
 *
 * Contains methods for dealing with files and the file system
 *
 * @package MojiTrac\Traits
 * @author Eric Lamb
 * @filesource ./module/Base/src/Base/Traits/File.php
 */
trait File
{

    /**
     * Returns the path to store files at on the filesystem
     * 
     * @return string
     */
    public function getStoragePath()
    {
        return realpath($_SERVER['DOCUMENT_ROOT'] . DS . '..' . DS . 'media' . DS);
    }

    /**
     * Takes $filename and returns the file extension
     * 
     * @param string $filename            
     * @return mixed
     */
    public function getFileExtension($filename)
    {
        $path_info = pathinfo($filename);
        return $path_info['extension'];
    }
}