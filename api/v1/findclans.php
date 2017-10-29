<?php
	
	// ¬вод дополнительных библиотек
	mb_internal_encoding("UTF-8"); // Ќазначаем кодировку
	include_once 'includes/config.php'; // загружаем HOST, USER, PASSWORD
	include_once 'additional_functions.php'; // загружаем дополнительный ‘ункции
	include_once 'caption.php'; // загружаем «аголовок
	//include_once 'header.php'; // загружаем Ўапку
	//include_once "manual.php"; // загружаем ћануал
	//include_once 'comments.php'; // загружаем комментарии пользовател€
	//include_once 'footer.php'; // загружаем Footer
	
	// ќтображаем заголовок
	caption_go();
	
	// ќтображаем шапку
	//header_go();
	
	// принимаем вход€щие параметры
	$clanName = $_GET['clanName'];
	$clanTag = $_GET['clanTag'];
	
	// определ€ем начальные данные
	$db_name = 'sc_clan_db';
	$db_table_to_show = 'corporations_history';
	
	// соедин€емс€ с сервером базы данных
	$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
	or die("Could not connect: " . mysql_error());
	
	// подключаемс€ к базе данных
	mysql_select_db($db_name, $connect_to_db)
	or die("Could not select DB: " . mysql_error());
	
	// если clanName не указан, ищем по clanTag
	if (!isset($clanName)) {
		$sql = "SELECT * FROM " . $db_table_to_show . " WHERE BINARY clanTag='" . $clanTag . "'";
	} else {
		$sql = "SELECT * FROM " . $db_table_to_show . " WHERE BINARY clanName='" . $clanName . "'";
	}
	
	// выбираем все значени€ из таблицы uid
	$qr_result = mysql_query($sql)
	or die("Could not found: " . mysql_error());

	// выводим на страницу сайта заголовки HTML-таблицы
	echo('<table border="1" style="margin-bottom: 50px;">');
	echo('<thead>');
	echo('<tr>');
	echo('<th>date</th>');
	echo('<th>clanName</th>');
	echo('<th>clanTag</th>');
	echo('<th>avgEffRating</th>');
	echo('<th>avgKarma</th>');
	echo('<th>avgPrestigeBonus</th>');
	echo('<th>avgGamePlayed</th>');
	echo('<th>avgGameWin</th>');
	echo('<th>avgTotalAssists</th>');
	echo('<th>avgTotalBattleTime</th>');
	echo('<th>avgTotalDeath</th>');
	echo('<th>avgTotalDmgDone</th>');
	echo('<th>avgTotalHealingDone</th>');
	echo('<th>avgTotalKill</th>');
	echo('<th>avgTotalVpDmgDone</th>');
	echo('</tr>');
	echo('</thead>');
	echo('<tbody>');
	echo('</tr>');

	// выводим в HTML-таблицу все данные клиентов из таблицы MySQL 
	while($data = mysql_fetch_array($qr_result)){ 
		
		$date = new DateTime($data['date']);
		$date->modify('-1 day');
		$date = $date->format('Y-m-d');
		echo('<tr>');
		echo('<td>' . $date . '</td>');
		echo('<td>' . $data['clanName'] . '</td>');
		echo('<td>' . $data['clanTag'] . '</td>');
		echo('<td>' . intval($data['effRating']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['karma']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['prestigeBonus']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['gamePlayed']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['gameWin']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['totalAssists']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['totalBattleTime']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['totalDeath']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['totalDmgDone']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['totalHealingDone']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['totalKill']) / intval($data['number']) . '</td>');
		echo('<td>' . intval($data['totalVpDmgDone']) / intval($data['number']) . '</td>');
		echo('</tr>');
	}

	echo('</tbody>');
	echo('</table>');
	echo('<p><p>');

	// закрываем соединение с сервером базы данных
	mysql_close($connect_to_db);
	
?>