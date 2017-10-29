<?php
	function comments_go()
	{
		$isComment = $_COOKIE["isComment"];
		
		// проверяем настройки
		if ($isComment != 'on') {
			return;
		}
		
		echo('<form action="add_comments.php" method="post" autocomplete="off" style="border-width: 1px; border-style: solid; width: 650px; padding: 5px; margin-bottom: 50px; margin-top: -30px;">');
		echo('<label title="Хранятся на вашем компьютере">Мои заметки:</label>');
		echo('<p><textarea name="text_comment" style="width: 100%; height: 100px; resize: none;">' . $_COOKIE["Comment"] . '</textarea>');
		echo('<p><input type="submit" class="button" value="Изменить"/>');
		echo('</form>');
	}
?>