<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Model/Sites.php
 */
namespace Sites\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;
use Sites\InputFilter\Sites\Settings AS SettingsInputFilter;
use \DateTime;

/**
 * Sites - Sites Locker Model
 *
 * @package mithra62\BackupPro
 * @author Eric Lamb
 * @filesource ./module/Sites/src/Sites/Model/Sites.php
 */
class Sites extends AbstractModel
{

    /**
     * The InputFilter object
     * @var InputFilter
     */
    protected $inputFilter;
    
    /**
     * The API object
     * @var Api
     */
    protected $api = null;
    
    /**
     * An instance of the Sites\Team object
     * @var Sites\Team
     */
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
            'errors' => (! empty($data['_system_errors']) ? json_encode($data['_system_errors']) : ''),
            'file_backup_total' => (! empty($data['file_backup_total']) ? $data['file_backup_total'] : 0),
            'database_backup_total' => (! empty($data['database_backup_total']) ? $data['database_backup_total'] : 0),
            'first_backup' => (! empty($data['first_backup']) ? $data['first_backup'] : ''),
            'last_backup' => (! empty($data['last_backup']) ? $data['last_backup'] : ''),
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
    
    /**
     * Sets the Team instance
     * @param Sites\Team $team
     * @return \Sites\Model\Sites
     */
    public function setTeam(Sites\Team $team)
    {
        $this->team = $team;
        return $this;
    }
    
    /**
     * Returns an instance of Team
     * @return Sites\Team
     */
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
    
    public function getSettingsInputFilter()
    {
        return new SettingsInputFilter();
    }
    
    /**
     * Returns all the sites a user can view
     *
     * @param int $user_id
     * @return array
     */
    public function getAllUserSites($user_id)
    {
        $sql = $this->db->select()->from(array('st' => 'site_teams'));
        $sql->join(array('s' => 'sites'), 'st.site_id = s.id');
        $sql = $sql->where(array(
            'user_id' => $user_id
        ));
    
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
        
        return $this->refreshSiteData($id, $data);
    }
    
    /**
     * Returns a site by its ID
     * @param int $id
     * @param \Application\Model\Hash $hash
     * @return array
     */
    public function getSiteByEndpointUrl($url, \Application\Model\Hash $hash = null) 
    {
        $sql = $this->db->select()->from(array('s' => 'sites'));
        $sql = $sql->where(array(
            'api_endpoint_url' => $url
        ));
        
        $data = $this->getRow($sql);
        if($data && !is_null($hash))
        {
            $data['api_secret'] = $hash->decrypt($data['api_secret']);
        }
        
        return $this->refreshSiteData($data['id'], $data);
    }
    
    /**
     * Will update the Site data from the API if within an hour of last refresh
     * @param int $id The site ID we're updating
     * @param array $data The site info we're connecting with
     * @return array The refreshed site data (if any)
     */
    protected function refreshSiteData($id, array $data, $force = false)
    {
        $date1 = new DateTime($data['last_modified']);
        $date2 = new DateTime(date('Y-m-d H:i:s'));
        
        $diff = $date2->diff($date1)->format("%a");
        if($diff >= 1 || $force) {
            $api_data = $this->getApi()->getSiteDetails($data['api_key'], $data['api_secret'], $data['api_endpoint_url']);
            if($api_data) {
                $data += $api_data;
                $this->updateSite($id, $data);
            }
        }
        
        return $data;
    }
    
    /**
     * Hits the API in realtime to get any issues with taking a backup
     * @param string $backup_type The type of backup we want to take
     * @param array $data The site connection details
     * @return array
     */
    public function getBackupPreventionErrors($backup_type, array $data) 
    {
        $api_data = $this->getApi()->getSiteDetails($data['api_key'], $data['api_secret'], $data['api_endpoint_url']);
        if($backup_type == 'database' && isset($api_data['backup_prevention_errors']['no_backup_file_location'])) {
            //we don't care about file backup issues on database backups
            unset($api_data['backup_prevention_errors']['no_backup_file_location']);
        }
        return ($api_data['backup_prevention_errors'] ? $api_data['backup_prevention_errors'] : array());
    }
    
    /**
     * Creates a Site
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
        $sql['owner_id'] = $data['owner_id'];
        $site_id = $data['site_id'] = $this->insert('sites', $sql);
        if ($site_id) {
            $this->team->addTeamMember($site_id, $data['owner_id']);
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
    public function updateSite($site_id, array $data, \Application\Model\Hash $hash = null)
    {
        $ext = $this->trigger(self::EventSiteUpdatePre, $this, compact('site_id', 'data', 'hash'), $this->setXhooks($data));
        if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $data = $ext->last();
        
        if(empty($data['site_name']))
        {
            $api_data = $this->getApi()->getSiteDetails($data['api_key'], $data['api_secret'], $data['api_endpoint_url']);
            if(is_array($api_data)) {
                $data += $api_data;
            }
        }
        
        $sql = $this->getSQL($data);
        if(!is_null($hash))
        {
            $sql['api_secret'] = $hash->encrypt($data['api_secret']);
        }
        else
        {
            unset($sql['api_secret']);
        }
        
        if ($this->update('sites', $sql, array('id' => $site_id))) {
    
            $ext = $this->trigger(self::EventSiteUpdatePost, $this, compact('site_id', 'data', 'hash'), $this->setXhooks($data));
            if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $site_id = $ext->last();
    
            return $site_id;
        }
    }
    
    /**
     * Removes a site entry
     * @param int $site_id
     * @return Ambigous <mixed, void>|unknown
     */
    public function removeSite($site_id)
    {
        $ext = $this->trigger(self::EventSiteRemovePre, $this, compact('site_id'), array());
        if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $site_id = $ext->last();
    
        if ($this->remove('sites', array('id' => $site_id))) {
            $this->remove('site_teams', array('site_id' => $site_id));
            $ext = $this->trigger(self::EventSiteRemovePost, $this, compact('site_id'), array());
            if ($ext->stopped()) return $ext->last(); elseif ($ext->last()) $site_id = $ext->last();
            
            return $site_id;
        }
    }   
    
    /**
     * Executes a backup
     * @param array $site_details
     * @param string $type
     * @return boolean
     */
    public function execBackup(array $site_details, $type = 'database')
    {
        if($this->getApi()->execBackup($site_details, $type))
        {
            unset($site_details['site_name']);
            $this->refreshSiteData($site_details['id'], $site_details, true);
            return true;
        }
    }
    
    public function updateSettings(array $site_details, array $data)
    {
        if($this->getApi()->updateSettings($site_details, $data))
        {
            $this->refreshSiteData($site_details['id'], $site_details, true);
            return true;
        }
    }
}