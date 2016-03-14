<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Base/src/Base/Traits/File.php
 */
namespace Base\Traits;

/**
 * Base - File System Trait
 *
 * Contains methods for dealing with files and the file system
 *
 * @package BackupProServer\Traits
 * @author Eric Lamb
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