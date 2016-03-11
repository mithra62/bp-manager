<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Currencies.php
 */
namespace Application\Model\Options;

/**
 * PM - Currencies Options Model
 *
 * @package Localization\Options
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/PM/src/PM/Model/Options/Currencies.php
 */
class Currencies
{

    static public function codes()
    {
        $data_path = realpath(dirname(__FILE__) . '/../../../../../../data');
        $xml = file_get_contents($data_path . '/currency_formats.xml');
        $currencyArray = \Zend\Json\Json::decode(\Zend\Json\Json::encode(simplexml_load_string($xml)), 1);
        
        $return = array();
        if (! empty($currencyArray['CcyTbl']['CcyNtry']) && is_array($currencyArray['CcyTbl']['CcyNtry'])) {
            foreach ($currencyArray['CcyTbl']['CcyNtry'] as $key => $value) {
                $return[$value['Ccy']] = $value['CcyNm'];
            }
        }
        
        return $return;
    }
}