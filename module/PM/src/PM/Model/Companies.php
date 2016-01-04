<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Companies.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

 /**
 * PM - Companies Model
 *
 * @package 	Companies
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Companies.php
 */
class Companies extends AbstractModel
{
	/**
	 * The validation filter object
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $inputFilter;
	
	/**
	 * The Companies Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Sets the input validation filter
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns the input filter
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'name',
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
	
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
			'name' => $data['name'],
			'phone1' => $data['phone1'],
			'phone2' => $data['phone2'],
			'fax' => $data['fax'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'city' => $data['city'],
			'state' => $data['state'],
			'zip' => $data['zip'],
			'primary_url' => $data['primary_url'],
			'description' => $data['description'],
			'type' => $data['type'],
			'custom' => $data['custom'],
			'default_hourly_rate' => (!empty($data['default_hourly_rate']) ? $data['default_hourly_rate'] : '0'),
			'client_language' => (!empty($data['client_language']) ? $data['client_language'] : 'en_US'),
			'currency_code' => (!empty($data['currency_code']) ? $data['currency_code'] : 'USD'),
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getCompanyIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $this->db->getCompany($sql);
	}
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getCompanyIdByHarvestId($harvest_id)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('harvest_id = ?', $harvest_id);
					  
		$company = $this->db->getCompany($sql);
		if($company)
		{
			return $company['id'];
		}
	}	
	
	/**
	 * Returns a company by its id
	 * @param int $id
	 * @param array $what
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getCompanyById($id, array $what = null)
	{
		$sql = $this->db->select();
		if(is_array($what))
		{
			$sql->from(array('c'=> 'companies'), $what);
		}
		else
		{
			$sql->from(array('c'=> 'companies'));
		}
				
		$sql->where(array('id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Returns all the company names
	 * @param string $type
	 * @param string $ids
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getAllCompanyNames($type = FALSE, $ids = FALSE)
	{
		$sql = $this->db->select()->from('companies', array('id','name'));
		if($type && is_array($type))
		{ 
			$where = $sql->where->in('type', $type);
			$sql = $sql->where($where);
		}
		
		if($ids && is_array($ids))
		{ 
			$sql = $sql->where('id IN (?)', $ids);
		}
		
		$sql = $sql->order('name ASC');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns all the companies
	 * @param string $view_type
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getAllCompanies($view_type = FALSE)
	{
		$sql = $this->db->select()->from('companies');
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('type' => $view_type));
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Calculates how many projects a given comapny has
	 * @param int $id
	 * @return string
	 */
	public function getProjectCount($id)
	{
		$sql = $this->db->select()
					->from('projects')->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(id)')))
					->where(array('company_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return (!empty($data['count']) ? $data['count'] : '0');
		}
	}
	
	/**
	 * Returns the total tasks a company has
	 * @param int $id
	 * @return int
	 */
	public function getTaskCount($id)
	{
		$sql = $this->db->select()
					->from('tasks')->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(id)')))
					->where(array('company_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return (!empty($data['count']) ? $data['count'] : '0');
		}		
	}
	
	/**
	 * Returns the total files a company has
	 * @param int $id
	 * @return int
	 */
	public function getFileCount($id)
	{
		$sql = $this->db->select()
					->from('files')->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(id)')))
					->where(array('company_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return (!empty($data['count']) ? $data['count'] : '0');
		}		
	}
	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @return int
	 */
	public function addCompany($data)
	{
	    $ext = $this->trigger(self::EventCompanyAddPre, $this, compact('data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$company_id = $this->insert('companies', $sql);
		
		$ext = $this->trigger(self::EventCompanyAddPost, $this, compact('data', 'company_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		return $company_id;
	}
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int $company_id
	 * @return bool
	 */
	public function updateCompany($data, $company_id)
	{
	    $ext = $this->trigger(self::EventCompanyUpdatePre, $this, compact('data', 'company_id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$update = $this->update('companies', $sql, array('id' => $company_id));
		
		$ext = $this->trigger(self::EventCompanyAddPost, $this, compact('data', 'company_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $update = $ext->last();

		return $update;
	}
	
	/**
	 * Updates the total project counts for a given $id
	 * @param int $id
	 * @param int $count
	 * @param string $col
	 * @return bool
	 */
	public function updateCompanyProjectCount($id, $count = 1, $col = 'active_projects')
	{
		$sql = array($col => new \Zend\Db\Sql\Expression($col.'='.$col.'+'.$count));
		return $this->update('companies', $sql, array('id' => $id));
	}	
	
	/**
	 * Handles everything for removing a company.
	 * @param $id
	 * @return bool
	 */
	public function removeCompany($company_id)
	{
		$ext = $this->trigger(self::EventCompanyRemovePre, $this, compact('company_id'), $this->setXhooks(array()));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		$where = array('id' => $company_id);
		$delete = $this->remove('companies', $where);
		
		$ext = $this->trigger(self::EventCompanyRemovePost, $this, compact('company_id'), $this->setXhooks(array()));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $update = $ext->last();
		
		return TRUE;
	}
	
	/**
	 * Sets up the contextual hooks based on $data
	 * @param array $data
	 * @return array
	 */
	public function setXhooks(array $data = array())
	{
		$return = array();
		if(!empty($data['type']))
			$return[] = array('type' => $data['type']);
	
		if(!empty($data['zip']))
			$return[] = array('zip' => $data['zip']);
	
		if(!empty($data['state']))
			$return[] = array('state' => $data['state']);
	
		if(!empty($data['company_id']))
			$return[] = array('company' => $data['company_id']);
	
		return $return;
	}	
}