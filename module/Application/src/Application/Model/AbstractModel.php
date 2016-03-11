<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/AbstractModel.php
 */
namespace Application\Model;

use Base\Model\BaseModel;

/**
 * Model Abstract
 *
 * Sets things up for abstracted functionality
 *
 * @package BackupProServer
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/AbstractModel.php
 */
abstract class AbstractModel extends BaseModel
{

    /**
     * Moji Abstract Model
     * 
     * @param \Zend\Db\Adapter\Adapter $adapter            
     * @param \Zend\Db\Sql\Sql $sql            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $sql)
    {
        parent::__construct($adapter, $sql);
    }
}