<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link			http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/AbstractRestfulJsonController.php
 */
namespace Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\EventManager\EventManagerInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use Hal\Resource;
use Hal\Link;
use Base\Traits\Controller as BaseControllerTrait;

/**
 * Api - Abstract Controller
 *
 * Sets all the global functionality up for the REST API
 *
 * @package BackupProServer\Controller
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Controller/AbstractRestfulJsonController.php
 */
class AbstractRestfulJsonController extends AbstractRestfulController
{
    /**
     * Setup the Traits we're using
     */
    use BaseControllerTrait;

    /**
     * Session
     * 
     * @var object
     */
    protected $session;

    /**
     * Permission Object
     * 
     * @var object
     */
    protected $perm;

    /**
     * Settings array
     * 
     * @var array
     */
    protected $settings;

    /**
     * Preferences array
     * 
     * @var array
     */
    protected $prefs;

    /**
     * The API Controllers that don't require authentication
     * 
     * @var array
     */
    protected $whitelist = array(
        'Api\Controller\Login'
    );

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->identity = $this->getServiceLocator()
            ->get('AuthService')
            ->getIdentity();
        $controller = $this->getEvent()
            ->getRouteMatch()
            ->getParam('controller', FALSE);
        if (empty($this->identity) && ! in_array($controller, $this->whitelist)) {
            return $this->setError(401, 'Authorization Required!');
        }
        
        $settings = $this->getServiceLocator()->get('Application\Model\Settings');
        $this->settings = $settings->getSettings();
        
        $this->getServiceLocator()->get('Timezone');
        
        $this->_initPrefs();
        $this->perm = $this->getServiceLocator()->get('Application\Model\Permissions');
        
        $translator = $e->getApplication()
            ->getServiceManager()
            ->get('translator');
        $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))->setFallbackLocale('en_US');
        
        if (! $this->_initIpBlocker()) {
            return $this->setError(401, 'Unallowed IP Address!');
        }
        
        return parent::onDispatch($e);
    }

    /**
     * Wrapper to handle error output
     *
     * Note that $detail should be a key for language translation
     *
     * @param int $code            
     * @param string $detail            
     * @param string $type            
     * @param string $title            
     * @param array $additional            
     * @return \ZF\ApiProblem\ApiProblemResponse
     */
    public function setError($code, $detail, $type = null, $title = null, array $additional = array())
    {
        return new ApiProblemResponse(new ApiProblem($code, $this->translate($detail, 'api'), $type, $title, $additional));
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractController::setEventManager()
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $this->events = $events;
        $events->attach('dispatch', array(
            $this,
            'checkOptions'
        ), 10);
    }

    /**
     * Provides oversight on permission dependant requsts
     * 
     * @param string $permission            
     * @param string $url            
     */
    public function check_permission($permission, $url = FALSE)
    {
        $this->identity = $this->getServiceLocator()
            ->get('AuthService')
            ->getIdentity();
        if (empty($this->identity)) {
            return FALSE;
        }
        
        if (! $this->perm->check($this->identity, $permission)) {
            return FALSE;
        }
        
        return TRUE;
    }

    /**
     * Start up the IP Blocker
     */
    protected function _initIpBlocker()
    {
        if (! empty($this->settings['enable_ip']) && $this->settings['enable_ip'] == '1') {
            $ip = $this->getServiceLocator()->get('PM\Model\Ips');
            if (! $ip->isAllowed($_SERVER['REMOTE_ADDR'])) {
                return FALSE;
            }
        }
        
        return TRUE;
    }

    /**
     * Creates the HAL Collection object for output
     *
     * $api_route must match a route from the API module.config file
     * $pm_route must match a route from the PM module.config file
     *
     * @param array $data            
     * @param string $api_route            
     * @param string $pm_route            
     * @param string $pm_route_pk            
     * @param string $embed_node_name            
     * @return array
     */
    protected function setupHalCollection(array $data, $api_route, $embed_node_name = 'items', $pm_route = FALSE, $pm_route_pk = 'task_id')
    {
        if (! empty($data['data'])) {
            $url = $this->getRequest()->getRequestUri();
            $total_pages = ceil($data['total_results'] / $data['limit']);
            $parent_data = array(
                'count' => $data['total'],
                'total_results' => $data['total_results'],
                'page' => $data['page'],
                'total_pages' => $total_pages,
                'limit' => (int) $data['limit']
            );
            $parent = new Resource($url, $parent_data);
            
            $url_parts = parse_url($url);
            if ($url_parts['query'] && $total_pages > 1) {
                parse_str($url_parts['query'], $get_array);
                
                $origial_get_array = $get_array;
                $get_array['page'] = 1;
                $parent->setLink(new Link($url_parts['path'] . '?' . http_build_query($get_array), 'first'));
                
                if ($data['page'] < $total_pages) {
                    $get_array['page'] = $origial_get_array['page'] + 1;
                    $parent->setLink(new Link($url_parts['path'] . '?' . http_build_query($get_array), 'next'));
                }
                
                if (($origial_get_array['page'] - 1) != 0) {
                    $get_array['page'] = $origial_get_array['page'] - 1;
                    $parent->setLink(new Link($url_parts['path'] . '?' . http_build_query($get_array), 'prev'));
                }
                
                if ($total_pages != $data['page']) {
                    $get_array['page'] = $total_pages;
                    $parent->setLink(new Link($url_parts['path'] . '?' . http_build_query($get_array), 'last'));
                }
            }
            
            foreach ($data['data'] as $key => $value) {
                $api_url = $this->url()->fromRoute($api_route, array(
                    'id' => $value['id']
                ));
                $item = new Resource($api_url);
                $item->setData($value);
                
                if ($pm_route) {
                    $pm_url = $this->url()->fromRoute($pm_route, array(
                        $pm_route_pk => $value['id']
                    ));
                    $item->setLink(new Link($pm_url, 'pm'));
                }
                
                $parent->setEmbedded($embed_node_name, $item);
            }
            return $parent->toArray();
        }
    }

    /**
     * Creates the HAL Resource for output
     *
     * @param array $data            
     * @param string $api_route            
     * @param array $_embedded            
     * @param string $pm_route            
     * @param string $pm_route_pk            
     * @return Ambigous <multitype:, multitype:unknown , void, multitype:multitype: >
     */
    protected function setupHalResource(array $data, $api_route, array $_embedded = array(), $pm_route = FALSE, $pm_route_pk = 'task_id')
    {
        $url = $this->getRequest()->getRequestUri();
        $parent = new Resource($url, $data);
        if ($pm_route) {
            $pm_url = $this->url()->fromRoute($pm_route, array(
                $pm_route_pk => $data['id']
            ));
            $parent->setLink(new Link($pm_url, 'pm'));
        }
        
        foreach ($_embedded as $key => $value) {
            if (! empty($value['link_meta'])) {
                foreach ($value['data'] as $k => $v) {
                    $url = $this->url()->fromRoute($value['link_meta']['route'], array(
                        'id' => $v[$value['link_meta']['pm_route_pk']]
                    ));
                    $item = new Resource($url);
                    $item->setData($v);
                    if ($value['link_meta']['pm_route']) {
                        $pm_url = $this->url()->fromRoute($value['link_meta']['pm_route'], array(
                            $value['link_meta']['pm_route_pk'] => $v[$value['link_meta']['pm_route_pk']]
                        ));
                        $item->setLink(new Link($pm_url, 'pm'));
                    }
                    $parent->setEmbedded($key, $item);
                }
            }
        }
        
        return $parent->toArray();
    }

    /**
     * Cleans up single resources to remove unwanted fields/keys
     * 
     * @param array $data            
     * @param array $map            
     * @return multitype:array
     */
    public function cleanResourceOutput(array $data, array $map)
    {
        // first, tidy things up so we're not just dumping db results
        $return = array();
        foreach ($data as $key => $value) {
            foreach ($map as $k => $v) {
                if ($key == $k) {
                    $return[$v] = $value;
                }
            }
        }
        
        return $return;
    }

    /**
     * Cleans up collections to remove unwanted fields/keys
     * 
     * @param array $data            
     * @param array $map            
     * @return multitype:array
     */
    public function cleanCollectionOutput(array $data, array $map)
    {
        // first, tidy things up so we're not just dumping db results
        $return = array();
        foreach ($data as $key => $value) {
            $return[$key] = array();
            foreach ($value as $k => $v) {
                foreach ($map as $map_key => $map_value) {
                    if ($k == $map_key) {
                        $return[$key][$map_value] = $v;
                    }
                }
            }
        }
        
        return $return;
    }

    /**
     * Creates the meta array for the _embed nodes of the HAL object
     * 
     * @param array $meta            
     * @param string $api_route            
     * @param string $pm_route            
     * @param string $pm_route_pk            
     * @return multitype:unknown multitype:string
     */
    public function setupCollectionMeta(array $meta, $api_route = 'api-users', $pm_route = 'users/view', $pm_route_pk = 'user_id')
    {
        $return = array(
            'data' => $meta,
            'link_meta' => array(
                'route' => $api_route,
                'pm_route' => $pm_route,
                'pm_route_pk' => $pm_route_pk
            )
        );
        
        return $return;
    }

    /**
     * Event to handle OPTION requests
     * 
     * @param \Zend\Mvc\MvcEvent $e            
     * @return void|\Zend\Stdlib\ResponseInterface
     */
    public function checkOptions(\Zend\Mvc\MvcEvent $e)
    {
        if ($this->params()->fromRoute('id', false)) {
            $options = $this->resourceOptions;
        } else {
            $options = $this->collectionOptions;
        }
        
        if (in_array($e->getRequest()->getMethod(), $options)) {
            return;
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(405);
        return $response;
    }

    /**
     * Sets the HTTP header code that's passed
     * 
     * @param int $code            
     */
    public function setStatusCode($code)
    {
        $response = $this->getResponse();
        $response->setStatusCode($code);
    }

    /**
     * Handy little method to disable unused HTTP verb methods
     * 
     * @return \ZF\ApiProblem\ApiProblemResponse
     */
    protected function methodNotAllowed()
    {
        return $this->setError(405, 'method_not_allowed');
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::options()
     */
    public function options()
    {
        if ($this->params()->fromRoute('id', false)) {
            $options = $this->resourceOptions;
        } else {
            $options = $this->collectionOptions;
        }
        
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Allow', implode(',', $options));
        return $response;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::create()
     */
    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::delete()
     */
    public function delete($id)
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::deleteList()
     */
    public function deleteList()
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::get()
     */
    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::getList()
     */
    public function getList()
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::head()
     */
    public function head($id = null)
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::patch()
     */
    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::replaceList()
     */
    public function replaceList($data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::patchList()
     */
    public function patchList($data)
    {
        return $this->methodNotAllowed();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Zend\Mvc\Controller\AbstractRestfulController::update()
     */
    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }
}