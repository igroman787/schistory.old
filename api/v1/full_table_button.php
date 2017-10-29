<?php
	function full_table_button_go() {
		echo('<form action="add_setting_full_table.php" method="post" autocomplete="off" style="display: inline-block;">');
		if ($_COOKIE["isFullTable"] == 'on') {
			echo('<input type="hidden" name="isFullTable" value=""/>');
			echo('<p><input type="submit" class="button" title="вкл" value="Полная таблица"/>');
		} else {
			echo('<input type="hidden" name="isFullTable" value="on"/>');
			echo('<p><input type="submit" class="button_off" title="выкл" value="Полная таблица"/>');
		}
		echo('</form>');
	}
?>