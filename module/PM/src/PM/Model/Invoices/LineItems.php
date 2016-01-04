<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Invoices/LineItems.php
 */

namespace PM\Model\Invoices;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

/**
 * PM - Invoice Line Item Model
 *
 * @package 	Companies\Invoices
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Invoices/LineItems.php
 */
class LineItems extends AbstractModel
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
    		'time_id' => (!empty($data['time_id']) ? $data['time_id'] : 0),
    		'name' => (!empty($data['name']) ? $data['name'] : ''),
    		'description' => (!empty($data['description']) ? $data['description'] : ''),
    		'unit_cost' => (!empty($data['unit_cost']) ? $data['unit_cost'] : 0),
    		'total_cost' => (!empty($data['total_cost']) ? $data['total_cost'] : 0),
    		'quantity' => (!empty($data['quantity']) ? $data['quantity'] : 1),
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
	 * Takes the POST data and parses out all the line item options
	 * @param array $line_items
	 * @return array
	 */
	public function parseItems(array $line_items)
	{	
		$invoiced_times = $this->parseTimeItems($line_items);
		$invoiced_items = $this->parseLineItems($line_items);
		return array_merge($invoiced_items, $invoiced_times);
	}
	
	/**
	 * Parses the POST data to prepare the line items for storage
	 * @param array $line_items
	 * @return Ambigous <multitype:, unknown>
	 */
	public function parseLineItems(array $line_items)
	{
		$invoiced_items = array();
		if( !empty($line_items['line_item']) && is_array($line_items['line_item']) && count(empty($line_items['line_item'])) >= 1)
		{
			$i = 0;
			foreach($line_items['line_item'] As $item)
			{
				if( !empty($item['item']) && !empty($item['unit_cost']) && !empty($item['qty']))
				{
					$invoiced_items[$i]['name'] = $item['item'];
					$invoiced_items[$i]['quantity'] = $item['qty'];
					$invoiced_items[$i]['description'] = $item['description'];
					$invoiced_items[$i]['total_cost'] = $item['qty']*$item['unit_cost'];	
					$invoiced_items[$i]['unit_cost'] = $item['unit_cost'];					
				}
				$i++;
			}
		}
		
		return $invoiced_items;
	}
	
	/**
	 * Parses the POST data for the time entry specific entries
	 * @param array $line_items
	 * @return Ambigous <number, multitype:multitype:NULL unknown  >
	 */
	public function parseTimeItems(array $line_items)
	{
		$invoiced_times = array();
		$time_ids = array();
		if( !empty($line_items['invoice_time']) && is_array($line_items['invoice_time']) && count(empty($line_items['invoice_time'])) >= 1)
		{
			foreach($line_items['invoice_time'] AS $time_id => $value)
			{
				if( !empty($line_items['hour_rate_field'][$time_id]) )
				{
					$invoiced_times[] = array(
							'unit_cost' => $line_items['hour_rate_field'][$time_id],
							'time_id' => $time_id
					);
					$time_ids[] = $time_id;
				}
			}
				
			if( count($time_ids) >= 1)
			{
				//we have some times so we need to do some math now
				$sql = $this->db->select()->from(array('t' => 'times'));
				$sql->where->in('t.id', $time_ids);
		
				$sql->join(array('p' => 'projects'), 'p.id = t.project_id', array('project_name' => 'name'), 'left');
				$sql->join(array('ta' => 'tasks'), 'ta.id = t.task_id', array('task_name' => 'name'), 'left');
				$time_data = $this->getRows($sql);
				if($time_data)
				{
		
					foreach($time_data AS $time)
					{
						foreach($invoiced_times AS $key => $invoiced_time)
						{
							if($invoiced_time['time_id'] == $time['id'])
							{
								if( !empty($time['task_name']) )
								{
									$invoiced_times[$key]['name'] = $time['project_name'].' => '.$time['task_name'];
								}
								elseif( !empty($time['project_name']) )
								{
									$invoiced_times[$key]['name'] = $time['project_name'];
								}
								else
								{
									$invoiced_times[$key]['name'] = 'na';
								}
								$invoiced_times[$key]['name'] .= ' ('.$time['date'].')';
								$invoiced_times[$key]['quantity'] = $time['hours'];
								$invoiced_times[$key]['description'] = $time['description'];
								$invoiced_times[$key]['total_cost'] = $time['hours']*$invoiced_time['unit_cost'];
							}
						}
					}
				}
			}
		}	
		return $invoiced_times;	
	}
	
	/**
	 * Returns a specific Invoice by its PK
	 * @param int $id
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getInvoiceLineItems($id)
	{
		$sql = $this->db->select()->from(array('i'=> 'invoices'));
		$sql = $sql->where(array('i.id' => $id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = i.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('o' => 'companies'), 'o.id = i.company_id', array('company_name' => 'name'), 'left');
		
		return $this->getRow($sql);
	}
	
	/**
	 * Returns an array of all contacts based on type
	 * @return mixed
	 */
	public function getAllLineItems($view_type = FALSE)
	{
		$sql = $this->db->select();
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('type' => $view_type));
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns all the line items for a given $invoice_id
	 * @param int $invoice_id
	 * @return array
	 */
	public function getLineItemByInvoiceId($invoice_id)
	{
		$sql = $this->db->select()->from(array('ili' => 'invoice_line_items'));
		
		$sql = $sql->where(array('ili.invoice_id' => $invoice_id));
		$sql = $sql->join(array('t' => 'times'), 't.id = ili.time_id', array('project_id', 'task_id', 'user_id'), 'left');
		
		return $this->getRows($sql);			
	}	
	
	/**
	 * Adds an Invoice Line Item to the system
	 * @param int $invoice_id
	 * @param array $data
	 * @return Ambigous <\Zend\EventManager\mixed, NULL, mixed>|Ambigous <\Base\Model\Ambigous, \Zend\Db\Adapter\Driver\mixed, NULL, \Zend\EventManager\mixed, mixed>
	 */
	public function addLineItem($invoice_id, array $data)
	{
	    $ext = $this->trigger(self::EventInvoiceLineItemAddPre, $this, compact('data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    
		$sql = $this->getSQL($data);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$sql['invoice_id'] = $invoice_id;
		
		$invoice_id = $this->insert('invoice_line_items', $sql);
		
		$ext = $this->trigger(self::EventInvoiceLineItemAddPost, $this, compact('data', 'invoice_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $contact_id = $ext->last();	
		
		return $contact_id;
	}
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateLineItem($data, $id)
	{
	    $ext = $this->trigger(self::EventInvoiceLineItemUpdatePre, $this, compact('data', 'id'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
	    	    
		$sql = $this->getSQL($data);
		$update = $this->update('invoices', $sql, array('id' => $id));
		
		$ext = $this->trigger(self::EventInvoiceLineItemUpdatePost, $this, compact('data', 'id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $update = $ext->last();		
		
		return $update;
	}
	
	/**
	 * Handles everything for removing an invoice's line items.
	 * @param $id
	 * @return bool
	 */
	public function removeLineItem($id)
	{
	    $data = $this->getContactById($id);
	    $ext = $this->trigger(self::EventInvoiceLineItemRemovePre, $this, compact('id', 'data'), $this->setXhooks($data));
	    if($ext->stopped()) return $ext->last(); elseif($ext->last()) $id = $ext->last();
	    	    
		$remove = $this->remove('invoices', array('id' => $id));
		
		$ext = $this->trigger(self::EventInvoiceLineItemRemovePost, $this, compact('id', 'data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $remove = $ext->last();
				
		return $remove;
	}
}