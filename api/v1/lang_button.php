<?php
	function lang_button_go() {
		echo('<form action="add_setting_lang.php" method="post" autocomplete="off" style="display: inline-block;">');
		if ($_COOKIE["language"] == 'ru') {
			echo('<input type="hidden" name="language" value=""/>');
			echo('<p><input type="submit" class="button" title="вкл" value="Перевод слов"/>');
		} else {
			echo('<input type="hidden" name="language" value="ru"/>');
			echo('<p><input type="submit" class="button_off" title="выкл" value="Перевод слов"/>');
		}
		echo('</form>');
	}
?>