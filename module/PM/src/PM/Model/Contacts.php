<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Projects.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

/**
 * PM - Company Contacts Model
 *
 * @package 	Companies\Contacts
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Projects.php
 */
class Contacts extends AbstractModel
{
	
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $inputFilter;

	/**
	 * The Project Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
    		'job_title' => (!empty($data['job_title']) ? $data['job_title'] : ''),
    		'company_id' => (!empty($data['company_id']) ? $data['company_id'] : ''),
    		'first_name' => (!empty($data['first_name']) ? $data['first_name'] : ''),
    		'last_name' => (!empty($data['last_name']) ? $data['last_name'] : ''),
    		'title' => (!empty($data['title']) ? $data['title'] : ''),
    		'email' => (!empty($data['email']) ? $data['email'] : ''),
    		'email2' => (!empty($data['email2']) ? $data['email2'] : ''),
    		'url' => (!empty($data['url']) ? $data['url'] : ''),
    		'phone_home' => (!empty($data['phone_home']) ? $data['phone_home'] : ''),
    		'phone2' => (!empty($data['phone2']) ? $data['phone2'] : ''),
    		'fax' => (!empty($data['fax']) ? $data['fax'] : ''),
    		'mobile' => (!empty($data['mobile']) ? $data['mobile'] : ''),
    		'address1' => (!empty($data['address1']) ? $data['address1'] : ''),
    		'address2' => (!empty($data['address2']) ? $data['address2'] : ''),
    		'city' => (!empty($data['city']) ? $data['city'] : ''),
    		'state' => (!empty($data['state']) ? $data['state'] : ''),
    		'zip' => (!empty($data['zip']) ? $data['zip'] : ''),
    		'description' => (!empty($data['description']) ? $data['description'] : ''),
    		'jabber' => (!empty($data['jabber']) ? $data['jabber'] : ''),
    		'icq' => (!empty($data['icq']) ? $data['icq'] : ''),
    		'msn' => (!empty($data['msn']) ? $data['msn'] : ''),
    		'yahoo' => (!empty($data['yahoo']) ? $data['yahoo'] : ''),
    		'aol' => (!empty($data['aol']) ? $data['aol'] : ''),
    		'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Sets the input filter to use
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns the InputFilter
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'email',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'first_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	public function getContactById($id)
	{
		$sql = $this->db->select()->from(array('c'=> 'company_contacts'));
		$sql = $sql->where(array('c.id' => $id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = c.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('o' => 'companies'), 'o.id = c.company_id', array('company_name' => 'name'), 'left');
		
		return $this->getRow($sql);
	}
	
	/**
	 * Returns an array of all contacts based on type
	 * @return mixed
	 */
	public function getAllContacts($view_type = FALSE)
	{
		$sql = $this->db->select();
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('type' => $view_type));
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns all the times for a given $id
	 * @param int $id
	 * @return array
	 */
	public function getContactsByCompanyId($id)
	{
		$sql = $this->db->select()->from(array('c'=>'company_contacts'));
		
		$sql = $sql->where(array('c.company_id' => $id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = c.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		
		return $this->getRows($sql);			
	}	
	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addContact($data)
	{
	    $ext = $this->trigger(self::EventContactAddPre, $this, compact('data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		
		$contact_id = $this->insert('company_contacts', $sql);
		
		$ext = $this->trigger(self::EventContactUpdatePost, $this, compact('data', 'contact_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $contact_id = $ext->last();	
		
		return $contact_id;
	}
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateContact($data, $id)
	{
	    $ext = $this->trigger(self::EventContactUpdatePre, $this, compact('data', 'id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$update = $this->update('company_contacts', $sql, array('id' => $id));
		
		$ext = $this->trigger(self::EventContactUpdatePost, $this, compact('data', 'id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $update = $ext->last();		
		
		return $update;
	}
	
	/**
	 * Handles everything for removing a company.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeContact($id)
	{
	    $data = $this->getContactById($id);
	    $ext = $this->trigger(self::EventContactRemovePre, $this, compact('id', 'data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $id = $ext->last();
	    	    
		$remove = $this->remove('company_contacts', array('id' => $id));
		
		$ext = $this->trigger(self::EventContactRemovePost, $this, compact('id', 'data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $remove = $ext->last();
				
		return $remove;
	}
}