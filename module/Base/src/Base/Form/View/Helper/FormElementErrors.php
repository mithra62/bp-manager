<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Base/src/Base/Form/View/Helper/FormElementErrors.php
 */
namespace Base\Form\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as OriginalFormElementErrors;

/**
 * Form Element Errors - View Helper
 *
 * Sets the styling to use for ZF form errors
 *
 * @package BackupProServer\Form\ElementErrors
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Base/src/Base/Form/View/Helper/FormElementErrors.php
 */
class FormElementErrors extends OriginalFormElementErrors
{

    protected $messageCloseString = '</li></ul>';

    protected $messageOpenFormat = '<ul%s class="errors"><li>';

    protected $messageSeparatorString = '</li><li class="error">';
}