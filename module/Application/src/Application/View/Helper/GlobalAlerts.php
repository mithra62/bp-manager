<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/View/Helper/CheckPermission.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Check Permission View Helper
 *
 * @package ViewHelpers\Users
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/View/Helper/CheckPermission.php
 */
class GlobalAlerts extends BaseViewHelper
{
    protected $check_email_verify = true;
    
    public function __invoke()
    {
        return $this;
    }
    
    /**
     * Outputs the global alert       
     */
    public function compile()
    {
        $flash = $this->getView()->flashMessenger();
        $flash->setMessageOpenFormat('<div%s>
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                 &times;
             </button>
             <ul><li>')
                     ->setMessageSeparatorString('</li><li>')
                     ->setMessageCloseString('</li></ul></div>');
        
        $return = '';
        $return .= $flash->render('error', array('alert', 'alert-dismissible', 'alert-danger'));
        $return .= $flash->render('info', array('alert', 'alert-dismissible', 'alert-info'));
        $return .= $flash->render('default', array('alert', 'alert-dismissible', 'alert-warning'));
        $return .= $flash->render('success', array('alert', 'alert-dismissible', 'alert-success'));
        
        if( $this->getIdentity() )
        {
            $user_data = $this->getView()->UserInfo($this->getIdentity());
            if($user_data['verified'] == '0' && $this->getCheckEmailVerify())
            {
                $url = $this->getView()->url('account/verify_email');
                $message = sprintf($this->getView()->_('verify_email_required_html', 'app'), $user_data['email'], $url);
                $return .=$this->getView()->alert($message, array('class' => 'alert-warning'), false);
            }

        }
        
        return $return;
        
    }
    
    public function setCheckEmailVerify($check)
    {
        $this->check_email_verify = $check;
        return $this;
    }
    
    public function getCheckEmailVerify()
    {
        return $this->check_email_verify;
    }
    
}