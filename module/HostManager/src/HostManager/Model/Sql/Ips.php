<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Sql/Ips.php
 */
namespace HostManager\Model\Sql;

use HostManager\Model\Sql\SqlAbstract;

/**
 * HostManager - bookmarks table class
 *
 * @package HostManager\Sql
 * @author Eric Lamb
 * @filesource ./module/HostManager/src/HostManager/Model/Sql/Ips.php
 */
class Ips extends SqlAbstract
{

    /**
     * Appends the account_id column to all SELECT calls to filter database queries
     * 
     * @param \Zend\Db\Sql\Select $sql            
     * @return \Zend\Db\Sql\Select
     */
    public function Select(\Zend\Db\Sql\Select $sql, $account_id)
    {
        return parent::Select($sql, $account_id);
    }
}