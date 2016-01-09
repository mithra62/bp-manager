<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Cron/Archive.php
 */
namespace HostManager\Cron;

use Base\Cron\BaseCron;

/**
 * HostManager - Task Archive Cron
 *
 * @package Cron
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/HostManager/src/HostManager/Cron/Archive.php
 */
class Archive extends BaseCron
{

    /**
     * When Crons should be sent
     * NOTE that only the hour parameter can be set for now
     * 
     * @todo allow for more abstraction on day of week at least
     * @var string
     */
    protected $expression_template = '@daily';

    /**
     * (non-PHPdoc)
     * 
     * @see \Base\Cron\BaseCron::shouldRun()
     */
    public function shouldRun()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Base\Cron\BaseCron::run()
     */
    public function run()
    {
        $this->console->writeLine('Starting Auto Archive...');
        
        $account = $this->getServiceLocator()->get('HostManager\Model\Accounts');
        $settings = $this->getServiceLocator()->get('Application\Model\Settings');
        $accounts = $account->getAccounts();
        foreach ($accounts as $site) {
            $this->console->writeLine('Processing Account: ' . $site['slug'] . '...');
            $site_settings = $settings->reset()->getSettings(array(
                'account_id' => $site['id']
            ));
            $last_ran = $site_settings['_task_auto_archive_last_ran'];
            if ($last_ran == '') {
                $this->console->writeLine('Skipping: ' . $site['slug'] . ' since never ran before...');
                $settings->updateSettings(array(
                    '_task_auto_archive_last_ran' => time()
                ), array(
                    'account_id' => $site['id']
                ));
                continue;
            }
            
            $this->setExpression($this->expression_template);
            $this->setLastRunDate($last_ran);
            if (! $this->isDue()) {
                $this->console->writeLine('Skipping ' . $site['slug'] . ' since last ran on ' . $this->getPreviousRunDate() . '...');
                $this->console->writeLine('Next run at: ' . $this->getNextRunDate());
                continue;
            }
            
            $date = mktime(0, 0, 0, date("m"), date("d") - $site_settings['task_auto_archive_days'], date("Y"));
            $date = date('Y-m-d H:i:s', $date);
            
            $sql = array(
                'status' => 6
            );
            $modified = $account->getDb()->select()->where->predicate->lessThan('last_modified', $date);
            $where = array(
                'account_id' => $site['id'],
                'status' => '5',
                $modified
            );
            
            $total_updated = $account->update('tasks', $sql, $where);
            
            $settings->updateSettings(array(
                '_task_auto_archive_last_ran' => time()
            ), array(
                'account_id' => $site['id']
            ));
            $this->console->writeLine('Finished ' . $site['slug'] . ' with ' . $total_updated . ' rows affected');
        }
    }
}