<?php
	function DisplayText($inputString)
	{
		$language = $_COOKIE["language"];
		
		// проверяем параметр языка
		if ($language != 'ru') {
			echo $inputString;
			return;
		}
		
		$unwantedContent = array("'", ".", ",", '"', '<th>', '</th>', '<tr>', '</tr>', '<td>', '</td>');
		
		// загружаем словарь
		$russian_strings = file_get_contents('russian.strings');
		
		// разбиваем входную строку на слова
		$outputString = $inputString;
		$inputString = str_replace($unwantedContent, '', $inputString);
		$InputString_m = explode(' ', $inputString);
		
		// ищем каждую строку в файле-переводчике.
		foreach ($InputString_m as &$item) {
			if (strlen($item)>0) {
				if (strpos($russian_strings, $item) != False) {
					// найти замену
					$strStartIndex = strpos($russian_strings, '"'.$item.'"=') + strlen('"'.$item.'"=');
					$str_n = substr($russian_strings, $strStartIndex);
					$strEndIndex = strpos($str_n, ';');
					$strReplace = substr($str_n, 0, $strEndIndex);
					$strReplace = str_replace('"', '', $strReplace);
					$outputString = str_replace($item, $strReplace, $outputString);
				}
			}
		}
		echo $outputString;
	}
	
	function FindUidFromNickname($nickname)
	{
		$webform = file_get_contents("http://gmt.star-conflict.com/pubapi/v1/userinfo.php?nickname=" . $nickname);
		if(strpos($webform, '"result":"ok"') !== False ) {
			$uidStartIndex = strpos($webform, '"uid":') + strlen('"uid":');
			$uid_n = substr($webform, $uidStartIndex);
			$uidEndIndex = strpos($uid_n, ',');
			
			$uid = substr($uid_n, 0, $uidEndIndex);
			
			$is_uid_fined = true;
		}
		if (!isset($is_uid_fined)) {
				$str = "<p>Пользователь '" . $nickname . "' не является жителем игровой вселенной Star Conflict.";
				DisplayText($str);
				FindNicknameInMySQL($nickname);
				exit();
			}
		return $uid;
	}
	function FindNicknameInMySQL($nickname) {
		// соединяемся с сервером базы данных
		$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
		or die("Could not connect: " . mysql_error());
		
		// подключаемся к базе данных
		$db_name = 'sc_history_db';
		mysql_select_db($db_name, $connect_to_db)
		or die("Could not select DB: " . mysql_error());
		
		// выбираем все значения из таблицы
		$qr_result = mysql_query("SELECT * FROM nickname_uid WHERE nickname='" . $nickname . "'");
		
		$result = mysql_num_rows($qr_result);
		if ($result == 0) {
			return;
		}
		
		$str = "<p>Однако данный никнейм был использован ранее:<p>";
		DisplayText($str);
		
		// Выводим таблицу
		DisplayText('<table border="1">');
		DisplayText('<thead>');
		DisplayText('<tr>');
		DisplayText('<th>uid</th>');
		DisplayText('<th>Old nickname</th>');
		DisplayText('</tr>');
		DisplayText('</thead>');
		DisplayText('<tbody>');
		
		while($data = mysql_fetch_array($qr_result)){ 
			echo('<tr>');
			echo('<td>' . '<a href="http://ts2.scorpclub.ru/api/v1/userinfo.php?uid=' . $data['uid'] . '" target="_blank">' . $data['uid'] . '</a></td>'); 
			echo('<td>' . $data['nickname'] . '</td>');
			echo('</tr>');
		}
		DisplayText('</tbody>');
		DisplayText('</table>');
		
		// закрываем соединение с сервером базы данных
		mysql_close($connect_to_db);
	}
?>