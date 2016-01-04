<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Invoices.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

/**
 * PM - Invoices Model
 *
 * @package 	Companies\Invoices
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Invoices.php
 */
class Invoices extends AbstractModel
{
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $inputFilter;
	
	/**
	 * An instance of the LineItem object
	 * @var \PM\Model\Invoices\LineItems
	 */
	public $lineItem = null;

	/**
	 * The Project Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db, \PM\Model\Invoices\LineItems $li)
	{
		parent::__construct($adapter, $db);
		$this->lineItem = $li;
	}
	
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
    		'invoice_number' => (!empty($data['invoice_number']) ? $data['invoice_number'] : ''),
    		'status' => (!empty($data['status']) ? $data['status'] : ''),
    		'date' => (!empty($data['date']) ? $data['date'] : ''),
    		'po_number' => (!empty($data['po_number']) ? $data['po_number'] : ''),
    		'discount' => (!empty($data['discount']) ? $data['discount'] : ''),
    		'terms_conditions' => (!empty($data['terms_conditions']) ? $data['terms_conditions'] : ''),
    		'notes' => (!empty($data['notes']) ? $data['notes'] : ''),
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
				'name'     => 'invoice_number',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'Db\NoRecordExists',
						'options' => array(
							'table' => 'invoices',
						    'field' => 'invoice_number',
							'adapter' => $this->adapter
						)
					),
				),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'date',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => '\Zend\Validator\Date'
					),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Generates an invoice number using the last invoice number as a baseline
	 * @return string
	 */
	public function getNextInvoiceNumber()
	{
		$sql = $this->db->select()->columns(array('invoice_number' => new \Zend\Db\Sql\Expression('MAX(invoice_number)')))->from('invoices');
		$result = $this->setOrder('id')->setLimit(1)->setOrderDir('DESC')->getRow($sql);
		if(!$result) {
			$value = 1;
		}
		else {
			$value = $result['invoice_number'];
		}
		
		$value++;
		return str_pad($value, 7, "0", STR_PAD_LEFT);
	}
	
	/**
	 * Returns a specific Invoice by its PK
	 * @param int $id
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getInvoiceById($id)
	{
		$sql = $this->db->select()->from(array('i'=> 'invoices'));
		$sql = $sql->where(array('i.id' => $id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = i.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('o' => 'companies'), 'o.id = i.company_id', array('company_name' => 'name'), 'left');
		
		$data = $this->getRow($sql);
		if($data)
		{
			$data['line_items'] = $this->lineItem->getLineItemByInvoiceId($id);
		}
		
		return $data;
	}
	
	/**
	 * Returns an array of all contacts based on type
	 * @return mixed
	 */
	public function getAllInvoices($view_type = FALSE)
	{
		$sql = $this->db->select();
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('type' => $view_type));
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns all the invoices for a given $company_id
	 * @param int $company_id
	 * @return array
	 */
	public function getInvoicesByCompanyId($company_id)
	{
		$sql = $this->db->select()->from(array('i' => 'invoices'));
		
		$sql = $sql->where(array('i.company_id' => $company_id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = i.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		
		return $this->getRows($sql);			
	}	
	
	/**
	 * Adds an Invoice to the system
	 * @param int $company_id
	 * @param array $data
	 * @return Ambigous <\Zend\EventManager\mixed, NULL, mixed>|Ambigous <\Base\Model\Ambigous, \Zend\Db\Adapter\Driver\mixed, NULL, \Zend\EventManager\mixed, mixed>
	 */
	public function addInvoice($company_id, array $data, array $line_items = array())
	{
	    $ext = $this->trigger(self::EventInvoiceAddPre, $this, compact('data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    
		$sql = $this->getSQL($data);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$sql['creator'] = $data['creator'];
		$sql['company_id'] = $company_id;
		
		$invoice_id = $this->insert('invoices', $sql);
		if($line_items)
		{
			$total = 0;
			foreach($line_items As $item)
			{
				$this->lineItem->addLineItem($invoice_id, $item);
				$total = $total+$item['total_cost'];
			}
		}
		
		if($total > 0)
		{
			$this->update('invoices', array('amount' => $total), array('id' => $invoice_id));
		}
		
		$ext = $this->trigger(self::EventInvoiceAddPost, $this, compact('data', 'invoice_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $invoice_id = $ext->last();	
		
		return $invoice_id;
	}
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateInvoice($data, $id)
	{
	    $ext = $this->trigger(self::EventInvoiceUpdatePre, $this, compact('data', 'id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$update = $this->update('invoices', $sql, array('id' => $id));
		
		$ext = $this->trigger(self::EventInvoiceUpdatePost, $this, compact('data', 'id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $update = $ext->last();		
		
		return $update;
	}
	
	/**
	 * Handles everything for removing an invoice.
	 * @param $invoice_id
	 * @return bool
	 */
	public function removeInvoice($invoice_id)
	{
	    $ext = $this->trigger(self::EventInvoiceRemovePre, $this, compact('invoice_id'));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $invoice_id = $ext->last();
	    	    
		$remove = $this->remove('invoices', array('id' => $invoice_id));
		if($remove)
		{
			$this->remove('invoices_line_items', array('invoice_id' => $invoice_id));
		}
		
		$ext = $this->trigger(self::EventInvoiceRemovePost, $this, compact('remove', 'invoice_id'));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $remove = $ext->last();
				
		return $remove;
	}
}