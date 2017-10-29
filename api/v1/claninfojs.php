<?php
	
	// Ввод дополнительных библиотек
	include_once 'includes/config.php'; // загружаем HOST, USER, PASSWORD
	include_once 'counter.php'; // загружаем Counter
	//counter_go('1');
	
	// принимаем входящие параметры
	$clanName = $_GET['clanName'];
	$clanTag = $_GET['clanTag'];
	
	// определяем начальные данные
	$db_name = 'sc_clan_db';
	$db_table_to_show = 'corporations_history';
	
	// соединяемся с сервером базы данных
	$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
	or die("Could not connect: " . mysql_error());
	
	// подключаемся к базе данных
	mysql_select_db($db_name, $connect_to_db)
	or die("Could not select DB: " . mysql_error());
	
	// если clanName не указан, ищем по clanTag
	if (!isset($clanName)) {
		$sql = "SELECT * FROM " . $db_table_to_show . " WHERE BINARY clanTag='" . $clanTag . "'";
	} else {
		$sql = "SELECT * FROM " . $db_table_to_show . " WHERE BINARY clanName='" . $clanName . "'";
	}
	
	// выбираем все значения из таблицы uid
	$qr_result = mysql_query($sql)
	or die("Could not found: " . mysql_error());
	
	// закрываем соединение с сервером базы данных
	mysql_close($connect_to_db);
	
	$bigdata = [];
	$result = mysql_num_rows($qr_result);
	while($data = mysql_fetch_array($qr_result)) {
		$date = new DateTime($data['date']);
		$date->modify('-1 day');
		$date = $date->format('Y-m-d');
		$data = array_replace_recursive($data, ['date' => $date, 0 => $date]);
		$bigdata = array_merge_recursive($bigdata, [$data]);
	}
	
	// Формируем вывод
	$output = ['result' => $result, 'text' => 'ok', 'bigdata' => $bigdata];
	echo json_encode($output, JSON_FORCE_OBJECT); // и отдаем как json
	
?>