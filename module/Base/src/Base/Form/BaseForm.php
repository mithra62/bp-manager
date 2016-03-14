<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Base/src/Base/BaseForm.php
 */
namespace Base\Form;

use Zend\Form\Form;

/**
 * Base - Form
 *
 * Adds the constant form elements
 * <br /><strong>The Base Form should be the parent of any Form objects within the system</strong>
 *
 * @abstract
 *
 * @package BackupProServer\Form
 * @author Eric Lamb <eric@mithra62.com>
 *        
 */
abstract class BaseForm extends Form
{

    /**
     * Simple Yes/No translations
     * 
     * @var array
     */
    public $yn_arr = array(
        '0' => 'No',
        '1' => 'Yes'
    );

    /**
     * Adds the constant form elements
     * 
     * @param string $options            
     */
    public function __construct($form_name, array $options = array())
    {
        parent::__construct($form_name, $options);
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => '_x',
            'type' => 'Csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 3600
                )
            )
        ));
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\Form\Form::add()
     */
    public function add($elementOrFieldset, array $flags = array())
    {
        if (! isset($elementOrFieldset['attributes']['id']) && strtolower($elementOrFieldset['type']) != 'crsf') {
            $elementOrFieldset['attributes']['id'] = $elementOrFieldset['name'];
        }
        parent::add($elementOrFieldset, $flags);
        return $this;
    }
}