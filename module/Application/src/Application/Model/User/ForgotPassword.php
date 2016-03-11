<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/ForgotPassword.php
 */
namespace Application\Model\User;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

/**
 * Forgot Password Model
 *
 * @package Users\Login\ForgotPassword
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/ForgotPassword.php
 *            
 */
class ForgotPassword extends AbstractModel
{

    protected $inputFilter;

    /**
     * Sets everything up
     * 
     * @param \Zend\Db\Adapter\Adapter $db            
     * @param \Zend\Db\Sql\Sql $sql            
     * @param \Application\Model\Users $users            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $db, \Zend\Db\Sql\Sql $sql, \Application\Model\Users $users = null)
    {
        parent::__construct($db, $sql);
        $this->users = $users;
    }

    /**
     * Sets the Input Filter to use
     * 
     * @param InputFilterInterface $inputFilter            
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Returns the InputFilter to use for validating the Forgot Password data
     * 
     * @uses \Zend\InputFilter\Factory::createInput()
     * @uses \Zend\InputFilter\InputFilter::add()
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter($translator)
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    
                    array(
                        'name' =>'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'isEmpty' => $translator('email_required', 'app')
                            ),
                        ),
                    ),  
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                'emailAddressInvalidFormat' => $translator('invalid_email_address', 'app')
                            ),
                        ),                        
                    ),
                    array(
                        'name' => 'Db\RecordExists',
                        'options' => array(
                            'table' => 'users',
                            'field' => 'email',
                            'adapter' => $this->adapter,
                            'messages' => array(
                                'noRecordFound' => $translator('enter_registered_email', 'app')
                            )                            
                        )
                    )
                )
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    /**
     * Sends the Forgot Password email
     * 
     * @param \Application\Model\Mail $mail            
     * @param \Application\Model\Hash $hash            
     * @param string $email_address'            
     * @uses \Application\Model\Users::getUserByEmail()
     * @uses \Application\Model\Users::upatePasswordHash()
     * @uses \Application\Model\Mail
     * @return boolean
     */
    public function sendEmail(\Application\Model\Mail $mail, \Application\Model\Hash $hash, $email_address)
    {
        $guid = $hash->guidish();
        $user_data = $this->users->getUserByEmail($email_address);
        if (! $user_data) {
            return FALSE;
        }
        
        if ($this->users->upatePasswordHash($user_data['id'], $guid)) {
            $change_url = $mail->web_url . $this->changePasswordUrl($guid);
            $mail->addTo($email_address);
            $mail->setViewDir($this->getModulePath(__DIR__) . '/../view/emails');
            $mail->setEmailView('forgot-password', array(
                'change_url' => $change_url,
                'user_data' => $user_data
            ));
            $mail->addTo($user_data['email']);
            $mail->setSubject('forgot_password_email_subject');
            return $mail->send($mail->transport);
        }
    }

    public function changePasswordUrl($guid)
    {
        return '/forgot-password/reset/' . $guid;
    }
}