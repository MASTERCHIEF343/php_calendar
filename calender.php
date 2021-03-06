<?php
	class LittleCalendar{
		/**DateTime**/
		protected $monthToUse;
		protected $prepared = false; 
		protected $days = array();

		public function __construct($month, $year){
			/**为要显示的月份建立一个DateTime**/
			$this->monthToUse = DateTime::createFromFormat('Y-m|',sprintf("%04d-%02d",$year,$month));
			//以年为先，月在后
			//控制输出的年是4位，月是2位
			$this->prepare();
		}
		
		protected function prepare(){
			//建立一个数组，包含一个月每天的信息
			//适当的填充
			//开头和结尾
			//首先，第一行显示星期几
			foreach (array('Su','Mo','Tu','We','Th','Fr','Sa') as $dow) {
				$endOfRow = ($dow == 'Sa');
				//Su=0,Sa=6
				$this->days[] = array('type' => 'dow',
								      'label' => $dow,
								      'endOfRow' => $endOfRow);
			}

			//接下来，在一周的第一天之前放置站位符
			for($i = 0, $j = $this->monthToUse->format('w'); $i < $j; $i++){
				$this->days[] = array('type' => 'blank');
			}

			//然后，对这个月的每一天分别有一项
			$today = date('Y-m-d');
			$days = new DatePeriod($this->monthToUse,
								 new DateInterval('P1D'),
								 $this->monthToUse->format('t') - 1);
			//生成这个月的范围
			foreach ($days as $day) {
				$isToday = ($day->format('Y-m-d') == $today);
				$endOfRow = ($day->format('w') == 6);
				$this->days[] = array('type' => 'day',
								      'label' => $day->format('j'), //存储月份中的第几天
								      'today' => $isToday,
								      'endOfRow' => $endOfRow);
			}

			//最后，如果endofweek不是这个月的最后一天
			//在末尾放置站位符
			if(!$endOfRow){
				for($i = 0, $j = 6 - $day->format('w'); $i < $j; $i++){
					$this->days[] = array('type' => 'blank');
				}
			}
		}

		public function html($opts = array()){
			if(! isset($opts['id'])){
				$opts['id'] = 'calender';
			}
			if(! isset($opts['month_link'])){
				$opts['month_link'] = '<a href="'.htmlentities($_SERVER['PHP_SELF']).'?'.'month=%d&amp;year=%d">%s</a>';
				//$_SERVER['PHP_SELF'] 获取当前页面地址
			}
			//可选 array
			$classes = array();
			foreach (array('prev','month','next','weekday','blank','day','today') as $class) {
				if(isset($opts['class']) && isset($opts['class'][$class])){
					$classes[$class] = $opts['class'][$class];
				}else{
					$classes[$class] = $class;
				}
			}

			/**为上一个月建立DateTime**/
			$prevMonth = clone $this->monthToUse;
			$prevMonth->modify("-1 month");
			$prevMonthLink = sprintf($opts['month_link'],
								        $prevMonth->format('m'),
								        $prevMonth->format('Y'),
								        '&laquo');

		        	/**为下一个月建立DateTime**/     
			$nextMonth = clone $this->monthToUse;
			$nextMonth->modify("+1 month");
			$nextMonthLink = sprintf($opts['month_link'],
								        $nextMonth->format('m'),
								        $nextMonth->format('Y'),
								        '&raquo');
			$html = '<table id="'.htmlentities($opts['id']).'">
					<tr>
					<td class="'.htmlentities($classes['prev']).'">'.$prevMonthLink.'</td>
					<td class="'.htmlentities($classes['month']).'" colspan="5" >'.$this->monthToUse->format('F Y').'</td>
					<td class="'.htmlentities($classes['next']).'">'.$nextMonthLink.'</td>
					</tr> ';
			$html .= '<tr>';

			$lastDayIndex = count($this->days) - 1;
			foreach ($this->days as $i => $day) {
				switch ($day['type']) {
					case 'dow':
						$class = 'weekday';
						$label = htmlentities($day['label']);
						break;
					case 'blank':
						$class = 'blank';
						$label = '&nbsp;';
						break;
					case 'day':
						$class = $day['today'] ? 'today' : 'day';
						$label = htmlentities($day['label']);
						break;
				}
				$html .= '<td class=" '.htmlentities($classes[$class]).' ">'.$label.'</td>';
				
				if(isset($day['endOfRow']) && $day['endOfRow']){
					$html .= "</tr>\n";
					if($i != $lastDayIndex){
						$html .= '<tr>';
					}
				}
			}
			$html .= '</table>';
			return $html;
		}

		public function text(){
			$LineLength = strlen('Su Mo Tu We Th Fr Sa');
			$header = $this->monthToUse->format('F Y');
			$headerSpacing = floor(($LineLength - strlen($header))/2);

			$text = str_repeat('   ', $headerSpacing) . $header . "\n";

			foreach ($this->days as $i => $day) {
				switch ($day['type']) {
					case 'dow':
						$text .= sprintf('% 2s',$day['label']);
						break;
					case 'blank':
						$text .= '  ';
						break;
					case 'day':
						$text .= sprintf('% 2d',$day['label']);
						break;
				}
				$text .= (isset($day['endOfRow']) && $day['endOfRow']) ? "\n" : " ";
			}
			if($text[strlen($text) - 1] != "\n"){
				$text .= "\n";
			}
			return $text;
		}
	}
?>