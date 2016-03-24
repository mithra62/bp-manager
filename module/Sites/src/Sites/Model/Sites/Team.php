<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/Model/Sites/Team.php
 */
namespace Sites\Model\Sites;

use Application\Model\AbstractModel;

/**
 * Sites - Team Model
 *
 * @package mithra62\BackupPro
 * @author Eric Lamb
 */
class Team extends AbstractModel
{
    /**
     * An array collection of site ids used for processing
     * @var array
     */
    protected $site_teams = array();
    
    /**
     * Adds a site team member
     * @param int $site_id
     * @param int $user_id
     * @return \Base\Model\Ambigous
     */
    public function addTeamMember($site_id, $user_id)
    {
        $data = array(
            'site_id' => $site_id, 
            'user_id' => $user_id,
            'last_modified' => new \Zend\Db\Sql\Expression('NOW()'),
            'created_date' => new \Zend\Db\Sql\Expression('NOW()')
        );
        
        return $this->insert('site_teams', $data);
    }
    
    /**
     * Checks whether a user is on a given site's team
     * @param int $identiy
     * @param int $site_id
     * @return boolean
     */
    public function userOnTeam($identiy, $site_id)
    {
        if(!isset($this->site_teams[$site_id]) || !is_array($this->site_teams[$site_id])) {
            $sql = $this->db->select()->from(array('st' => 'site_teams'));
            $sql = $sql->where(array(
                'site_id' => $site_id
            ));
            
            $team_data = $this->getRows($sql);   
            if($team_data) 
            {
                foreach($team_data As $key => $value) 
                {
                    $this->site_teams[$site_id][] = $value['user_id'];
                }
            }
        }
        
        return (in_array($identiy, $this->site_teams[$site_id]));
    }
}