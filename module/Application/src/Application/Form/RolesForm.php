<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Form/RolesForm.php
 */
namespace Application\Form;

use Base\Form\BaseForm;

/**
 * Roles Form
 *
 * Generates the Roles form
 *
 * @package Users\Roles
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/RolesForm.php
 *            
 */
class RolesForm extends BaseForm
{

    /**
     * Returns the User Roles form
     * 
     * @param string $options            
     */
    public function __construct($name, \Application\Model\User\Roles $roles)
    {
        parent::__construct($name);
        
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'input large',
                'id' => 'name'
            )
        ));
        
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'description',
            'attributes' => array(
                'class' => 'styled_textarea',
                'rows' => '7',
                'cols' => '40'
            )
        ));
        
        $permissions = $roles->getAllPermissions();
        
        foreach ($permissions as $perm) {
            $this->add(array(
                'type' => 'Checkbox',
                'name' => $perm['name'],
                'options' => array(
                    'use_hidden_element' => true,
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                    'id' => $perm['name']
                )
            ));
        }
    }
}