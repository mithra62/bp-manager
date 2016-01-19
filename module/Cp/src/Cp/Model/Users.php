<?php
namespace Cp\Model;

use Application\Model\Users As AppUsers;

class Users extends AppUsers
{
    public function addCpUser(array $data, \Application\Model\Hash $hash, \Application\Model\Mail $mail)
    {
        $user_id = $this->addUser($data, $hash);
        if( !empty($data['auto_verify']) && $data['auto_verify'] === '1' )
        {
            $this->update('users', $this->getVerifySql(), array('id' => $user_id));
        } 
        elseif( !empty($data['send_verification_email']) && $data['send_verification_email'] === '1' )
        {
            $this->sendVerifyEmail($user_id, $mail, $hash);
        }
        
        return $user_id;
    }
}