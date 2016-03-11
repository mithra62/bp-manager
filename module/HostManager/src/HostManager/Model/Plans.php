<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Plans.php
 */
namespace HostManager\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

/**
 * PM - Ip Locker Model
 *
 * @package HostManager\Plans
 * @author Eric Lamb
 * @filesource ./module/HostManager/src/HostManager/Model/Plans.php
 */
class Plans extends AbstractModel
{

    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'ip',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => '\Zend\Validator\Hostname',
                        'options' => array(
                            'allow' => \Zend\Validator\Hostname::ALLOW_IP
                        )
                    )
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    /**
     * Creates the SQL array to update or create an IP entry
     * 
     * @param array $data            
     * @return multitype:\PM\Model\Zend_Db_Expr number unknown
     */
    public function getSQL(array $data)
    {
        return array(
            'ip' => ip2long($data['ip']),
            'ip_raw' => $data['ip'],
            'description' => $data['description'],
            'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
        );
    }

    /**
     * Returns all the Ip Addresses
     */
    public function getAllIps()
    {
        $sql = $this->db->select()->from('ips');
        return $this->getRows($sql);
    }

    /**
     * Returns the IP for the pk
     * 
     * @param int $id            
     */
    public function getIpById($id)
    {
        $sql = $this->db->select()->from(array(
            'ip' => 'ips'
        ));
        $sql = $sql->join(array(
            'u' => 'users'
        ), 'u.id = ip.creator', array(
            'first_name',
            'last_name'
        ), 'left');
        $sql = $sql->where(array(
            'ip.id' => $id
        ));
        return $this->getRow($sql);
    }

    /**
     * Checks if the provided Ip Address is allowed in the system
     * 
     * @param int $ip            
     */
    public function isAllowed($ip)
    {
        $sql = $this->db->select()
            ->from(array(
            'ip' => 'ips'
        ))
            ->columns(array(
            'id'
        ))
            ->where(array(
            'ip' => ip2long($ip)
        ));
        $data = $this->getRow($sql);
        return $data;
    }

    /**
     * Adds an Ip Address to the white list
     * 
     * @param array $data            
     * @param int $creator            
     */
    public function addIp(array $data, $creator)
    {
        if ($this->isAllowed($data['ip'])) {
            return TRUE;
        }
        
        $sql = $this->getSQL($data);
        $sql['creator'] = $creator;
        $sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
        if ($this->insert('ips', $sql)) {
            return TRUE;
        }
    }

    /**
     * Removes an Ip Address from the white list
     * 
     * @param string $key            
     * @param stirng $col            
     */
    public function removeIp($id)
    {
        if ($this->remove('ips', array(
            'id' => $id
        ))) {
            return TRUE;
        }
    }

    /**
     * Updates an Ip Address on the white list
     * 
     * @param array $data            
     * @param int $id            
     */
    public function updateIp(array $data, $id)
    {
        $sql = $this->getSQL($data);
        if ($this->update('ips', $sql, array(
            'id' => $id
        ))) {
            return TRUE;
        }
    }
}