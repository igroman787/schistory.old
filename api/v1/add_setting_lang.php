<?php
	$language = $_POST["language"];
	setcookie("language", $language, time()+63113904);
	header("Location: ".$_SERVER["HTTP_REFERER"]); // Делаем реридект обратно
?>