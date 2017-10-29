<?php
	function manual_go() {
		include_once 'top100.php'; // загружаем top100
		include_once 'legend.php'; // загружаем legendSC
		include_once 'findusers.php'; // загружаем FindUsers
		
		echo('<head>');
		echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">');
		echo('<title>Manual for UserInfo | ts2.scorpclub.ru</title>');
		echo('<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>');
		echo('<link rel="stylesheet" type="text/css" href="/css/api.css">');
		echo('</head>');
		echo('<body>');
		
		echo('<div id="left">');
		findusers_go();
		echo('</div>');
		
		echo('<div id="right">');
		echo('Здесь можно просмотреть историю пилотов Star Conflict');
		echo('<a href="userinfojs.php?nickname=Igroman787" style="color: green; font-size: x-small">json</a>');
		include_once 'user_numbers.php';
		echo('<small>');
		echo(" (в базе ");
		echo(user_numbers());
		echo(" никнеймов)");
		echo('</small>');
		
		echo('<form action="/api/v1/userinfo.php" method="get" autocomplete="off">');
		echo('<p><input type="text" maxlength="20" placeholder="Поиск по Нику" name="nickname"/>');
		echo('<input type="submit" class="button" value="Найти"/>');
		echo('</form>');
		echo('<form action="/api/v1/userinfo.php" method="get" autocomplete="off">');
		echo('<p><input type="text" maxlength="20" placeholder="Поиск по uid" name="uid"/>');
		echo('<input type="submit" class="button" value="Найти"/>');
		echo('</form>');
		echo('</div>');
		
		echo('<div style="margin-top: 30px; margin-bottom: 50px">');
		echo('<div id="left">');
		echo('<font>100 самых активных пилотов SC за сутки</font>');
		top100_gamePlayed2_go();
		echo('</div>');
		echo('<div id="right">');
		echo('<font>100 лучших пилотов SC за сутки</font>');
		top100_kd2_go();
		echo('</div>');
		echo('</div>');

		
		footer_go();

		echo('</body>');
	}
	function clan_manual_go() {
		
		//echo('<style>');
		//echo('html { background-image: url(/includes/blue-boolean.png) !important; }');
		//echo('</style>');
		
		echo('<head>');
		echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">');
		echo('<title>Manual for ClanInfo | ts2.scorpclub.ru</title>');
		echo('<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>');
		echo('<link rel="stylesheet" type="text/css" href="/css/api.css">');
		echo('</head>');
		echo('<body>');
		
		echo('<div id="left">');
		//echo('Здесь можно найти пилотов Star Conflict по определенным параметрам');
		//echo('<p><form action="/api/v1/findusers.php" method="get" autocomplete="off" style="display: inline;">');
		//echo('<input type="text" maxlength="100" size="50" placeholder="Поиск по Параметрам:" name="search" value="' . $_GET['search'] . '"/>');
		//echo('<input type="submit" class="button" value="Найти"/>');
		echo('</div>');
		
		echo('<div id="right">');
		echo('Здесь можно просмотреть историю корпорации Star Conflict');
		//echo('<a href="userinfojs.php?nickname=Igroman787" style="color: green; font-size: x-small">json</a>');
		echo('<form action="/api/v1/claninfo.php" method="get" autocomplete="off">');
		echo('<p><input type="text" maxlength="20" placeholder="Поиск по Названию" name="clanName"/>');
		echo('<input type="submit" class="button" value="Найти"/>');
		echo('</form>');
		echo('<form action="/api/v1/claninfo.php" method="get" autocomplete="off">');
		echo('<p><input type="text" maxlength="20" placeholder="Поиск по Тэгу" name="clanTag"/>');
		echo('<input type="submit" class="button" value="Найти"/>');
		echo('</form>');
		echo('</div>');
		
	}
?>