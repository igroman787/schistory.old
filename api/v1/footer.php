<?php
	function footer_go() {
		include_once 'counter.php'; // загружаем Counter
		
		echo('<footer>');
		
		echo('<div class="poster">');
		echo('Другие проекты');
		echo('<div class="descr">');
		echo('<a href="https://dl.dropboxusercontent.com/u/1741337/Star%20Conflict/Trading/TradeChat_Items.html" target="_blank">Объем рынка по товарам</a>');
		echo(' (автор: ');
		echo('<a href="https://forum.star-conflict.ru/index.php?/topic/56405-torgovyi-chat-v-vebe/" target="_blank">weegee</a>');
		echo(')');
		echo('</div>');
		echo('</div>');
		
		echo('<div class="poster">');
		echo('Музыка');
		echo('<div class="descr">');
		echo('<audio controls preload="metadata" title="Keep It Close - Seven Lions">');
		echo('<source src="audio/music.mp3" type="audio/mpeg">');
		echo('Тег audio не поддерживается вашим браузером. ');
		echo('<a href="audio/music.mp3">Скачайте музыку</a>');
		echo('</audio>');
		echo('</div>');
		echo('</div>');
		
		echo('<div class="poster">');
		echo('О нас');
		echo('<div class="descr">');
		echo('Обсуждение данного проекта <a href="https://forum.star-conflict.ru/index.php?/topic/56632-istoriya-sc" target="_blank">на форуме SC</a>');
		echo('<p>Количество просмотров HTML: ' . counter_go());
		echo('<p>Количество просмотров JSON: ' . counterinfo_go('1'));
		$df = disk_free_space("/");
		$ds = disk_total_space("/");
		$freespace = round($df/$ds*100, 2);
		$loadspace = 100 - $freespace;
		if ($loadspace > 95) {
			$color = 'red';
		} elseif ($loadspace > 75) {
			$color = 'darkorange';
		} else {
			$color = 'limegreen';
		}
		echo('<p>Состояние БД: Использовано <font style="color: ' . $color . ';">' . $loadspace . '%</font>');
		echo('</div>');
		echo('</div>');
		
		echo('</footer>');
	}
?>