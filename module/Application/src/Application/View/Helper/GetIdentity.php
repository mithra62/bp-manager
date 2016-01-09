<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/View/Helper/GetIdentity.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * View Helper - Get Identity
 *
 * @package ViewHelpers
 * @author Eric Lamb
 * @filesource ./module/Application/src/Application/View/Helper/GetIdentity.php
 */
class GetIdentity extends BaseViewHelper
{

    public function __invoke()
    {
        return $this->getIdentity();
    }
}