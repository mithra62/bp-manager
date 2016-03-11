<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Cron/DailyReminder.php
 */
namespace HostManager\Cron;

use Base\Cron\BaseCron;

/**
 * HostManager - Daily Reminder Cron
 *
 * @package Cron
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/Cron/DailyReminder.php
 *            
 */
class DailyReminder extends BaseCron
{

    /**
     * When Crons should be sent
     * NOTE that only the hour parameter can be set for now
     * 
     * @todo allow for more abstraction on day of week at least
     * @var string
     */
    protected $expression_template = '0 %s * * 1-5';

    /**
     * Contains the Account details
     * 
     * @var array
     */
    private $accounts = array();

    /**
     * Determines whether we should even do anything
     * 
     * @see \Base\Cron\BaseCron::shouldRun()
     */
    public function shouldRun()
    {
        // we always come back true because this is a global cron based on individual users
        // we'll have to verify manually here
        return true;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Base\Cron\BaseCron::run()
     */
    public function run()
    {
        $user = $this->getServiceLocator()->get('HostManager\Model\Users');
        $task = $this->getServiceLocator()->get('PM\Model\Tasks');
        $account = $this->getServiceLocator()->get('HostManager\Model\Accounts');
        
        $users = $user->getAllUsers();
        foreach ($users as $member) {
            // check if we should send based on user pref
            if ($user->checkPreference($member['id'], 'noti_daily_task_reminder', '1') == '0') {
                $this->console->writeLine('Skipping ' . $member['email'] . ' for preference reasons...');
                continue;
            }
            
            // now should we send based on last send?
            $last_ran = $user->checkPreference($member['id'], '_daily_reminder_schedule_last_sent');
            $user_data = $user->user_data->reset()->getUsersData($member['id']);
            $user->setTimezone($user_data['timezone']);
            if (! $last_ran) {
                // never been ran before so we set now and wait for next iteration to process
                $user->user_data->updateUserDataEntry('_daily_reminder_schedule_last_sent', date('U'), $member['id']);
                $this->console->writeLine('Skipping ' . $member['email'] . ' hasn\'t been processed yet...');
                continue;
            }
            
            $schedule = sprintf($this->expression_template, trim($user->checkPreference($member['id'], 'daily_reminder_schedule', '11')));
            $this->setExpression($schedule);
            $this->setLastRunDate($last_ran);
            if (! $this->isDue()) {
                $this->console->writeLine('Skipping ' . $member['email'] . ' since last ran on ' . $this->getPreviousRunDate() . '...');
                $this->console->writeLine('Next run at: ' . $this->getNextRunDate());
                continue;
            }
            
            // now grab tasks and see if we have content yet
            $user_tasks = $user->getAssignedTasks($member['id'], $user->checkPreference($member['id'], 'task_reminder_upcoming_days', '30'));
            if (! $user_tasks) {
                $this->console->writeLine('Skipping ' . $member['email'] . ' since there aren\'t any tasks to remind for...');
                continue;
            }
            
            // ok, we have tasks that are worth notifying about
            // now to prep and send
            $mail = $this->getServiceLocator()->get('Application\Model\Mail');
            $mail->setTranslationDomain('pm');
            $this->email_view_path = $mail->getModulePath(__DIR__) . '/view/emails';
            $mail->setTo($member['email'], $member['first_name'] . ' ' . $member['last_name']);
            $mail->setViewDir($this->email_view_path);
            $mail->setEmailView('task-reminder', array(
                'user_data' => $member,
                'tasks' => $user_tasks
            ));
            $mail->setSubject('daily_task_reminder_email_subject');
            $mail->send();
            
            $user->user_data->updateUserDataEntry('_daily_reminder_schedule_last_sent', date('U'), $member['id']);
            $this->console->writeLine('Sent for ' . $member['email']);
        }
    }
}