<?php
/**
 * mithra62 - MojiTrac
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Model/
 */
namespace Base\Model;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use DateTime;

/**
 * Base - Model
 *
 * General Moji Model Methods. The Base Model should be the parent of any Models within the system
 * if database access and/or Event interactivity is required.
 * <br /><strong>Note that if a key => value style interface is needed, use the KeyValue Model instead (it extends BaseModel).</strong>
 *
 * @package MojiTrac\Model
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Base/src/Base/Model/BaseModel.php
 */
abstract class BaseModel implements EventManagerInterfaceConstants
{

    /**
     * The database object
     * 
     * @var object
     */
    protected $db;

    /**
     * The Cache Object
     * 
     * @var object
     */
    public $cache;

    /**
     * The stored cache name
     * 
     * @var string
     */
    public $cache_key = null;

    /**
     * The Event Manager Object
     * 
     * @var \Zend\EventManager\EventManager
     */
    public $events = null;

    /**
     * The Database Adapter
     * 
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter = null;

    /**
     * The sort order for SQL queries
     * 
     * @var string
     */
    protected $sortOrder = null;

    /**
     * The direction sorts will use
     * 
     * @var string
     */
    protected $sortOrderDir = 'ASC';

    /**
     * The number or rows to return from the db
     * 
     * @var int
     */
    protected $limit = null;

    /**
     * The WHERE clause array for SQL
     * 
     * @var array
     */
    protected $where = null;

    /**
     * Where the SQL queries should start
     * 
     * @var int
     */
    protected $offset = null;

    /**
     * Moji Abstract Model
     * 
     * @param \Zend\Db\Adapter\Adapter $adapter            
     * @param \Zend\Db\Sql\Sql $sql            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter = null, \Zend\Db\Sql\Sql $sql = null)
    {
        if ($adapter && $sql) {
            $this->adapter = $adapter;
            $this->db = $sql;
        }
    }

    /**
     * Returns an instance of the DB object
     * 
     * @return object
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Returns single row from $sql
     * 
     * @param \Zend\Db\Sql\Select $sql            
     * @return array:
     */
    public function getRow(\Zend\Db\Sql\Select $sql)
    {
        $sql = $this->prepSql($sql);
        $ext = $this->trigger(self::EventDbSelectPre, $this, compact('sql'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $sql = $ext->last();
        
        $selectString = $this->db->getSqlStringForSqlObject($sql);
        $result = $this->query($selectString, 'execute')->toArray();
        
        $ext = $this->trigger(self::EventDbSelectPost, $this, compact('result'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $result = $ext->last();
        
        if (! empty($result['0'])) {
            return $result['0'];
        } else {
            return array();
        }
    }

    /**
     * Returns multiple rows from $sql
     * 
     * @param \Zend\Db\Sql\Select $sql            
     * @return array:
     */
    public function getRows(\Zend\Db\Sql\Select $sql)
    {
        $sql = $this->prepSql($sql);
        $ext = $this->trigger(self::EventDbSelectPre, $this, compact('sql'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $sql = $ext->last();
        
        $this->total_results = 0;
        $this->total_pages = 0;
        $selectString = $this->db->getSqlStringForSqlObject($sql);
        $result = $this->query($selectString, 'execute')->toArray();
        $this->total_results = $this->getTotalResults();
        $this->total_pages = (!is_null($this->getLimit()) ? ceil($this->total_results / $this->getLimit()) : false );
        $ext = $this->trigger(self::EventDbSelectPost, $this, compact('result'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $result = $ext->last();
        
        if (! empty($result)) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * Updates $table with $what based on $where
     * 
     * @param string $table            
     * @param array $what            
     * @param array $where            
     */
    public function update($table, array $what = null, array $where = null)
    {
        $sql = $this->db->update($table)
            ->set($what)
            ->where($where);
        
        $ext = $this->trigger(self::EventDbUpdatePre, $this, compact('sql'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $sql = $ext->last();
        
        $updateString = $this->db->getSqlStringForSqlObject($sql);
        $result = ($this->query($updateString, 'execute'));
        
        $ext = $this->trigger(self::EventDbUpdatePost, $this, compact('sql', 'result'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $result = $ext->last();
        
        return $result->getAffectedRows();
    }

    /**
     * Creates a new entry in $table
     * 
     * @param string $table            
     * @param array $data            
     * @return Ambigous <\Zend\Db\Adapter\Driver\mixed, NULL>
     */
    public function insert($table, array $data)
    {
        $sql = $this->db->insert($table)->values($data);
        
        $ext = $this->trigger(self::EventDbInsertPre, $this, compact('sql'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $sql = $ext->last();
        
        $insertString = $this->db->getSqlStringForSqlObject($sql);
        $result = ($this->query($insertString, 'execute'));
        
        $ext = $this->trigger(self::EventDbInsertPost, $this, compact('sql', 'result'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $result = $ext->last();
        
        return $result->getGeneratedValue();
    }

    /**
     * Removes rows from $table based on $where
     * 
     * @param string $table            
     * @param array $where            
     * @return number
     */
    public function remove($table, array $where)
    {
        $sql = $this->db->delete($table)->where($where);
        
        $ext = $this->trigger(self::EventDbRemovePre, $this, compact('sql'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $sql = $ext->last();
        
        $removeString = $this->db->getSqlStringForSqlObject($sql);
        $result = ($this->query($removeString, 'execute'));
        
        $ext = $this->trigger(self::EventDbRemovePost, $this, compact('sql', 'result'), array());
        if ($ext->stopped())
            return $ext->last();
        elseif ($ext->last())
            $result = $ext->last();
        
        return $result->getAffectedRows();
    }

    public function setWhere(array $where = null)
    {
        $this->where = $where;
        return $this;
    }

    /**
     * Sets the SQL sort order
     * 
     * @param string $order            
     * @return \Base\Model\BaseModel
     */
    public function setOrder($order = null)
    {
        if ($order) {
            $this->sortOrder = $order;
        }
        
        return $this;
    }

    /**
     * Sets the direction of the db SELECT sort order
     * 
     * @param string $dir            
     * @return \Base\Model\BaseModel
     */
    public function setOrderDir($dir = null)
    {
        if ($dir) {
            $this->sortOrderDir = $dir;
        }
        
        return $this;
    }

    /**
     * Sets the amount of rows to return on SELECT db calls
     * 
     * @param string $limit            
     * @return \Base\Model\BaseModel
     */
    public function setLimit($limit = null)
    {
        if ($limit) {
            $this->limit = $limit;
        }
        
        return $this;
    }

    /**
     * Returns the limit the SQL is using
     * 
     * @return number
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the offset based on $page number
     *
     * Note that $this->limit needs to be set before
     * 
     * @param string $page            
     * @return \Base\Model\BaseModel
     */
    public function setPage($page = null)
    {
        if ($page && $this->limit) {
            $this->offset = ($page - 1) * $this->limit;
            $this->page = $page;
        }
        
        return $this;
    }

    /**
     * Returns the page number a pagination set is on
     * 
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Returns the number of rows a given SQL query would return without pagination
     * 
     * @return string
     */
    public function getTotalResults()
    {
        $result = $this->query("SELECT FOUND_ROWS() AS total_rows", 'execute')->toArray();
        $total = 0;
        if (! empty($result['0']['total_rows'])) {
            $total = $result['0']['total_rows'];
        }
        return $total;
    }

    /**
     * Applies all the chained options for the SQL object
     * 
     * @param \Zend\Db\Sql\Select $sql            
     * @return \Zend\Db\Sql\Select
     */
    private function prepSql(\Zend\Db\Sql\Select $sql)
    {
        if ($this->sortOrder) {
            $sql->order($this->sortOrder . ' ' . $this->sortOrderDir);
        }
        
        if ($this->limit) {
            $sql->limit($this->limit);
            $sql = $sql->quantifier(new \Zend\Db\Sql\Expression('SQL_CALC_FOUND_ROWS'));
        }
        
        if ($this->offset) {
            $sql->offset($this->offset);
        }
        
        if ($this->where) {
            $sql->where($this->where);
        }
        
        return $sql;
    }

    /**
     * Executes a SQL string
     * 
     * @param string $sql            
     * @param string $type            
     * @return Ambigous <\Zend\Db\Adapter\Driver\StatementInterface, \Zend\Db\ResultSet\Zend\Db\ResultSet, \Zend\Db\Adapter\Driver\ResultInterface, \Zend\Db\ResultSet\Zend\Db\ResultSetInterface>
     */
    public function query($sql, $type = 'execute')
    {
        return ($this->adapter->query($sql, $type));
    }

    /**
     * Returns the databse adapter or lazy loads it if it doesn't exist
     */
    public function getAdapter()
    {
        if (! $this->adapter) {
            $sm = $this->getServiceLocator();
            $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
        }
        return $this->adapter;
    }

    /**
     * Creates an instance of the Event Manager
     * 
     * @param EventManagerInterface $events            
     * @return \Application\Model\AbstractModel
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
            'Moji'
        ));
        $this->events = $events;
        return $this;
    }

    /**
     * Returns an instance of the Event Manager (or creates one if it doesn't exist yet)
     * 
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Wrapper for trigger Event Manager hooks
     * 
     * @param mixed $names            
     * @param object $obj            
     * @param array $argv            
     * @param array $xhooks            
     * @return object
     */
    public function trigger($names, $obj, array $argv, array $xhooks = array())
    {
        if (! is_array($names)) {
            $names = array(
                $names
            );
        }
        
        // setup the "special" context sensitive hooks
        foreach ($names as $name) {
            foreach ($xhooks as $key => $value)
                foreach ($value as $context => $pk)
                    $names[] = $name . '[' . $context . '.' . $pk . ']';
        }
        
        $names = (array_reverse($names));
        
        $argv = $this->getEventManager()->prepareArgs($argv);
        foreach ($names as $event) {
            $ext = $this->getEventManager()->trigger($event, $obj, $argv);
            if ($ext->stopped()) {
                return $ext;
            }
        }
        
        return $ext;
    }

    /**
     * Sets up the contextual hooks based on $data
     * 
     * @param array $data            
     * @return array
     */
    public function setXhooks(array $data = array())
    {
        $return = array();
        if (! empty($data['company']))
            $return[] = array(
                'company' => $data['company']
            );
        
        if (! empty($data['project']))
            $return[] = array(
                'project' => $data['project']
            );
        
        if (! empty($data['priority']))
            $return[] = array(
                'priority' => $data['priority']
            );
        
        if (! empty($data['type']))
            $return[] = array(
                'type' => $data['type']
            );
        
        if (! empty($data['status']))
            $return[] = array(
                'status' => $data['status']
            );
        
        return $return;
    }

    /**
     * Resolves $path to determine which module path should be created
     * 
     * @param string $path            
     * @return string
     */
    public function getModulePath($path)
    {
        return realpath($path . '/../../../');
    }

    /**
     * Creates all directories; works with arrays
     *
     * @param mixed $path
     *            array
     * @return mixed on failure or string on success
     */
    public function chkmkdir($path)
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, TRUE);
        }
        return $path;
    }

    /**
     * Sets the Timezone for the system
     * 
     * @param string $timezone            
     */
    public function setTimezone($timezone)
    {
        date_default_timezone_set($timezone);
        $dt = new DateTime();
        $offset = $dt->format('P');
        $this->query("SET time_zone='$offset'");
    }
}