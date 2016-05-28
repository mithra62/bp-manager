<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Mail.php
 */
namespace Application\Model;

use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplatePathStack;

/**
 * Mail Model
 *
 * @package Mail
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/src/Application/Model/Mail.php
 */
class Mail extends AbstractModel
{

    /**
     * The email method
     * 
     * @var object
     */
    public $sending_transport = null;

    /**
     * The message object
     * 
     * @var \Zend\Mail\Message
     */
    public $message = null;

    /**
     * The sendmail type transport
     * 
     * @var \Zend\Mail\Transport\Smtp
     */
    public $sendmail_transport = null;

    /**
     * The file type transport (mostly used for logging emails sent)
     * 
     * @var \Zend\Mail\Transport\File
     */
    public $file_transport = null;

    /**
     * The SMTP type transport
     * 
     * @var \Zend\Mail\Transport\Smtp
     */
    public $smtp_transport = null;

    /**
     * The View model
     * 
     * @var \Zend\View\Model\ViewModel
     */
    public $view_model = null;

    /**
     * Used in conjunction with $view_model to render emails
     * 
     * @var Object
     */
    private $renderer = null;

    /**
     * The URL to reference the main site
     * 
     * @var mixed
     */
    public $web_url = FALSE;

    /**
     * The localization domain
     * 
     * @var string
     */
    private $translation_domain = 'app';

    /**
     * Mail Model
     * 
     * @param \Zend\Db\Adapter\Adapter $adapter            
     * @param \Zend\Db\Sql\Sql $sql            
     * @param \Zend\Mail\Message $message            
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db, \Zend\Mail\Message $message, array $settings)
    {
        parent::__construct($adapter, $db);
        
        $this->message = $message;
        $this->message->setEncoding("UTF-8");
        $this->message->getHeaders()->addHeaderLine('X-MailGenerator', $settings['site_name']);
        // $this->message->getHeaders()->addHeaderLine('content-type', 'multipart/alternative'); //so we can send both HTML and txt emails
        // $this
        $this->message->addReplyTo($settings['mail_reply_to_email'], $settings['mail_reply_to_name']);
        $this->message->setSender($settings['mail_sender_email'], $settings['mail_sender_name']);
        $this->message->setFrom($settings['mail_from_email'], $settings['mail_from_name']);
        $this->web_url = (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : false);
    }

    /**
     * Sets the Sendmail Tranport object
     * 
     * @param \Zend\Mail\Transport\Sendmail $sendmail            
     * @return \Application\Model\Mail
     */
    public function setSendmailTransport(\Zend\Mail\Transport\Sendmail $sendmail)
    {
        $this->sendmail_transport = $sendmail;
        return $this;
    }

    /**
     * Sets the File Tranport object
     * 
     * @param \Zend\Mail\Transport\File $file            
     * @return \Application\Model\Mail
     */
    public function setFileTransport(\Zend\Mail\Transport\File $file)
    {
        $this->file_transport = $file;
        return $this;
    }

    /**
     * Sets the SMTP Tranport object
     * 
     * @param \Zend\Mail\Transport\Smtp $smtp            
     * @return \Application\Model\Mail
     */
    public function setSmtpTransport(\Zend\Mail\Transport\Smtp $smtp)
    {
        $this->smtp_transport = $smtp;
        return $this;
    }

    /**
     * Sets the Mail configuration array
     * 
     * @param
     *            $config
     * @return \Application\Model\Mail
     */
    public function setMailConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Abstracts out the email body from a view script
     * 
     * @param
     *            template
     * @param
     *            $variables
     * @return \Application\Model\Mail
     */
    public function setEmailView($template, $variables)
    {
        $variables = array_merge($variables, array(
            'site_url' => $this->web_url
        ));
        $this->getViewModel()
            ->setTerminal(true)
            ->setTemplate($template)
            ->setVariables($variables);
        $this->setBody($this->renderer->render($this->getViewModel()));
        return $this;
    }

    /**
     * Sets the path to the email templates
     * 
     * @param string $path            
     * @return \Application\Model\Mail
     */
    public function setViewDir($path)
    {
        $this->view_dir = $path;
        return $this;
    }

    /**
     * Sets the View Helpers for use in the email view scripts
     * 
     * @param object $helpers            
     */
    public function setViewHelpers(\Zend\View\HelperPluginManager $helpers)
    {
        $this->view_helpers = $helpers;
        return $this;
    }

    /**
     * Sets the Translator object up
     * 
     * @param \Zend\Mvc\I18n\Translator $translator            
     * @return \Application\Model\Mail
     */
    public function setTranslator(\Zend\Mvc\I18n\Translator $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Sets the translation (module) domain to use
     * 
     * @param string $domain            
     * @return \Application\Model\Mail
     */
    public function setTranslationDomain($domain = 'app')
    {
        $this->translation_domain = $domain;
        return $this;
    }

    /**
     * Returns an instance of the View Model, creating one if it doens't exist
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function getViewModel()
    {
        if (! $this->view_model) {
            $this->renderer = new PhpRenderer();
            $this->renderer->setHelperPluginManager($this->view_helpers);
            
            $resolver = new TemplatePathStack();
            $resolver->setPaths(array(
                $this->view_dir
            ));
            $this->renderer->setResolver($resolver);
            $this->view_model = new ViewModel();
            $this->view_model->setTerminal(TRUE);
        }
        
        return $this->view_model;
    }

    /**
     * Appends (or creates) an emai address to a list to send to
     * 
     * @param string $email_address            
     * @param string $name            
     * @return \Application\Model\Mail
     */
    public function addTo($email_address, $name = null)
    {
        $this->message->addTo($email_address, $name);
        return $this;
    }

    /**
     * Creates a new email collection to send to
     * 
     * @param unknown $email_address            
     * @param string $name            
     * @return \Application\Model\Mail
     */
    public function setTo($email_address, $name = null)
    {
        $this->message->setTo($email_address, $name);
        return $this;
    }

    /**
     * Sets the email body explicitly based on the passed string
     * 
     * @param string $message            
     * @return \Application\Model\Mail
     */
    public function setBody($message)
    {
        $text = new MimePart(strip_tags($message));
        $text->type = "text/plain";
        
        $html = new MimePart($message);
        $html->type = "text/html";
        
        $body_html = new MimeMessage();
        $body_html->setParts(array(
            $html,
            $text
        ));
        
        $this->message->setBody($body_html);
        return $this;
    }

    /**
     * Sets the email subject and translates against the view model
     * 
     * @todo hook the subject up for translation
     *      
     * @param string $subject            
     * @return \Application\Model\Mail
     */
    public function setSubject($subject)
    {
        $subject = $this->translator->translate($subject, $this->translation_domain);
        $this->message->setSubject($subject);
        return $this;
    }

    /**
     * Sends the email
     * 
     * @return boolean
     */
    public function send()
    {
        // if($this->isValid())
        {
            // first log it!
            $this->file_transport->send($this->message);
            $transport = $this->getTransport();
            $transport->send($this->message);
            
            return true;
        }
    }

    /**
     * Verifies we have everything we need to send an email
     * 
     * @return bool
     */
    public function isValid()
    {
        if ($this->message->isValid()) {
            return TRUE;
        }
    }

    /**
     * Determines how to send the email using which method (php or SMTP)
     * 
     * @return object
     */
    private function getTransport()
    {
        // first, we use the value from the config
        $type = (empty($this->config['email']['type']) ? $this->config['email']['type'] : 'php');
        
        // next, we have to validate we have the proper options for the setting
        if (! empty($this->config['email']['smtp_options']) && $this->smtp_transport !== null) {
            $this->smtp_transport->setOptions(new SmtpOptions($this->config['email']['smtp_options']));
        }
        
        return $this->smtp_transport;
    }
}