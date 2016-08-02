<style type="text/css">
.prev { text-align: left; }
.next { text-align: right; }
.day, .month, .weekday { text-align: center; }
.today { background: yellow; }
.blank { }
</style>
<?php
	require('calender.php');
	header("Content-Type: text/html;charset=utf-8");
	//如果查询字符串中没有指定月或年
	//则显示当月的月历
	$month =isset($_GET['month']) ? intval($_GET['month']) : date('m');
	$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
	
	$cal = new LittleCalendar($month,$year);

	echo  $cal->html();
?>