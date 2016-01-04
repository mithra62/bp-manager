<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectPriority.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Fusion Charts View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectPriority.php
 */
class FusionCharts extends BaseViewHelper
{
	/**
	 * Wrapper to generate the chart and return it
	 * @param string $chart
	 * @param string $chartType
	 * @param string $width
	 * @param string $height
	 * @param string $chartID
	 * @param string $isTransparent
	 */
	public function __invoke($chart, $chartType = 'column2d', $width="400", $height="300", $chartID="", $isTransparent="")
	{
		$helperPluginManager = $this->getServiceLocator();
		$serviceManager = $helperPluginManager->getServiceLocator();
		
		$this->data = $serviceManager->get('PM\Model\Charts');
		$this->identity = $this->getIdentity();
				
		$this->chart = $serviceManager->get('PM\Model\FusionCharts');
		$this->chart->setup($chartType, $width, $height, $chartID, 'yes');
		
		$this->chart->setSwfPath($this->view->StaticUrl()."/charts/");
		$this->chart->setParamDelimiter(";");
		$this->chart->setChartMessage("ChartNoDataText=No Data Available;PBarLoadingText=Please Wait.The chart is loading...");
		$strParam = 'bgColor=ffffff; formatNumber=1; formatNumberScale=0; numVDivlines=10; bgAlpha=40; numdivlines=4; outCnvBaseFontColor=666666; showAlternateVGridColor=1; AlternateVGridColor=e1f5ff; divLineColor=e1f5ff; baseFontColor=666666; hoverCapBgColor=F3F3F3; hoverCapBorderColor=666666; canvasBorderColor=666666; canvasBorderThickness=1; limitsDecimalPrecision=0; divLineDecimalPrecision=0; decimalPrecision=2;';
		
		# Set chart attributes
		$this->chart->setChartParams($strParam);
		$this->chart->addColors("99B3FF; FFE1E1; E9E9E9; E3FFDF;B0BED9; FFFBCC;B0BED9;D7DFFF;");
				
		$method = "_$chart";
		if(method_exists($this, $method))
		{
			return $this->$method();
		}
	}
	
	private function _task_gantt()
	{
		$date_format = 'm/d/Y';
		
		$this->chart->setChartParam('extendCategoryBg', '0');
		$this->chart->setChartParam('ganttLineColor', 'B0BED9');
		$this->chart->setChartParam('ganttLineAlpha', '60');
		
		//$this->chart->setChartParams("dateFormat=m/d/Y");
		$tasks = $this->data->getProjectGantt($this->view->project);
		$total_tasks = count($tasks);

		if($total_tasks == 0)
		{
			return $this->no_chart();
		}
		
		if($total_tasks == 1)
		{
			$this->chart->height = 100;
		}
		elseif($total_tasks <= 10)
		{
			$this->chart->height = $total_tasks*50;
		}
		$date_range = $this->data->getProjectTaskDateRange($this->view->project);	
		$this->chart->addGanttCategorySet($tasks['0']['project_name']);
		$this->chart->setGanttProcessesParams("fontSize=11; isBold=0; headerbgColor=E3FFDF; align=left; headerFontSize=16; headerVAlign=top; headerAlign=left;");
		$count = 0;
		foreach($tasks AS $task)
		{	
			$bg_color = ($count%2 ? 'E3FFDF' : 'E9E9E9');
			$task_url = $this->view->url(array('module' => 'pm','controller' => 'tasks','action'=>'view', 'id' => $task['task_id']), null, TRUE);
			$this->chart->addGanttProcess(str_replace("'", '', $task['task_name']), "color=FFE1E1; bgColor=$bg_color; id=".$task['task_id']."; link=$task_url");
			$this->chart->addGanttTask(str_replace("'", '', $task['task_name']), "color=FFE1E1; bgColor=$bg_color; processId=".$task['task_id']."; start=".$this->utils->formatDate($task['start_date'], $date_format." H:i:s")."; end=".$this->utils->formatDate($task['end_date'], $date_format." H:i:s")."; link=".$task_url.";");
			$count++;
		}
		
		$min_date = new DateTime($date_range['min_date']);
		$max_date = new DateTime($date_range['max_date']);
		
		$interval = $max_date->diff($min_date);		
		
		$month_diff = (int)$interval->format('%m');
		$hour_parts = explode(" ", $date_range['min_date']);
		$dp = explode('-', $hour_parts['0']);
		for($i=0; $i<=$month_diff; $i++)
		{
			$last_day = date('t', mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));
			$start_date = date($date_format, mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));
			$end_date = date($date_format, mktime(1, 0, 0, $dp['1']+$i, $last_day, $dp['0']));
			$date_name = date('M, Y', mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));
			$cal_link_date = date('F Y', mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));
			$this->chart->addGanttCategory($date_name, "bgColor=E3FFDF; start=".$start_date." 00:00:00; end=".$end_date." 23:59:59; link=/pm/calendar/?date=".urlencode($cal_link_date));
		}
		
		if((int)$interval->format('%d') <= '31')
		{
			$last_day = date('t', mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));
			$start_date = date($date_format, mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));
			$end_date = date($date_format, mktime(1, 0, 0, $dp['1']+$i, $last_day, $dp['0']));
			$date_name = date('M, Y', mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));
			$cal_link_date = date('F Y', mktime(1, 0, 0, $dp['1']+$i, 1, $dp['0']));			
			$this->chart->addGanttCategory($date_name, "bgColor=E3FFDF; start=".$start_date." 00:00:00; end=".$end_date." 23:59:59; link=/pm/calendar/?date=".urlencode($cal_link_date));
		}

		//echo $this->chart->getXML();
		//exit;
		return $this->chart->renderChart(false, false);	
	}
	
	private function _times_sum_user()
	{
		$range = 30;
		//$this->chart->setChartParam('caption', 'Recorded Time for Past '.$range.'Days');
		$this->chart->setChartParam('xAxisName', 'Date');
		$this->chart->setChartParam('rotateNames', '1');
		$this->chart->setChartParam('showValues', '1');	
		$this->chart->setChartParam('formatNumber', '0');
		$this->chart->setChartParam('formatNumberScale', '0');
		$times = $this->data->getUserDateSumTimes($this->identity, $range);
		
		if(count($times) == '0')
		{
			return $this->no_chart('No Recorded Time');
		}
		
		$total_times = 0;
		for($i=0; $i<=$range;$i++)
		{
			$math = $range-$i;
			$month = date("n");
			$day = date("j");
			$year = date("Y");
			$datestring = mktime(0, 0, 0, $month, $day-$math, $year);
			$date = date('Y-m-d', $datestring); 
			$found = false;
			foreach($times AS $time)
			{
				if($date == $time['date'])
				{
					$found = true;
					$total_times = $total_times+$time['total_hours'];
					$this->chart->addChartData($time['total_hours'],"name=".$this->formatDate($time['date'], 'M, d')."; alpha=70; link=".$this->view->url('times/view-day', array('month' => date('n', $datestring), 'year' => date('Y', $datestring), 'day' => date('j', $datestring))));
					break;
				}
			}
			
			if(!$found)
			{
				$this->chart->addChartData(0,"name=".$this->formatDate($date, 'M, d')."; alpha=70; link=".$this->view->url('times/view-day', array('month' => date('n', $datestring), 'year' => date('Y', $datestring), 'day' => date('j', $datestring))));
			}
		}
		
		$count = count($times);
		if($count > 0)
		{
			$this->chart->addTrendLine("startValue=".($total_times/$count).";endValue=".($total_times/$count).";color=999999;thickness=2;alpha=70;isTrendZone=0;showOnTop=1");
		}
		return $this->chart->renderChart(false, false);			
	}
	
	private function _times_sum_date()
	{
		$this->chart->setChartParam('caption', 'Time History');
		$this->chart->setChartParam('xAxisName', 'Month / Year');
		$this->chart->setChartParam('showValues', '1');	
		$this->chart->setChartParam('formatNumber', '0');
		$this->chart->setChartParam('formatNumberScale', '0');
		$times = $this->data->getDateSumTimes();
		$total_times = 0;
		foreach($times AS $time)
		{
			$total_times = $total_times+$time['total_hours'];
			$parts = explode('-', $time['f_date']);
			$url_date = $this->utils->getMonthString($parts['1']).'+'.$parts['0'];
			$this->chart->addChartData($time['total_hours'],"name=".$time['f_date']."; alpha=70; link=".$this->view->url(array('module' => 'pm','controller' => 'times','action'=>'index'), null, TRUE).'?date='.$url_date);
		}
		
		$this->chart->addTrendLine("startValue=".($total_times/count($times)).";endValue=".($total_times/count($times)).";color=999999;displayvalue=Average.; thickness=2;alpha=70;isTrendZone=0;showOnTop=1");
		return $this->chart->renderChart(false, false);		
	}

	private function _task_status()
	{
		$this->chart->setChartParam('caption', 'Task Status');
		$this->chart->setChartParam('showValues', '1');	
		$this->chart->setChartParam('formatNumber', '0');
		$this->chart->setChartParam('formatNumberScale', '0');
		$this->chart->setChartParam('pieSliceDepth', '10');
		$statuses = $this->data->getTaskStatus();
		foreach($statuses AS $status)
		{
			$this->chart->addChartData($status['status_count'],"name=".$this->view->ProjectStatus($status['status'])."; alpha=70");
		}
		return $this->chart->renderChart(false, false);			
	}
		
	private function _project_status()
	{
		$this->chart->setChartParam('caption', 'Project Status');
		$this->chart->setChartParam('showValues', '1');	
		$this->chart->setChartParam('formatNumber', '0');
		$this->chart->setChartParam('formatNumberScale', '0');
		$this->chart->setChartParam('pieSliceDepth', '30');
		$statuses = $this->data->getProjectStatus();
		foreach($statuses AS $status)
		{
			$this->chart->addChartData($status['status_count'],"name=".$this->view->ProjectStatus($status['status'])."; alpha=70");
		}
		return $this->chart->renderChart(false, false);			
	}
		
	private function _company_types()
	{
		$this->chart->setChartParam('caption', 'Company Types');
		$this->chart->setChartParam('showValues', '1');	
		$this->chart->setChartParam('formatNumber', '0');
		$this->chart->setChartParam('formatNumberScale', '0');
		$this->chart->setChartParam('pieSliceDepth', '30');
		$types = $this->data->getCompanyTypes();
		foreach($types AS $type)
		{
			$this->chart->addChartData($type['type_count'],"name=".$this->view->CompanyType($type['type'])."; alpha=70");
		}
		return $this->chart->renderChart(false, false);			
	}
	
	private function _user_times()
	{
		$this->chart->setChartParam('caption', 'Hours Logged By User');
		$this->chart->setChartParam('showValues', '1');
		
		$company = $this->view->company;
		$date = $this->view->date;
		$times = $this->data->getUserTimes($company, $date);
		$total_times = 0;
		foreach($times AS $time)
		{
			$total_times = $total_times+$time['hours_worked'];
			$this->chart->addChartData($time['hours_worked'],"name=".$time['first_name']." ".$time['last_name']."; alpha=70; ");
		}
		$this->chart->addTrendLine("startValue=".($total_times/count($times)).";endValue=".($total_times/count($times)).";color=999999;displayvalue=Average.; thickness=2;alpha=70;isTrendZone=0;showOnTop=1");
		return $this->chart->renderChart(false, false);
	}
	
	private function _company_items()
	{
		$companies = $this->data->getCompanyProjectsToTask();
		//$this->chart->height = count($companies)*15;
		$this->chart->setChartParam('showValues', '0');
		$this->chart->setChartParam('formatNumber', '0');

		$this->chart->setChartParam('caption', 'Number of Projects by Company');
		$this->chart->addDataset("Projects");
		foreach($companies AS $company)
		{
			if(!array_key_exists('name', $company))
			{
				continue;
			}
			$this->chart->addCategory($company['name']);
			$this->chart->addChartData($company['project_count'],"link=".$this->view->url(array('module' => 'pm','controller' => 'reports','action'=>'projects', 'company' => $company['id']), null, TRUE));
		}
		
		$this->chart->addDataset("Tasks");
		foreach($companies AS $company)
		{
			if(!array_key_exists('name', $company))
			{
				continue;
			}						
			$this->chart->addChartData($company['task_count'],"link=".$this->view->url(array('module' => 'pm','controller' => 'reports','action'=>'tasks', 'company' => $company['id']), null, TRUE));
		}		

		return $this->chart->renderChart(false, false);		
	}
	
	private function _project_items()
	{
		$company = $this->view->company;
		$projects = $this->data->getProjectsTasks($company);
		
		//$this->chart->height = count($projects)*20;
		$this->chart->setChartParam('showValues', '0');
		$this->chart->setChartParam('formatNumber', '0');

		$this->chart->setChartParam('caption', 'Number of Tasks by Project');
		$this->chart->addDataset("Tasks");
		foreach($projects AS $project)
		{
			if(!array_key_exists('name', $project))
			{
				continue;
			}
			$this->chart->addCategory($project['name']);
			$this->chart->addChartData($project['task_count'],"link=".$this->view->url(array('module' => 'pm','controller' => 'reports','action'=>'tasks', 'project' => $project['id']), null, TRUE));
		}		

		return $this->chart->renderChart(false, false);			
	}
	
	private function no_chart($message = 'No data to plot...')
	{
		$str = '<div class="information png_bg">'.$message.'</div>';
		return $str;
	}
	
	private function average($a){
		return array_sum($a)/count($a) ;
	}	
}