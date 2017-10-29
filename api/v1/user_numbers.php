<?php
	function user_numbers()
	{
		include_once 'includes/config.php';
		
		$connect_to_db = mysql_connect(HOST, USER, PASSWORD)
		or die("Could not connect: " . mysql_error());
		
		mysql_select_db('sc_history_db', $connect_to_db)
		or die("Could not select DB: " . mysql_error());
		
		$qr_result = mysql_query("SELECT * FROM nickname_uid")
		or die(mysql_error());
		
		$result = mysql_num_rows($qr_result);
		
		mysql_close($connect_to_db);
		return $result;
	}
?>