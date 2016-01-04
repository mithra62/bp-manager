<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/Calendar.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper, DateTime, IntlDateFormatter, DateInterval, Exception;

/**
 * PM - Calendar View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/View/Helper/Calendar.php
 */
class Calendar extends BaseViewHelper
{
	/**
	 * The users set locale
	 * @var string
	 */
	private $locale;
	
	/**
	 * The current datetime
	 * @var Datetime
	 */
    private $now;
    
    /**
     * The date we're working with
     * @var DateTime
     */
    private $date;
    
    /**
     * An array of all the month names translated
     * @var array
     */
    private $monthNames = array();
    
    /**
     * An array of the 7 days of the week
     * @var array
     */
    private $dayNames = array();
    
    /**
     * The date range the calendar uses
     * @var array
     */
    private $validDates = array();
    
    /**
     * The number of datys in the current month
     * @var int
     */
    private $numMonthDays;
    
    /**
     * A DateTime object with the next month set
     * @var DateTime
     */
    private $nextMonth;

    /**
     * A DateTime object with the previous month set
     * @var DateTime
     */    
    private $prevMonth;
    
    /**
     * The first day of the week
     * @var int
     */
    private $firstDayOfWeek;
    
    /**
     * The number of weeks in the current month
     * @var int
     */
    private $numWeeks;
    private $localeStr = null;
    public  $url_base = FALSE;
    public  $date_key = FALSE;
    public  $date_data = FALSE;
    public  $link_rel = '';
	
    /**
     * Sets up and calls the Calendar object
     * @param string $calendar_data
     * @param string $base_url
     * @param string $date_key
     * @param string $link_rel
     * @return string
     */
	public function __invoke($month = null, $year = null)
	{
		//set the locale
		$prefs = $this->getUserData();
		$locale = $prefs['locale'];
		$base_date = null;

		if ($month != '' && $year != '') 
		{
			$base_date = date('r', mktime(12,0,0,$month, 1, $year));
		} 
		else 
		{
			$this->month = date('m');
			$this->year = date('Y');
			$base_date = date('r', mktime(12,0,0,date('m'), 1, date('Y')));
		}
		
		$this->setDate($base_date, $locale);
		$this->setValidDateRange(-36,48);
		return $this;
	}
	
	/**
	 * Sets the default values and calculations
	 * @param string $date
	 */
	private function initDateParams ($date)
	{
		$this->monthNames = $this->getMonthNames();
		$this->dayNames = $this->getDayNames(); 
		$this->setValidDateRange();
		$this->numMonthDays = $this->date->format('t');
		$this->setNextMonth($date);
		$this->setPrevMonth($date);
		$this->firstDayOfWeek = $this->date->format('w');
		$this->numWeeks = ceil(($this->getFirstDayOfWeek() + $this->getNumMonthDays()) / 7);
	}
	
	/**
	 * Sets the data to display in each calendar cell
	 * @param array $data
	 * @return \PM\View\Helper\Calendar
	 */
	public function setData(array $data)
	{
		$this->date_data = $data;
		return $this;
	}
	
	/**
	 * Sets the route name for the items to view
	 * @param string $link_base
	 * @return \PM\View\Helper\Calendar
	 */
	public function setDayRouteName($link_base)
	{
		$this->day_route_name = $link_base;
		return $this;
	}
	
	/**
	 * Sets the route name for date values
	 * @param string $link_base
	 * @return \PM\View\Helper\Calendar
	 */
	public function setMonthRouteName($link_base)
	{
		$this->month_route_name = $link_base;
		return $this;
	}
	
	/**
	 * Sets the month names, handling translations accordingly based on locale
	 * @return multitype:array
	 */
	private function setMonthNames()
	{
		$range = range(1,12);
		$return = array();
		foreach($range AS $key => $monthNum)
		{
		    $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM');
			$return[$monthNum] = datefmt_format( $fmt , mktime(12,0,0,$monthNum,1,date('Y')));
		}

		return $return;
	}
	
	/**
	 * Sets the day names, handling translations
	 * @return multitype:array
	 */
	private function setDayNames()
	{
	    $range = range(1,7);
	    $return = array();
	    foreach($range AS $key => $dayNum)
	    {
	    	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'eee');
	    	$key = strtolower(datefmt_format( $fmt , mktime(12,0,0,4,$dayNum+5,2014))); //we force the date so things start on Sunday
	    	
	    	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'EEEE');
	    	$return[$key] = datefmt_format( $fmt , mktime(12,0,0,4,$dayNum+5,2014));
	    }

	    return $return;
	}
	
	/**
	 * Sets the date range the calendar uses
	 * @param int $startOffset
	 * @param int $endOffset
	 */
	public function setValidDateRange ($startOffset = -1, $endOffset = 12)
	{
		$this->validDates = array();
		$startDate = clone $this->now;
		$abs = abs($startOffset);
		$startMonth = $startDate->add(DateInterval::createFromDateString($startOffset.' month'));

		$startNum = intval($startMonth->format("m"));
		$key_fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
		$value_fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMM- yyyy');
		$key = datefmt_format( $key_fmt , strtotime($startMonth->format('r')));
		
		$this->validDates[$key] = datefmt_format( $key_fmt , strtotime($startMonth->format('r')));
		for ($i = $startNum; $i <= ($startNum + $endOffset); $i ++) {
		    
		    $month = $startDate->add(DateInterval::createFromDateString('1 month'));
		    $str = datefmt_format( $key_fmt , strtotime($month->format('r')));
			$this->validDates[$str] = $str;
		}
		unset($startDate);
		unset($startMonth);
		unset($startNum);
	}

	/**
	 * Creates an object for the next month based on the current, passed, date object
	 * @param DateTime $date
	 */
	private function setNextMonth (DateTime $date)
	{
		$tempDate = clone $date;
		$this->nextMonth = $tempDate->add(DateInterval::createFromDateString('1 month'));
		unset($tempDate);
	}

	/**
	 * Creates an object for the previous month based n the passed date object
	 * @param DateTime $date
	 */
	private function setPrevMonth (DateTime $date)
	{
		$tempDate = clone $date;
		$this->prevMonth = $tempDate->sub(DateInterval::createFromDateString('1 month'));
		unset($tempDate);
	}

	public function getCalendarHeaderHtml ( $arr = NULL )
	{
		$showPrevMonthLink = true;
		$showNextMonthLink=false;
		$selectBox=false;
		$selectBoxName="selectMonth";
		$selectBoxFormName="selectMonthForm";
		if (is_array($arr))
			extract( $arr );
	
		//prev/next link in header display
		$pLink = $nLink = "";
		$pLinkClass = "id=\"prevMonth\" style=\"visibility: visible;\"";
		$nLinkClass = "id=\"nextMonth\" style=\"visibility: visible;\"";
		if ($showPrevMonthLink) 
		{
			$date_string = $this->getPrevMonthAsDateString();
			$month = $this->getPrevMonthNum();
			$year = $this->getPrevMonthYear();			
			if (! array_key_exists($date_string, $this->validDates)) //check if the prev month in list of valid dates
				$pLinkClass = "id=\"prevMonth\" style=\"visibility: hidden;\"";
			$pLink = "<a $pLinkClass href=\"".$this->view->url($this->month_route_name, array('month' => $month, 'year' => $year)). "\">&lt;&nbsp;$date_string</a>\n";
		}
		if ($showNextMonthLink) 
		{
			$date_string = $this->getNextMonthAsDateString();
			$month = $this->getNextMonthNum();
			$year = $this->getNextMonthYear();
			
			if (! array_key_exists($date_string, $this->validDates)) //check if the next month in list of valid dates
				$nLinkClass = "id=\"nextMonth\" style=\"visibility: hidden;\"";
			$nLink = "<a $nLinkClass href=\"".$this->view->url($this->month_route_name, array('month' => $month, 'year' => $year)). "\">$date_string&nbsp;&gt;</a>\n";
		}

		$headDate = $this->getDateAsString();
		if ($selectBox) 
		{
			$headDate = "\n<form name=\"$selectBoxFormName\" id=\"$selectBoxFormName\" method=\"get\">\n";
			$headDate .= $this->getValidDatesSelectBox(array('selectedDateStr'=>$this->getDateAsString(),
					'selectBoxName'=>$selectBoxName));
			$headDate .= "</form>\n";
		}
		return "<div id=\"calendar_header\">$pLink&nbsp;$headDate&nbsp;$nLink</div>\n";
	
	}

	 public function getCalendarBodyHtml ( $arr = NULL )
	 {
	 	$showToday = true;
	 	$tableClass = "calendar";
	 	if (is_array($arr))
	 		extract($arr);
	 	
	 	$html = '';
	 	
	 	$html .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"$tableClass\">\n";
	 	$html .= "<tr class=\"weekdays\">\n";
	 	
	 	//days of the week display
	 	foreach ($this->dayNames as $dayShort=>$dayFull)
	 		$html .= "<td class='header'>$dayFull</td>\n";
	 	
	 	$html .= "</tr>\n";
	 	
	 	//day numbers displaydate
	 	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'd');
	 	$today = datefmt_format($fmt, strtotime($this->now->format('r')));
	 	
	 	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
	 	$nowDate = datefmt_format($fmt, strtotime($this->now->format('r')));
	 	$focusDate = $this->getDateAsString();
	 	$calDayNum = 1;
	 	
	 	//day numbers display loop
	 	for ($i = 0; $i < $this->getNumWeeks(); $i ++)
	 	{
		 	$html .= "<tr class=\"days\">";
		 	for ($j = 0; $j < 7; $j ++)
		 	{
			 	$cellNum = ($i * 7 + $j);
			 	$class = '';
			 	$m_date = FALSE;
			 	if($this->day_route_name)
			 	{
			 		$m_date = $this->getYear().'-'.(strlen($this->getMonthNum()) == 1 ? '0'.$this->getMonthNum() : $this->getMonthNum()).'-'.(strlen($calDayNum) == 1 ? '0'.$calDayNum : $calDayNum);
			 	}
			 	
			 	if ($showToday && $nowDate == $focusDate && $today == $calDayNum && $cellNum >= $this->getFirstDayOfWeek())
			 		$class = "class = \"today\"";
			 	
			 	$html .= "<td $class>";
		 	
		 		$date = FALSE;
		 		if ($cellNum >= $this->getFirstDayOfWeek() && $cellNum < ($this->getNumMonthDays() + $this->getFirstDayOfWeek()))
		 		{
		 			$date = $calDayNum;
		 			if($m_date && $this->day_route_name)
		 			{
		 				$link = '<a href="'.$this->view->url($this->day_route_name, array('month' => $this->date->format('n'), 'year' => $this->date->format('Y'), 'day' => $date)).'" rel="'.$this->link_rel.'">'.$date.'</a>';
		 				$html .= $link;
		 				$html .= $this->process_date_data($m_date);
		 			}
		 			$calDayNum ++;
		 		}
		 	
		 		$html .= "</td>\n";
		 	}
		 	$html .= "</tr>\n";
	 	}
		$html .= "</table>\n";
		return $html;
    }
	
    private function process_date_data($m_date)
	{
        if(!is_array($this->date_data))
        {
            return;
        }
	
        $stuff = '';
        if(array_key_exists($m_date, $this->date_data))
        {
        	$route_date = strtotime($m_date);
        	$route_options = array('day' => date('j', $route_date), 'year' => date('Y', $route_date), 'month' => date('n', $route_date));
            foreach($this->date_data[$m_date] AS $data)
            {
            	$route_options = array_merge($route_options, $data['route']['options']);
                $stuff .= '<br /><a href="'.$this->view->url($data['route']['route_name'], $route_options).'" rel="'.$data['rel'].'" title="'.strip_tags($data['string']).'">'.$data['string'].'</a>';
            }
        }
	
		return $stuff;
	 }
	
    public function getCalendarHtml ( $arr = NULL )
    {
        $showToday=false;
        $showPrevMonthLink=false;
        $showNextMonthLink=false;
        $tableClass="calendar";
        $selectBox=false;
        $selectBoxName="selectMonth";
        $selectBoxFormName="selectMonthForm";
        if (is_array($arr))
        	extract ($arr);
        			
        $html = "<div id=\"calendar_wrapper\">\n";
        $html .= $this->getCalendarHeaderHtml(
            array(
                'showPrevMonthLink'=>$showPrevMonthLink,
                'showNextMonthLink'=>$showNextMonthLink,
                'selectBox'=>$selectBox,
                'selectBoxName'=>$selectBoxName,
                'selectBoxFormName'=>$selectBoxFormName)
        ); 
        
        $html .= "<div id=\"calendar_body\">\n";
        $html .= $this->getCalendarBodyHtml(array('showToday'=>$showToday,
        'tableClass'=>$tableClass)); //returns a table
        $html .= "</div>\n</div>\n";

        return $html;
    }

	public function getValidDatesSelectBox ( $arr = NULL )
	{
		$month_route = $this->getMonthRouteName();
		$selectedDateStr=false;
		$selectBoxName="";
	    if (is_array($arr))
	    	extract($arr);
	
		$html = "<select name=\"$selectBoxName\" id=\"$selectBoxName\" class=\"select\" >\n";
	 	foreach ($this->validDates as $option => $value) 
	 	{
	 		$selected = '';
	 		if ($selectedDateStr && $selectedDateStr == $option)
	 		{
	 			$selected = "selected";
	 		}
	 		
	 		$date_str = strtotime($option);
	 		$html .= '<option value="'.$this->view->url($this->getMonthRouteName(), array('month' => date('n', $date_str), 'year' => date('Y', $date_str))).'" '.$selected.'>'.$value.'</option>'."\n";
		}
		
	 	$html .= "</select>\n";
	 	return $html;
	}
	
	public function getMonthRouteName()
	{
		return $this->month_route_name;
	}
	
	public function getValidDates ()
	{
		return $this->validDates;
	}
	
    public function getMonthNames ()
    {
    	if(!$this->monthNames)
    	{
    		$this->monthNames = $this->setMonthNames();
    	}
    	
        return $this->monthNames;
    }
    
	public function getDayNames ()
	{
        if(!$this->dayNames)
        {
        	$this->dayNames = $this->setDayNames();
        }
	    	    
        return $this->dayNames;
	}

	
	public function getLocale ()
	{
        return $this->locale;
	}
	
	public function getLocaleAsString ()
	{
        return $this->locale->toString();
	}
	
	public function getFirstDayOfWeek ()
	{
        return $this->firstDayOfWeek;
	}
	
	public function getDate ()
	{
        return $this->date;
	}

    public function getDateAsString ()
    {
        $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
        return datefmt_format($fmt, strtotime($this->date->format('r')));        
    }
    
	public function setDate ($date = null, $locale = "en_US")
	{
    	$this->now = new DateTime();
    	$this->locale = $locale; 
    	
    	if($date === null){
    		$date = date('r', mktime(12, 0, 0, date('m'), '1', date('Y')));
    	}
    	
    	//date
    	try {
    	   $this->date = new DateTime($date);
    	} catch (Exception $zde) {
    	   $this->date = new DateTime();
    	}
    	//date params
    	$this->initDateParams($this->date);
	}
	
	/**
	* @return int
	*/
	public function getNumMonthDays ()
	{
	   return $this->numMonthDays;
	}
	
	public function getMonthName ()
	{
        return $this->date->format('F');
	}

    public function getMonthShortName ()
	{
        return $this->date->format("M");
	}
	
	public function getMonthNum ()
	{
	    return $this->date->format("n");
	}

    public function getYear ()
	{
        return $this->date->format("Y");
	}
	
    public function getNextMonthName ()
	{
		return $this->nextMonth->format("F");
	}

	public function getNextMonthNum ()
	{
		return $this->nextMonth->format("n");
	}

     public function getNextMonthYear ()
     {
     	return $this->nextMonth->format("Y");
     }
	     
    public function getNextMonthAsDateString ()
	{
	    $fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
	    return datefmt_format($fmt, strtotime($this->nextMonth->format('r')));
	}

    public function getPrevMonthName ()
	{
		return $this->prevMonth->format("F");
	}
	
	public function getPrevMonthNum ()
	{
		return $this->prevMonth->format("n");
	}
	
	public function getPrevMonthYear ()
	{
		return $this->prevMonth->format("Y");
	}
	
    public function getPrevMonthAsDateString ()
    {
	 	$fmt = datefmt_create ($this->locale, null, null, null, IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
	 	return datefmt_format($fmt, strtotime($this->prevMonth->format('r')));
	}
	
	public function getNumWeeks ()
	{
		return $this->numWeeks;
	}	
}