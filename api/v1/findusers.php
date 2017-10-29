<?php
	function findusers_header($sort_list, $inline = False) {
		if ($inline) {
			echo('<a href="http://ts2.scorpclub.ru/api/v1/userinfo.php" class="button"/>Главная</a>');
		}
		echo('<form action="/api/v1/findusers.php" method="get" autocomplete="off" style="display: inline;">');
		echo('<input type="text" maxlength="100" size="50" placeholder="Поиск по Параметрам:" name="search" value="' . $_GET['search'] . '"/>');
		echo('<input type="submit" class="button" value="Найти"/>');
		if (!$inline) {
			echo('<div>');
		}
		echo('<select name="sort" style="margin-left: 3px;">');
		echo('<option disabled>Сортировать по:</option>');
		foreach ($sort_list as &$value) {
			echo('<option value="' . $value . '">' . $value . '</option>');
		}
		echo('</select>');
		
		echo('<select name="DESC" style="margin-left: 3px;">');
		echo('<option disabled>Сортировка:</option>');
		echo('<option selected value="">Прямая</option>');
		echo('<option value=" DESC">Обратная</option>');
		echo('</select>');
		
		echo('<select name="limit" style="margin-left: 3px;">');
		echo('<option disabled>Вывод:</option>');
		echo('<option selected value="50">50</option>');
		echo('<option value="100">100</option>');
		echo('<option value="200">200</option>');
		echo('<option value="400">400</option>');
		echo('<option value="800">800</option>');
		echo('<option value="1600">1600</option>');
		echo('<option value="3200">3200</option>');
		echo('<option value="6400">6400</option>');
		echo('<option value="12800">12800</option>');
		echo('<option value="25600">25600</option>');
		echo('</select>');
		echo('</form><p>');
		if (!$inline) {
			echo('</div>');
		}
	}
	
	function findusers_form($sort_list) {
		echo('Здесь можно найти пилотов Star Conflict по определенным параметрам');
		echo('<a href="http://ts2.scorpclub.ru/api/v1/findusersjs.php?search=clanTag%3D%27scorp%27&sort=nickname&limit=50" style="color: green; font-size: x-small">json</a><p>');
		findusers_header($sort_list);
	}
	
	function usersNotFound() {
		echo('Неправильный поисковый запрос "' . $_GET['search'] . '"');
		
		echo('<p><details>');
		echo('<summary style="cursor: pointer">Пример:</summary>');
		echo(file_get_contents('help_findusers.txt'));
		echo('</details>');
		
		echo('<p><details>');
		echo('<summary style="cursor: pointer">Допустимые параметры:</summary>');
		echo('<p>nickname, uid, kd, kd2, kda, kda2, wr, wr2, wl, wl2, effRating, effRating2, karma, karma2, prestigeBonus, prestigeBonus2, gamePlayed, gamePlayed2, gameWin, gameWin2, totalAssists, totalAssists2, totalBattleTime, totalBattleTime2, totalDeath, totalDeath2, totalDmgDone, totalDmgDone2, totalHealingDone, totalHealingDone2, totalKill, totalKill2, totalVpDmgDone, totalVpDmgDone2, clanName, clanTag');
		echo('</details>');
		
		exit();
	}
	
	function findusers_go() {
		include_once 'includes/config.php'; // загружаем HOST, USER, PASSWORD
		include_once 'additional_functions.php'; // загружаем дополнительный Функции
		$sort_str = 'nickname, uid, kd, kd2, kda, kda2, wr, wr2, wl, wl2, effRating, effRating2, karma, karma2, prestigeBonus, prestigeBonus2, gamePlayed, gamePlayed2, gameWin, gameWin2, totalAssists, totalAssists2, totalBattleTime, totalBattleTime2, totalDeath, totalDeath2, totalDmgDone, totalDmgDone2, totalHealingDone, totalHealingDone2, totalKill, totalKill2, totalVpDmgDone, totalVpDmgDone2, clanName, clanTag';
		$sort_str = str_replace(' ','',$sort_str);
		$sort_list = explode(',', $sort_str);
		
		if (!isset($_GET['search']) and !isset($_GET['sort']) and !isset($_GET['limit']) and !isset($_GET['DESC'])) {
			findusers_form($sort_list);
		}
		else {
			
			findusers_header($sort_list, True);
			
			$search = $_GET['search'];
			$sort = $_GET['sort'];
			$limit = $_GET['limit'];
			$DESC = $_GET['DESC'];
			
			foreach ($sort_list as &$value) {
				if (strpos($search, $value) > -1) {
					$column_str .= $value . ',';
					#array_push($column_list, $value);
				}
			}
			$column_str = substr($column_str, 0, strlen($column_str)-1);
			$column_list = explode(',', $column_str);
			
			
			$dop_oreder = str_replace(',',', ',$column_str);
			
			
			// определяем начальные данные
			$db_name = 'sc_history_db';
			
			// соединяемся с сервером базы данных
			$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
			or die("Could not connect: " . mysql_error());
			
			// подключаемся к базе данных
			mysql_select_db($db_name, $connect_to_db)
			or die("Could not select DB: " . mysql_error());
			
			// выбираем все значения из таблицы
			$qr_result_num = mysql_query("SELECT * FROM top100 WHERE " . $search . " ORDER BY " . $sort .', '  . $dop_oreder . $DESC)
			or usersNotFound();
			$qr_result = mysql_query("SELECT * FROM top100 WHERE " . $search . " ORDER BY " . $sort .', '  . $dop_oreder . $DESC . " LIMIT " . $limit)
			or usersNotFound();
			#echo("SELECT * FROM top100 WHERE " . $search . " ORDER BY " . $sort .', '  . $dop_oreder . $DESC . " LIMIT " . $limit);
			echo('найдено ' . mysql_num_rows($qr_result_num) . ' совпадений.');
			
			// выводим на страницу сайта заголовки HTML-таблицы
			DisplayText('<table border="1">');
			DisplayText('<thead>');
			DisplayText('<tr>');
			
			DisplayText('<th>uid</th>');
			DisplayText('<th>nickname</th>');
			foreach ($column_list as &$value) {
				DisplayText('<th>' . $value . '</th>');
			}
			

			DisplayText('</tr>');
			DisplayText('</thead>');
			DisplayText('<tbody>');
			
			// выводим в HTML-таблицу все данные клиентов из таблицы MySQL 
			while($data = mysql_fetch_array($qr_result)){ 
				echo('<tr>');
				
				echo('<td>' . '<a href="http://ts2.scorpclub.ru/api/v1/userinfo.php?uid=' . $data['uid'] . '" target="_blank">' . $data['uid'] . '</a></td>'); 
				echo('<td>' . $data['nickname'] . '</td>');
				foreach ($column_list as &$value) {
					echo('<td>' . $data[$value] . '</td>');
				}
				
				echo('</tr>');
			}
			
			DisplayText('</tbody>');
			DisplayText('</table>');
			
			// закрываем соединение с сервером базы данных
			mysql_close($connect_to_db);
			
			include_once 'footer.php'; // загружаем Footer
			footer_go();
			exit();
		}
	}
	if (isset($_GET['search']) and isset($_GET['sort']) and isset($_GET['limit'])) {
			header ("Content-Type: text/html; charset=utf-8");
			echo('<link rel="stylesheet" type="text/css" href="/css/api.css">');
			echo('<title>FindUsers | ts2.scorpclub.ru</title>');
			findusers_go();
		}
?>