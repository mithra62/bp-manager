<?php
 /**
 * mithra62 - Backup Pro Server
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Ips.php
 */

namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

 /**
 * PM - Ip Locker Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Ips.php
 */
class Ips extends AbstractModel
{
    protected $inputFilter;
    
    /**
     * The IP Locker Model
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
    
    public function getInputFilter()
    {
    	if (!$this->inputFilter) {
    		$inputFilter = new InputFilter();
    		$factory = new InputFactory();
    
    		$inputFilter->add($factory->createInput(array(
				'name'     => 'ip',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => '\Zend\Validator\Hostname',
						'options' => array(
							'allow' => \Zend\Validator\Hostname::ALLOW_IP
						)
					),
				),
    		)));
    
    		$this->inputFilter = $inputFilter;
    	}
    	
    	return $this->inputFilter;
    }
    
    /**
     * Creates the SQL array to update or create an IP entry
     * @param array $data
     * @return multitype:\PM\Model\Zend_Db_Expr number unknown
     */
	public function getSQL(array $data){
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
	 * Returns an array of IP Addresses
	 * @param array $where
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getIps(array $where = array())
	{
		$sql = $this->db->select()->from(array('ip'=>'ips'));
		$sql = $sql->join(array('u' => 'users'), 'u.id = ip.creator', array('first_name', 'last_name'), 'left');
		if($where)
		{
			$sql = $sql->where($where);
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns a single IP Address and its details
	 * @param array $where
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getIp(array $where = array())
	{
		$sql = $this->db->select()->from(array('ip'=>'ips'));
		$sql = $sql->join(array('u' => 'users'), 'u.id = ip.creator', array('first_name', 'last_name'), 'left');
		if($where)
		{
			$sql = $sql->where($where);
		}
		
		return $this->getRow($sql);		
	}
	
	/**
	 * Returns the IP for the pk
	 * @param int $id
	 */
	public function getIpById($id)
	{
		$sql = $this->db->select()->from(array('ip'=>'ips'));
		$sql = $sql->where(array('ip.id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Checks if the provided Ip Address is allowed in the system
	 * @param int $ip
	 */
	public function isAllowed($ip)
	{
		$sql = $this->db->select()->from(array('ip' => 'ips'))->columns( array('id'))->where(array('ip' => ip2long($ip), 'confirm_key' => null));
		$data = $this->getRow($sql);
		return $data;
	}
	
	/**
	 * Adds an Ip Address to the white list
	 * @param array $data
	 * @param int $creator
	 */
	public function addIp(array $data, $creator)
	{
		if($this->isAllowed($data['ip']))
		{
			return TRUE;
		}
		
		$sql = $this->getSQL($data);
		$sql['creator'] = $creator;
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		if($this->insert('ips', $sql))
		{
		    return TRUE;			
		}	
	}
	
	/**
	 * Removes an Ip Address from the white list
	 * @param string $key
	 * @param stirng $col
	 */
	public function removeIp($id)
	{
		if($this->remove('ips', array('id' => $id)))
		{
		    return TRUE;
		}
	}
	
	/**
	 * Updates an Ip Address on the white list
	 * @param array $data
	 * @param int $id
	 */
	public function updateIp(array $data, $id)
	{
		$sql = $this->getSQL($data);
		if($this->update('ips', $sql, array('id' => $id)))
		{
		    return TRUE;	
		}
	}
	
	/**
	 * Processes an IP address a user wants to verify for access
	 * @param string $ip
	 * @param array $user_data
	 * @param \Application\Model\Mail $mail
	 */
	public function allowSelf($ip, array $user_data, \Application\Model\Mail $mail, \Application\Model\Hash $hash)
	{
		$hash = $hash->guidish();
		$where = array('ip' => ip2long($ip));
		$check = $this->getIps($where);
		$success = false;
		if(!$check)
		{
			$sql = $this->getSQL(array('ip' => $ip, 'description' => 'Self Added'));
			$sql['confirm_key'] = $hash;;
			$sql['creator'] = $user_data['id'];
			$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
			$success = $this->insert('ips', $sql);
		}
		else
		{
			$sql = array('confirm_key' => $hash, 'last_modified' => new \Zend\Db\Sql\Expression('NOW()'));
			$success = $this->update('ips', $sql, $where);
		}
		
		if($success)
		{
			$mail->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
			$mail->setViewDir($mail->getModulePath(__DIR__).'/view/emails');
			$mail->setEmailView('ip-self-allow', array('user_data' => $user_data, 'user_id' => $user_data['id'], 'verify_code' => $hash));
			$mail->setTranslationDomain('pm');
			$mail->setSubject('email_subject_ip_self_allow');
			
			if($mail->send())
			{
				return true;
			}
		}	
	}
	
	public function allowCodeAccess($code)
	{
		return $this->update('ips', array('confirm_key' => ''), array('confirm_key' => $code));
	}
}