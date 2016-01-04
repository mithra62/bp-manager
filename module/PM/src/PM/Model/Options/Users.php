<?php
class PM_Model_Options_Users
{	
	static public function users($blank = FALSE)
	{
		$users = new Model_Users;
		$arr = $users->getSelectOptions();
		$_new = array();
		if($blank)
		{
			$_new['0'] = '';
		}
		foreach($arr AS $user)
		{
			$_new[$user['id']] = $user['first_name'].' '.$user['last_name'];
		}
		
		return $_new;
	}
}