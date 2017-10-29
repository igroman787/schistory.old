<?php
	$isFullTable = $_POST["isFullTable"];
	setcookie("isFullTable", $isFullTable, time()+63113904);
	header("Location: ".$_SERVER["HTTP_REFERER"]); // Делаем реридект обратно
?>