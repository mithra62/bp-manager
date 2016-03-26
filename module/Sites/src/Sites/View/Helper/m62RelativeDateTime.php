<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/View/Helper/m62DateTime.php
 */
namespace Sites\View\Helper;

use Base\View\Helper\BaseViewHelper;
use RelativeTime\RelativeTime;
use DateTime, IntlDateFormatter, DateInterval;

/**
 * Sites - Backup Pro Format Date View Helper
 *
 * @package ViewHelpers\Date
 * @author Eric Lamb <eric@mithra62.com>
 */
class m62RelativeDateTime extends BaseViewHelper
{
    
    private $relative_config = array(
        'truncate' => 1
    );
    
	/**
	 * Returns the human readable date if under week old
	 * @param string $date
	 * @return string
	 */
	public function __invoke($timestamp, $ending = true)
	{
	    if (! $timestamp) {
	        return 'N/A';
	    }
	    
	    if (! is_numeric($timestamp)) {
	        $timestamp = (int) strtotime($timestamp);
	    }
	    
	    if ($timestamp == '0') {
	        return 'N/A';
	    }
	    
	    $this->relative_config['suffix'] = true;
	    if (! $ending) {
	        $this->relative_config['suffix'] = false;
	    }
	    
        $relative = new RelativeTime($this->relative_config);
        return $relative->timeAgo($timestamp);
	}
}