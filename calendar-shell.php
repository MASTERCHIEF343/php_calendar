<?php
	require('calender.php');
	//如果查询字符串中没有指定月或年
	//则显示当月的月历
	$month = date('m');
	$year = date('Y');
	
	$cal = new LittleCalendar($month,$year);

	echo  $cal->text();
?>