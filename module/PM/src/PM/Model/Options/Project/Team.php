<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Project/Team.php
 */

namespace PM\Model\Options\Project;

use PM\Model\Options\AbstractOptions;

/**
 * PM - Project Team Options Model
 *
 * @package 	Projects\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Project/Team.php
 */
class Team extends AbstractOptions
{	
	/**
	 * Creates an array of a Project team
	 * @param \PM\Model\Projects $project
	 * @param int $project_id
	 * @param string $blank
	 * @return multitype:string
	 */
	static public function team(\PM\Model\Projects $project, $project_id, $blank = FALSE)
	{
		$arr = $project->getProjectTeamMembers($project_id);
		$_new = array();
		if($blank)
		{
			$_new['0'] = 'Unassigned';
		}
		foreach($arr AS $user)
		{
			$_new[$user['user_id']] = $user['first_name'].' '.$user['last_name'];
		}
		
		return $_new;
	}
}