<?php
	function user_history_go()
	{
		// ввод дополнительных функций
		include_once('new_user.php');
		
		// принимаем входящие параметры
		$nickname = $_GET['nickname'];
		$uid = $_GET['uid'];
		$isFullTable = $_COOKIE["isFullTable"];

		// определяем начальные данные
		$db_name = 'sc_history_db';
		//$db_table_to_show_from_nickname = 'nickname_uid';
		//$db_table_translate_ru = 'translate_ru';

		// соединяемся с сервером базы данных
		$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
		or die("Could not connect: " . mysql_error());

		// подключаемся к базе данных
		mysql_select_db($db_name, $connect_to_db)
		or die("Could not select DB: " . mysql_error());
		
		// если uid не указан, узнаем его
		if (!isset($uid)) {
			$uid = FindUidFromnickname($nickname);
		}

		// выбираем все значения из таблицы uid
		$db_table_to_show = 'uid_' . $uid;
		$qr_result = mysql_query("SELECT * FROM " . $db_table_to_show . " ORDER BY date")
		or UserNotFound($nickname);
		
		// Выводим ссылку на сайт Итали
		echo('<table border="1" style="margin-bottom: 10px;">');
		echo('<thead>');
		echo('<tr>');
		echo('<th>Детальная статистика игрока с инфографиками: <a href="http://it4ly.altervista.org/star-conflict-players-statistics" target="_blank">it4ly.altervista.org</a></th>');
		echo('</tr>');
		echo('</tbody>');
		echo('</table>');

		// выводим на страницу сайта заголовки HTML-таблицы
		DisplayText('<table border="1" style="margin-bottom: 50px;">');
		DisplayText('<thead>');
		DisplayText('<tr>');
		DisplayText('<th>date</th>');
		DisplayText('<th>uid</th>');
		DisplayText('<th>nickname</th>');
		DisplayText('<th>clanName</th>');
		DisplayText('<th>clanTag</th>');
		DisplayText('<th>K/D</th>');
		DisplayText('<th>KDA</th>');
		DisplayText('<th>WinRate</th>');
		DisplayText('<th>W/L</th>');
		DisplayText('<th>K/D+</th>');
		DisplayText('<th>KDA+</th>');
		DisplayText('<th>WinRate+</th>');
		DisplayText('<th>W/L+</th>');
		DisplayText('<th>gamePlayed+</th>');
		full_table($isFullTable);
		DisplayText('</tr>');
		DisplayText('</thead>');
		DisplayText('<tbody>');

		// выводим в HTML-таблицу все данные клиентов из таблицы MySQL 
		while($data = mysql_fetch_array($qr_result)){ 
			$totalKill = intval($data['totalKill']);
			$totalDeath = intval($data['totalDeath']);
			$totalAssists = intval($data['totalAssists']);
			$gameWin = intval($data['gameWin']);
			$gamePlayed = intval($data['gamePlayed']);
			
			$totalKill_old = intval($old_totalKill);
			$totalDeath_old = intval($old_totalDeath);
			$totalAssists_old = intval($old_totalAssists);
			$gameWin_old = intval($old_gameWin);
			$gamePlayed_old = intval($old_gamePlayed);
			
			$kd = $totalKill / $totalDeath;
			$kd = round($kd, 2);
			$kda = ($totalKill + $totalAssists) / $totalDeath;
			$kda = round($kda, 2);
			$wr = $gameWin / $gamePlayed;
			$wr = round($wr, 2);
			$wl = $gameWin / ($gamePlayed - $gameWin);
			$wl = round($wl, 2);
			
			$kd2_den = $totalDeath - $totalDeath_old;
			$kd2_num = $totalKill - $old_totalKill;
			if ($kd2_den == 0) {
				$kd2_den = 1;
			}
			$kd2 = $kd2_num / $kd2_den;
			$kd2 = round($kd2, 2);
			
			$kda2_num = ($totalKill - $totalKill_old) + ($totalAssists - $totalAssists_old);
			$kda2_den = $totalDeath - $totalDeath_old;
			if ($kda2_den == 0) {
				$kda2_den = 1;
			}
			$kda2 = $kda2_num / $kda2_den;
			$kda2 = round($kda2, 2);
			
			$wr2_num = $gameWin - $gameWin_old;
			$wr2_den = $gamePlayed - $gamePlayed_old;
			if ($wr2_den == 0) {
				$wr2_den = 1;
			}
			$wr2 = $wr2_num / $wr2_den;
			$wr2 = round($wr2, 2);
			
			$wl2_num = $gameWin - $gameWin_old;
			$wl2_den = ($gamePlayed - $gamePlayed_old) - ($gameWin - $gameWin_old);
			if ($wl2_den == 0) {
				$wl2_den = 1;
			}
			$wl2 = $wl2_num / $wl2_den;
			$wl2 = round($wl2, 2);
			
			$gamePlayed2 = $gamePlayed - $gamePlayed_old;
			
			$old_totalKill = $data['totalKill'];
			$old_totalDeath = $data['totalDeath'];
			$old_totalAssists = $data['totalAssists'];
			$old_gameWin = $data['gameWin'];
			$old_gamePlayed = $data['gamePlayed'];
			
			$date = new DateTime($data['date']);
			$date->modify('-1 day');
			$date = $date->format('Y-m-d');
			echo('<tr>');
			echo('<td>' . $date . '</td>');
			echo('<td>' . $data['uid'] . '</td>');
			echo('<td>' . $data['nickname'] . '</td>');
			echo('<td>' . $data['clanName'] . '</td>');
			echo('<td>' . $data['clanTag'] . '</td>');
			echo('<td>' . $kd . '</td>');
			echo('<td>' . $kda . '</td>');
			echo('<td>' . $wr*100 . '%</td>');
			echo('<td>' . $wl . '</td>');
			echo('<td>' . $kd2 . '</td>');
			echo('<td>' . $kda2 . '</td>');
			echo('<td>' . $wr2*100 . '%</td>');
			echo('<td>' . $wl2 . '</td>');
			echo('<td>' . $gamePlayed2 . '</td>');
			full_table_lines($isFullTable, $data);
			echo('</tr>');
		}

		echo('</tbody>');
		echo('</table>');
		echo('<p><p>');

		// закрываем соединение с сервером базы данных
		mysql_close($connect_to_db);
	}
	function full_table($isFullTable) {
		if ($isFullTable != 'on') {
			return;
		}
		DisplayText('<th>effRating</th>');
		DisplayText('<th>karma</th>');
		DisplayText('<th>prestigeBonus</th>');
		DisplayText('<th>gamePlayed</th>');
		DisplayText('<th>gameWin</th>');
		DisplayText('<th>totalAssists</th>');
		DisplayText('<th>totalBattleTime</th>');
		DisplayText('<th>totalDeath</th>');
		DisplayText('<th>totalDmgDone</th>');
		DisplayText('<th>totalHealingDone</th>');
		DisplayText('<th>totalKill</th>');
		DisplayText('<th>totalVpDmgDone</th>');
	}
	function full_table_lines($isFullTable, $data) {
		if ($isFullTable != 'on') {
			return;
		}
		echo('<td>' . $data['effRating'] . '</td>');
		echo('<td>' . $data['karma'] . '</td>');
		echo('<td>' . (float)$data['prestigeBonus']*100 . '%</td>');
		echo('<td>' . $data['gamePlayed'] . '</td>');
		echo('<td>' . $data['gameWin'] . '</td>');
		echo('<td>' . $data['totalAssists'] . '</td>');
		echo('<td>' . $data['totalBattleTime'] . '</td>');
		echo('<td>' . $data['totalDeath'] . '</td>');
		echo('<td>' . $data['totalDmgDone'] . '</td>');
		echo('<td>' . $data['totalHealingDone'] . '</td>');
		echo('<td>' . $data['totalKill'] . '</td>');
		echo('<td>' . $data['totalVpDmgDone'] . '</td>');
	}
	
	function UserNotFound($nickname) {
		new_user_go($nickname);
		exit();
	}
?>