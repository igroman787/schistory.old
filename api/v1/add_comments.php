<?php
	$text_comment = $_POST["text_comment"];
	setcookie("Comment", $text_comment, time()+63113904);
	header("Location: ".$_SERVER["HTTP_REFERER"]); // Делаем реридект обратно
?>