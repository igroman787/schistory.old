<?php
	$isComment = $_POST["isComment"];
	setcookie("isComment", $isComment, time()+63113904);
	header("Location: ".$_SERVER["HTTP_REFERER"]); // Делаем реридект обратно
?>