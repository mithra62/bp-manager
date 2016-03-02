<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Sites/src/Sites/Form/SiteForm.php
 */
namespace Sites\Form;

use Base\Form\BaseForm;

/**
 * SiteForm Form
 *
 * Generates the UsersForm form
 *
 * @package Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Form/UsersForm.php
 *            
 */
class SiteForm extends BaseForm
{

    /**
     * Returns the Users form
     * 
     * @param string $name            
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'api_endpoint_url',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'api_endpoint_url'
            )
        ));
    }
}