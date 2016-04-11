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
        $view = $options = $this->site->getApi()->getOptions($this->site_data);
        $view['form_errors'] = $this->returnEmpty($this->site_data['settings']);
        
        $form->setPlatformOptions($options);
        switch($section)
        {
            case 'cron':
                $form = $form->getCronForm();
                break;
            case 'db':
                $form = $form->getDbForm();
                break;
            case 'files':
                $form = $form->getFilesForm();
                break;
            case 'license':
                $form = $form->getGeneralForm();
                break;
            case 'api':
                $form = $form->getGeneralForm();
                break;
            case 'integrity_agent':
                $form = $form->getIntegrityForm();
                break;
        
            default:
                $form = $form->getGeneralForm();
                break;
        }
        
        $default_data = $this->site_data['settings'];
        $default_data['cron_notify_emails'] = implode("\n", $this->site_data['settings']['cron_notify_emails']);
        $default_data['exclude_paths'] = implode("\n", $this->site_data['settings']['exclude_paths']);
        $default_data['backup_file_location'] = implode("\n", $this->site_data['settings']['backup_file_location']);
        $default_data['db_backup_archive_pre_sql'] = implode("\n", $this->site_data['settings']['db_backup_archive_pre_sql']);
        $default_data['db_backup_archive_post_sql'] = implode("\n", $this->site_data['settings']['db_backup_archive_post_sql']);
        $default_data['db_backup_execute_pre_sql'] = implode("\n", $this->site_data['settings']['db_backup_execute_pre_sql']);
        $default_data['db_backup_execute_post_sql'] = implode("\n", $this->site_data['settings']['db_backup_execute_post_sql']);
        $default_data['backup_missed_schedule_notify_emails'] = implode("\n", $this->site_data['settings']['backup_missed_schedule_notify_emails']);
        
        $form->setData($default_data);
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $form_data = $request->getPost();
            $form->setData($form_data);
            $validate = $this->site->getApi()->validateSettings($this->site_data, $form_data->toArray());
            
            if ($validate['total_failures'] == '0') {
                if ($this->site->updateSettings($this->site_data, $form_data->toArray())) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('settings_updated', 'sites'));
                    return $this->redirect()->toRoute('site_settings', array(
                        'site_id' => $this->site_id,
                        'section' => $section
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
                
                $view['form_errors'] = array_merge($view['form_errors'], $validate['failures']);
            }
        } 
        
        $view['form'] = $form;
        $view['settings'] = $this->site_data['settings'];
        $view['section'] = $section;
        $view['active_sidebar'] = 'site_nav_'.$this->site_id;
        $this->layout()->setVariable('active_sidebar', $view['active_sidebar']);
        return $view;
    }
}