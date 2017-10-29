<?php
	function legendSC_go() {
		include_once 'includes/config.php'; // загружаем HOST, USER, PASSWORD
		include_once 'additional_functions.php'; // загружаем дополнительный Функции
		
		// определяем начальные данные
		$db_name = 'sc_history_db';
		
		// соединяемся с сервером базы данных
		$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
		or die("Could not connect: " . mysql_error());
		
		// подключаемся к базе данных
		mysql_select_db($db_name, $connect_to_db)
		or die("Could not select DB: " . mysql_error());
		
		// выбираем все значения из таблицы
		$qr_result = mysql_query("SELECT * FROM top100 ORDER BY gameWin DESC LIMIT 50")
		or die("Could not found: " . mysql_error());
		
		// выводим на страницу сайта заголовки HTML-таблицы
		DisplayText('<div style="padding: 5px;">');
		echo('<style type="text/css">');
		echo('.brick { background: #a6432e; color: black; padding-left: 5px; padding-right: 5px; display: inline-block; margin: 1px; }');
		echo('</style>');

		
		// выводим в HTML-таблицу все данные клиентов из таблицы MySQL 
		while($data = mysql_fetch_array($qr_result)){ 
			echo('<div class="brick" title="' . $data['gameWin'] . '">');
			
			#echo('<td> ' . $data['nickname'] . ' </td>');
			echo(' ' . $data['nickname'] . ' ');
			
			echo('</div>');
		}
		
		DisplayText('</div>');
		
		// закрываем соединение с сервером базы данных
		mysql_close($connect_to_db);
	}
?>