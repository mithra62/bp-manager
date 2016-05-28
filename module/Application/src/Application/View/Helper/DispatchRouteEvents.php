<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		2.0
 * @filesource 	./module/Application/View/Helper/DispatchRouteEvents.php
 */
namespace Application\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * Application - Allows overriding the rendered partials a given view is using
 *
 * Dispatches the event wrapper to override the partials being used to construct a page
 *
 * @param array $partials
 *            The partial views in the order they're to be rendered
 * @param array $context
 *            The view parameters
 * @package ViewHelpers\Views
 * @author Eric Lamb <eric@mithra62.com>
 * @filesource ./module/Application/View/Helper/DispatchRouteEvents.php
 */
class DispatchRouteEvents extends BaseViewHelper
{

    /**
     *
     * @ignore
     *
     */
    public function __invoke(array $partials = array(), array $context = array())
    {
        $route_match = $this->serviceLocator->getServiceLocator()
            ->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $view_event = $this->serviceLocator->getServiceLocator()->get('Application\Model\ViewEvents');
        $event_name = 'view.render.' . str_replace('/', '.', $route_match->getMatchedRouteName());
        
        $partials = $view_event->runEvent($event_name, $partials, $context);
        return $partials;
    }
}