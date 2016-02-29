<?php
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Sites/src/Sites/Model/Sites.php
 */
namespace Sites\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

/**
 * Sites - Sites Locker Model
 *
 * @package mithra62\BackupPro
 * @author Eric Lamb
 * @filesource ./module/Sites/src/Sites/Model/Sites.php
 */
class Sites extends AbstractModel
{

    protected $inputFilter;

    /**
     * The Sites Model
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Sql\Sql $db
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
    {
        parent::__construct($adapter, $db);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    /**
     * Returns all the system users
     *
     * @param string $status
     * @return array
     */
    public function getAllSites($status = FALSE)
    {
        $sql = $this->db->select()->from('sites');
    
        if ($status != '') {
            $sql = $sql->where(array(
                'user_status' => $status
            ));
        }
    
        return $this->getRows($sql);
    }
    
}