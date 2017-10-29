<?php
	function comm_button_go() {
		echo('<form action="add_setting_comment.php" method="post" autocomplete="off" style="display: inline-block;">');
		if ($_COOKIE["isComment"] == 'on') {
			echo('<input type="hidden" name="isComment" value=""/>');
			echo('<p><input type="submit" class="button" title="вкл" value="Мои заметки"/>');
		} else {
			echo('<input type="hidden" name="isComment" value="on"/>');
			echo('<p><input type="submit" class="button_off" title="выкл" value="Мои заметки"/>');
		}
		echo('</form>');
	}
?>