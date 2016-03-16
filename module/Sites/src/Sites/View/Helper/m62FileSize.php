<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource 	./module/Sites/src/Sites/View/Helper/m62FileSize.php
 */
namespace Sites\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * Sites - Backup Pro Format Filesize View Helper
 *
 * @package ViewHelpers\Filesize
 * @author Eric Lamb <eric@mithra62.com>
 */
class m62FileSize extends BaseViewHelper
{
    public function __invoke($string, $html = true)
    {
        if( $string == '' )
        {
            return $this->getView()->_('na');
        }
        
        $formatted_size = $this->filesizeFormat($string);
        $return = '';
        if( $html )
        {
            $return = '<span class="backup_pro_filesize" title="' . number_format($string) . ' bytes">' . $formatted_size . '</span>';
        }
        else
        {
            $return = $formatted_size;
        }
        
        return $return;
    }


    /**
     * Format a number of bytes into a human readable format.
     * Optionally choose the output format and/or force a particular unit
     *
     * @param string $val
     *            The number to format
     * @param number $digits
     *            How many digits to display
     * @param string $mode
     *            Either SI or EIC to determine either 1000 or 1024 bytes
     * @param string $bB
     *            Whether to use b or B formatting
     * @return string
     */
    public function filesizeFormat($val, $digits = 3, $mode = "IEC", $bB = "B")
    { // $mode == "SI"|"IEC", $bB == "b"|"B"
        $si = array("","k","M","G","T","P","E","Z","Y");
        $iec = array(
            "",
            "Ki",
            "Mi",
            "Gi",
            "Ti",
            "Pi",
            "Ei",
            "Zi",
            "Yi"
        );
        switch (strtoupper($mode)) {
            case "SI":
                $factor = 1000;
                $symbols = $si;
                break;
            case "IEC":
                $factor = 1024;
                $symbols = $iec;
                break;
            default:
                $factor = 1000;
                $symbols = $si;
                break;
        }
        switch ($bB) {
            case "b":
                $val *= 8;
                break;
            default:
                $bB = "B";
                break;
        }
        for ($i = 0; $i < count($symbols) - 1 && $val >= $factor; $i ++) {
            $val /= $factor;
        }
        $p = strpos($val, ".");
        if ($p !== false && $p > $digits) {
            $val = round($val);
        } elseif ($p !== false) {
            $val = round($val, $digits - $p);
        }
        
        return round($val, $digits) . " " . $symbols[$i] . $bB;
    }
}