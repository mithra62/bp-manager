<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Api/src/Api/Controller/OptionsController.php
*/

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * Api - Options Controller
 *
 * Options REST API Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Controller/OptionsController.php
 */
class OptionsController extends AbstractRestfulJsonController
{
	/**
	 * Maps the available HTTP verbs we support for groups of data
	 * @var array
	 */
	protected $collectionOptions = array(
		'GET', 'POST', 'OPTIONS'
	);
	
	/**
	 * Maps the available HTTP verbs for single items
	 * @var array
	 */
	protected $resourceOptions = array(
		'GET', 'POST', 'DELETE', 'PUT', 'OPTIONS'
	);
		
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::getList()
	 */
	public function getList()
	{
		$area = $this->getRequest()->getQuery('area', false);
		$order = $this->getRequest()->getQuery('order', false);
		$order_dir = $this->getRequest()->getQuery('order_dir', false);
		$limit = $this->getRequest()->getQuery('limit', 10);
		$page = $this->getRequest()->getQuery('page', 1);
		
		$option = $this->getServiceLocator()->get('Api\Model\Options');
		
		if($area)
		{
			$option->setWhere(array('area' => $area));
		}
		
		$options = $option->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAllOptions();
		if(!$options)
		{
			return $this->setError(404, 'not_found');
		}

		$options['data'] = $this->cleanCollectionOutput($options['data'], $option->optionsOutputMap);
		return new JsonModel( $this->setupHalCollection($options, 'api-options', 'options', 'options/view', 'option_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::get()
	 */
	public function get($id)
	{
		$option = $this->getServiceLocator()->get('Api\Model\Options');
		$option_data = $option->getOptionById($id);
		if(!$option_data)
		{
			return $this->setError(404, 'not_found');
		}

		$option_data = $this->cleanResourceOutput($option_data, $option->optionsOutputMap);
		return new JsonModel( $this->setupHalResource($option_data, 'api-options', array(), 'options/view', 'option_id') );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::create()
	 */
	public function create($data)
	{
		if(!parent::check_permission('manage_options'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$option = $this->getServiceLocator()->get('Api\Model\Options');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
		$inputFilter = $option->getInputFilter($translate);
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
		
		$option_id = $option->addOption($data, $this->identity);
		if(!$option_id)
		{
			return $this->setError(500, 'option_create_failed');
		}	

		$option_data = $option->getOptionById($option_id);
		$option_data = $this->cleanResourceOutput($option_data, $option->optionsOutputMap);
		return new JsonModel( $this->setupHalResource($option_data, 'api-options', array(), 'options/view', 'option_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::delete()
	 */
	public function delete($id)
	{
		if(!parent::check_permission('manage_options'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$option = $this->getServiceLocator()->get('Api\Model\Options');
		$option_data = $option->getOptionById($id);
		if(!$option_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		if(!$option->removeOption($id))
		{
			return $this->setError(500, 'option_remove_failed');
		}
	
		return new JsonModel( );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::update()
	 */
	public function update($id, $data)
	{
		if(!parent::check_permission('manage_options'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$option = $this->getServiceLocator()->get('Api\Model\Options');
		$option_data = $option->getOptionById($id);
		if (!$option_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		$inputFilter = $option->getInputFilter($translate);
		$data = array_merge($option_data, $data);
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
	
		try {
				
			$option->updateOption($data, $id);
				
		} catch(Zend_Exception $e)
		{
			return $this->setError(500, 'option_update_failed');
		}

		$option_data = $option->getOptionById($id);
		$option_data = $this->cleanResourceOutput($option_data, $option->optionsOutputMap);
		return new JsonModel( $this->setupHalResource($option_data, 'api-options', array(), 'options/view', 'option_id') );
	}
}
