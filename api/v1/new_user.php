<?php
	function new_user_go($nickname) {
		echo('<script src="https://www.google.com/recaptcha/api.js" async defer></script>');
		echo('<form action="add_new_user.php" method="post" autocomplete="off" style="border-width: 1px; border-style: solid; width: 650px; padding: 5px;">');
		echo('Пользователь не найден.');
		echo('<p>Хотите ли вы, чтобы мы начали писать историю данного пользователя?');
		echo('<div class="g-recaptcha" data-sitekey="6LcSrR8UAAAAAOxE2CoCVuuntrkvGvdN1TZwqsiT"></div>');
		echo('<p><input type="text" maxlength="20" placeholder="Добавить Никнейм" name="nickname" value="' . $nickname . '"/>');
		echo('<input type="submit" class="button" style="display: inline-block;" value="Добавить"/>');
		echo('</form>');
	}
?>