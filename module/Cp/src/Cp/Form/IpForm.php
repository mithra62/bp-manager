<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Forms/IpForm.php
 */
namespace PM\Form;

use Base\Form\BaseForm;

/**
 * Ip Locker Form
 *
 * Generates the Ip Locker form
 *
 * @package IpLocker
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Forms/IpForm.php
 *            
 */
class IpForm extends BaseForm
{

    /**
     * Returns the Ip Locker form
     * 
     * @param string $options            
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->add(array(
            'name' => 'ip',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large',
                'id' => 'ip'
            )
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'description',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
    }
}