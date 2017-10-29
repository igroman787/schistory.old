<?php
	function counter_go($id='0') {
		include_once 'includes/config.php';
		
		// Считываем значение с из таблицы
		$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
		or die("Could not connect: " . mysql_error());
		
		mysql_select_db('other_db', $connect_to_db)
		or die("Could not select DB: " . mysql_error());
		
		$qr_result = mysql_query("SELECT counter FROM counter WHERE id=" . $id)
		or die(mysql_error());
		
		$data = mysql_fetch_array($qr_result);
		$result = $data['counter'];
		
		// Увеличивваем значение на еденицу
		$result++;
		
		// Записываем значение обратно в таблицу
		$qr_result = mysql_query("UPDATE counter SET counter=" . $result . " WHERE id=" . $id)
		or die(mysql_error());
		
		// Закрыть соединение MySQL
		mysql_close($connect_to_db);
		
		// Выводи значение
		return $result;
	}
	function counterinfo_go($id) {
		include_once 'includes/config.php';
		
		// Считываем значение из таблицы
		$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
		or die("Could not connect: " . mysql_error());
		
		mysql_select_db('other_db', $connect_to_db)
		or die("Could not select DB: " . mysql_error());
		
		$qr_result = mysql_query("SELECT counter FROM counter WHERE id=" . $id)
		or die(mysql_error());
		
		$data = mysql_fetch_array($qr_result);
		$result = $data['counter'];
		
		// Закрыть соединение MySQL
		mysql_close($connect_to_db);
		
		// Выводи значение
		return $result;
	}
?>