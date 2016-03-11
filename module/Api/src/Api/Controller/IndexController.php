<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link			http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Controller/IndexController.php
 */
namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

/**
 * Api - Index Controller
 *
 * General API Interaction Controller
 *
 * @package mithra62:Mojitrac
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Api/src/Api/Controller/IndexController.php
 */
class IndexController extends AbstractRestfulJsonController
{

    /**
     * Maps the available HTTP verbs we support for groups of data
     * 
     * @var array
     */
    protected $collectionOptions = array(
        'GET',
        'OPTIONS'
    );

    /**
     * Maps the available HTTP verbs for single items
     * 
     * @var array
     */
    protected $resourceOptions = array(
        'GET',
        'OPTIONS'
    );

    public function indexAction()
    {
        return new JsonModel(array(
            'data' => "The MojiTrac API is alive."
        ));
    }

    /**
     * Creates a JSON array to feed to drop down fields
     * 
     * @return \Zend\View\Model\JsonModel
     */
    public function chainProjectsAction()
    {
        $company_id = $this->getRequest()->getQuery('_value', false);
        if ($company_id == 0) {
            return $this->setError(422, 'Bad, or no, company_id parameter');
        }
        
        if ($this->perm->check($this->identity, 'manage_projects') && $this->perm->check($this->identity, 'view_companies')) {
            $project = $this->getServiceLocator()->get('PM\Model\Projects');
            $projects = $project->getProjectOptions($company_id);
        } else {
            $user = $this->getServiceLocator()->get('PM\Model\Users');
            $projects = $user->getAssignedProjects($this->identity);
            $arr = array();
            $count = 0;
            foreach ($projects as $project) {
                if ($project['company_id'] == $company_id) {
                    $arr[$count]['id'] = $project['id'];
                    $arr[$count]['name'] = $project['name'];
                    $count ++;
                }
            }
            $projects = $arr;
        }
        
        $arr = array();
        $arr[] = array(
            'none' => ''
        );
        foreach ($projects as $project) {
            $arr[] = array(
                $project['id'] => $project['name']
            );
        }
        
        return new JsonModel($arr);
    }

    /**
     * Creates a JSON array to feed to drop down fields
     * 
     * @return \Zend\View\Model\JsonModel
     */
    public function chainTasksAction()
    {
        $project_id = (int) $this->getRequest()->getQuery('_value', false);
        if ($project_id == 0) {
            return $this->setError(422, 'Bad, or no, project_id parameter');
        }
        
        if ($this->perm->check($this->identity, 'manage_tasks')) {
            $task = $this->getServiceLocator()->get('PM\Model\Tasks');
            $tasks = $task->getTaskOptions($project_id);
        } else {
            $user = $this->getServiceLocator()->get('PM\Model\Users');
            $tasks = $user->getOpenAssignedTasks($this->identity, $project_id);
        }
        
        $arr = array();
        $arr[] = array(
            'none' => ''
        );
        foreach ($tasks as $task) {
            $arr[] = array(
                $task['id'] => $task['name']
            );
        }
        
        return new JsonModel($arr);
    }
}
