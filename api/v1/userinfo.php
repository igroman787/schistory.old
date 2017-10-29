<?php
	// Ввод дополнительных библиотек
	mb_internal_encoding("UTF-8"); // Назначаем кодировку
	include_once 'includes/config.php'; // загружаем HOST, USER, PASSWORD
	include_once 'additional_functions.php'; // загружаем дополнительный Функции
	include_once 'caption.php'; // загружаем Заголовок
	include_once 'header.php'; // загружаем Шапку
	include_once "manual.php"; // загружаем Мануал
	//include_once "user_brief_info.php" // загружаем Кратку инфу пользователя
	include_once 'user_history.php'; // загружаем историю пользователя
	include_once 'comments.php'; // загружаем комментарии пользователя
	include_once 'footer.php'; // загружаем Footer
	
	// выводим Мануал, если нет параметров
	if (!isset($_GET['nickname']) and !isset($_GET['uid'])) {
		manual_go();
		exit();
	}
	
	// Отображаем заголовок
	caption_go();
	
	// Отображаем шапку
	header_go();
	
	// Отображаем историю пользователя
	user_history_go();
	
	// Отображаем комментарии
	comments_go();
	
	// Отображаем подвал
	footer_go();
	
?>