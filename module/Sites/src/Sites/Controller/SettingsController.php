<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Controller/SettingsController.php
 */
 
namespace Sites\Controller;

/**
 * Sites - Sites Settings Controller
 *
 * @package BackupProServer\Controller
 * @author	Eric Lamb <eric@mithra62.com>
 */
class SettingsController extends AbstractSitesController
{
    public function indexAction()
    {
        $section = $this->params()->fromRoute('section');
        $form = $this->getServiceLocator()->get('Sites\Form\SettingsForm');
        $options = $this->site->getApi()->getOptions($this->site_data);
        
        switch($section)
        {
            case 'cron':
            case 'db':
            case 'files':
            case 'license':
            case 'api':
            case 'integrity_agent':
                echo $this->render('form/_'.$section, $vars);
                break;
        
            default:
                $form = $form->getGeneralForm();
                break;
        }
        
        $form->setPlatformOptions($options)->setData($this->site_data['settings']);
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $formData = $request->getPost();
            $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
            $inputFilter = $site->getInputFilter($translate);
            $form->setInputFilter($inputFilter);
            $form->setData($request->getPost());
            if ($form->isValid($formData)) {
                $data = $formData->toArray();
                $data['owner_id'] = $this->getIdentity();
                $site_id = $id = $site->addSite($data, $hash);
                if ($site_id) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('site_added', 'sites'));
                    return $this->redirect()->toRoute('sites/view', array(
                        'site_id' => $id
                    ));
                } else {
                    $view['errors'] = array(
                        $this->translate('something_went_wrong', 'app')
                    );
                    $this->layout()->setVariable('errors', $view['errors']);
                }
            } else {
                $view['errors'] = array(
                    $this->translate('please_fix_the_errors_below', 'app')
                );
                $this->layout()->setVariable('errors', $view['errors']);
                $site_form->setData($formData);
            }
        }        
        
        
        $view = array();
        $view['form'] = $form;
        $view['settings'] = $this->site_data['settings'];
        $view['section'] = $section;
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}