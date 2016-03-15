<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		1.0
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
    
    protected $api = null;
    
    protected $team = null;

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
     * Returns an array for modifying $_name
     *
     * @param
     *            $data
     * @return array
     */
    public function getSQL($data)
    {
        return array(
            'api_endpoint_url' => (! empty($data['api_endpoint_url']) ? $data['api_endpoint_url'] : ''),
            'site_name' => (! empty($data['site_name']) ? $data['site_name'] : ''),
            'platform' => (! empty($data['platform']) ? $data['platform'] : ''),
            'api_key' => (! empty($data['api_key']) ? $data['api_key'] : ''),
            'api_secret' => (! empty($data['api_secret']) ? $data['api_secret'] : ''),
            'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
        );
    }
    
    /**
     * Sets the API object
     * @param Api $api
     * @return \Sites\Model\Sites
     */
    public function setApi(Api $api)
    {
        $this->api = $api;
        return $this;
    }
    
    /**
     * Returns the API object
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }
    
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }
    
    public function getTeam()
    {
        return $this->team;
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
     * @param bool $unique Whether unique entries should verified
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter(\Zend\I18n\View\Helper\Translate $translator, $site_id = false)
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
        
            if($site_id)
            {
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
                        array(
                            'name' => 'Sites\Validate\Site\ChangeEndpointUrl',
                            'options' => array(
                                'table' => 'sites',
                                'field' => 'api_endpoint_url',
                                'adapter' => $this->adapter,
                                'site' => $this,
                                'id' => $site_id
                            )
                        )
                    )
                )));
            }
            else 
            {
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
                        array(
                            'name' => 'Db\NoRecordExists',
                            'options' => array(
                                'table' => 'sites',
                                'field' => 'api_endpoint_url',
                                'adapter' => $this->adapter,
                                'messages' => array(
                                    'recordFound' => $translator('create_site_exist_error', 'sites')
                                )
                            )
                        )
                    )
                )));
            }

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
    
    /**
     * Returns a site by its ID
     * @param int $id
     * @param \Application\Model\Hash $hash
     * @return array
     */
    public function getSiteById($id, \Application\Model\Hash $hash = null) 
    {
        $sql = $this->db->select()->from('sites');
        $sql = $sql->where(array(
            'id' => $id
        ));
        
        $data = $this->getRow($sql);
        if($data && !is_null($hash))
        {
            $data['api_secret'] = $hash->decrypt($data['api_secret']);
        }
        
        return $data;
    }
    
    /**
     * Returns a site by its ID
     * @param int $id
     * @param \Application\Model\Hash $hash
     * @return array
     */
    public function getSiteByEndpointUrl($url, \Application\Model\Hash $hash = null) 
    {
        $sql = $this->db->select()->from('sites');
        $sql = $sql->where(array(
            'api_endpoint_url' => $url
        ));
        
        $data = $this->getRow($sql);
        if($data && !is_null($hash))
        {
            $data['api_secret'] = $hash->decrypt($data['api_secret']);
        }
        
        return $data;
    }
    
    /**
     * Creates a member
     *
     * @param array $data
     * @param \Application\Model\Hash $hash
     * @return int
     */
    public function addSite(array $data, \Application\Model\Hash $hash)
    {
        $ext = $this->trigger(self::EventSiteAddPre, $this, compact('data', 'hash'), $this->setXhooks($data));
        if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $data = $ext->last();
        
        if(empty($data['site_name']))
        {
            $api_data = $this->getApi()->getSiteDetails($data['api_key'], $data['api_secret'], $data['api_endpoint_url']);
            $data += $api_data;
        }
        
        $sql = $this->getSQL($data);
        $sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
        $sql['api_secret'] = $hash->encrypt($data['api_secret']);
        $site_id = $data['site_id'] = $this->insert('sites', $sql);
        if ($site_id) {
    
            $ext = $this->trigger(self::EventSiteAddPost, $this, compact('site_id', 'data', 'hash'), $this->setXhooks($data));
            if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $site_id = $ext->last();
    
            return $site_id;
        }
    }    
    
    /**
     * Updates a site entry
     * @param int $id
     * @param array $data
     * @param \Application\Model\Hash $hash
     */
    public function updateSite($site_id, array $data, \Application\Model\Hash $hash)
    {
        $ext = $this->trigger(self::EventSiteUpdatePre, $this, compact('site_id', 'data', 'hash'), $this->setXhooks($data));
        if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $data = $ext->last();
        
        if(empty($data['site_name']))
        {
            $api_data = $this->getApi()->getSiteDetails($data['api_key'], $data['api_secret'], $data['api_endpoint_url']);
            $data += $api_data;
        }
        
        $sql = $this->getSQL($data);
        $sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
        $sql['api_secret'] = $hash->encrypt($data['api_secret']);
        if ($this->update('sites', $sql, array('id' => $site_id))) {
    
            $ext = $this->trigger(self::EventSiteUpdatePost, $this, compact('site_id', 'data', 'hash'), $this->setXhooks($data));
            if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $site_id = $ext->last();
    
            return $site_id;
        }
    }
    
    public function removeSite($site_id)
    {
        $ext = $this->trigger(self::EventSiteRemovePre, $this, compact('site_id'), array());
        if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $site_id = $ext->last();
    
        if ($this->remove('sites', array('id' => $site_id))) {
    
            $ext = $this->trigger(self::EventSiteRemovePost, $this, compact('site_id'), array());
            if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $site_id = $ext->last();
            
            return $site_id;
        }
    
    }    
    
}