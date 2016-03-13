<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Sites/src/Sites/Model/Sites.php
 */
namespace Sites\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

/**
 * Sites - Sites Locker Model
 *
 * @package mithra62\BackupPro
 * @author Eric Lamb
 * @filesource ./module/Sites/src/Sites/Model/Sites.php
 */
class Sites extends AbstractModel
{

    protected $inputFilter;

    /**
     * The Sites Model
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Sql\Sql $db
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
    {
        parent::__construct($adapter, $db);
    }

    /**
     * Sets the input filter
     * @param InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    /**
     * Returns an instance of the input filter
     * @param \Zend\I18n\View\Helper\Translate $translator
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter(\Zend\I18n\View\Helper\Translate $translator)
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
        
            $inputFilter->add($factory->createInput(array(
                'name' => 'api_endpoint_url',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => $translator('api_endpoint_url_required', 'sites')
                            ),
                        ),
                    ),
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'api_key',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => $translator('api_key_required', 'sites')
                            ),
                        ),
                    ), 
                    array(
                        'name' => '\Sites\Validate\Site\Connect',
                        'options' => array(
                            'site' => $this,
                        )
                    )
                )
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'api_secret',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => $translator('api_secret_required', 'sites')
                            ),
                        ),
                    ),
                )
            )));
        
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
    
    /**
     * Returns all the system users
     *
     * @param string $status
     * @return array
     */
    public function getAllSites($status = FALSE)
    {
        $sql = $this->db->select()->from('sites');
    
        if ($status != '') {
            $sql = $sql->where(array(
                'user_status' => $status
            ));
        }
    
        return $this->getRows($sql);
    }
    
}