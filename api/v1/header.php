<?php
	function header_go()
	{
		include_once('lang_button.php');
		include_once('comm_button.php');
		include_once('full_table_button.php');
		echo('<a href="http://ts2.scorpclub.ru/api/v1/userinfo.php" class="button"/>Главная</a>');
		echo('<form action="/api/v1/userinfo.php" method="get" autocomplete="off" style="display: inline-block;">');
		echo('<input type="text" maxlength="20" placeholder="Поиск по Нику" name="nickname"/>');
		echo('<input type="submit" class="button" value="Найти"/>');
		echo('</form>');
		lang_button_go();
		comm_button_go();
		full_table_button_go();
	}
	function searcher_go()
	{
		echo('<a href="http://ts2.scorpclub.ru/api/v1/userinfo.php" class="button"/>Главная</a>');
		echo('<form action="/api/v1/userinfo.php" method="get" autocomplete="off" style="display: inline-block;">');
		echo('<input type="text" maxlength="20" placeholder="Поиск по Нику" name="nickname"/>');
		echo('<input type="submit" class="button" value="Найти"/>');
		echo('</form>');
	}
?>