<?php
	include_once 'includes/config.php'; // загружаем HOST, USER, PASSWORD
	include_once 'counter.php'; // загружаем Counter
	counter_go('1');
	
	// определяем начальные данные
	$db_name = 'other_db';
	$db_table_to_show = 'timestamps';
	
	// соединяемся с сервером базы данных
	$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
	or exit("Could not connect: " . mysql_error());
	
	// подключаемс¤ к базе данных
	mysql_select_db($db_name, $connect_to_db)
	or exit("Could not select DB: " . mysql_error());
	
	// выбираем все значения из таблицы
	$qr_result = mysql_query("SELECT * FROM " . $db_table_to_show)
	or exit('Could not find: ' . mysql_error());
	
	// закрываем соединение с сервером базы данных
	mysql_close($connect_to_db);
	
	$data = [];
	$result = mysql_num_rows($qr_result);
	while($row = mysql_fetch_array($qr_result)) {
		$nomination = $row['nomination'];
		$value = $row['value'];
		$data = array_merge_recursive($data, [$nomination => $value]);
	}
	
	// Формируем вывод
	$output = ['result' => $result, 'text' => 'ok', 'data' => $data];
	echo json_encode($output, JSON_FORCE_OBJECT); // и отдаем как json
	
?>