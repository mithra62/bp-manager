<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Traits/File.php
 */
namespace PM\Traits;

use Base\Traits\File as BaseTrait;

/**
 * PM - File System Trait
 *
 * Contains methods for dealing with files and the file system in the context of projects and tasks
 *
 * @package MojiTrac\Traits
 * @author Eric Lamb
 * @filesource ./module/PM/src/PM/Traits/File.php
 */
trait File
{
    use BaseTrait;

    /**
     * Returns the path to the media storage directory making sure the path exists, and creating it if not, along the way.
     * 
     * @param string $start            
     * @param int $company_id            
     * @param int $project_id            
     * @param int $task_id            
     */
    public function checkMakeDirectory($start, $company_id, $project_id = false, $task_id = false)
    {
        $destination = $start . DS . $company_id;
        if ($project_id) {
            $destination = $destination . DS . $project_id;
        }
        
        if ($project_id && $task_id) {
            $destination = $destination . DS . $task_id;
        }
        
        return $this->chkmkdir($destination);
    }
}