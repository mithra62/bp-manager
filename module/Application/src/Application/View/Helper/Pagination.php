<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/View/Helper/Pagination.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * Application - Pagination View Helper
 *
 * @package ViewHelpers\Pagination
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/View/Helper/Pagination.php
 */
class Pagination extends BaseViewHelper
{

    /**
     * Abstracts pagination for HTML
     * 
     * @param string $date            
     * @return void|boolean
     */
    public function __invoke($route_key, $total_pages = 1, $current_page = 1, array $options = array())
    {
        $return = '';
        if( $total_pages > $current_page )
        {
            $return = '<ul class="pagination pull-right">';
            for($i=1;$i<=$total_pages;$i++) {
                
                if($i === 1){
                    $prev_page = $current_page-1;
                    if($total_pages >  $prev_page && $prev_page !== 0) {
                        $return .= '<li><a href="'.$this->view->url($route_key).'?page='.$prev_page.'">&laquo;</a></li>';       
                    }
                }
                
                $return .= '<li class="'.($i == $current_page ? 'active' : '').'"><a href="'.$this->view->url($route_key).'?page='.$i.'">'.$i.'</a></li>';
                
                if($i == $total_pages) {
                    $next_page = $current_page+1;
                    if( $next_page <= $total_pages ) {
                        $return .= '<li><a href="'.$this->view->url($route_key).'?page='.$next_page.'">&raquo;</a></li>';
                    }
                }       
            }
            
            $return .= '</ul>';
        }
        return $return;            
    }
}