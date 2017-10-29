<?php
	$nickname = $_POST["nickname"];
	echo('<script src="https://www.google.com/recaptcha/api.js"></script>');
	if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) {
		$secret = '6LcSrR8UAAAAAG8LMqNL8dSRON7l96L_R4Itcg3Y';
		$response = $_POST['g-recaptcha-response'];
		$rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response");
		$arr = json_decode($rsp, TRUE);
		if($arr['success']) {
			include_once 'caption.php'; // загружаем Заголовок
			include_once 'header.php'; // загружаем Шапку
			caption_go(); // Отображаем заголовок
			searcher_go(); // Отображаем шапку
			add_new_user_from_MySQL($nickname);
			echo('<p>История пользователя ' . $nickname . ' начала записываться.');
		}
	} else {
		echo('invalid captcha');
	}
	function add_new_user_from_MySQL($nickname)
	{
		$service_port = 4800;
		$address = gethostbyname('ts2.scorpclub.ru');
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$result = socket_connect($socket, $address, $service_port);
		$in = '<nickname>' . $nickname . '</nickname>';
		socket_write($socket, $in, strlen($in));
		socket_close($socket);
	}
?>