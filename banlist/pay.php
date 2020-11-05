<?php
	define('GUARD', true);
	include 'include/core.php';
	
	if((Configuration::$main['m_purse'] == NULL) || (Configuration::$main['m_cost'] == NULL) || (Configuration::$main['m_secret_key'] == NULL)){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /?error=1");
		exit();
	}
	
	DataBase::connect();
	$ban = mysql_query('SELECT * FROM `superban` WHERE banid='.$_POST['banid'].'');
	
	if(mysql_num_rows($ban) == 0){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /?error=2");
		exit();
	}
	
	$info = 'Приобретение платного разбана для id = '.$_POST['banid'].'!';

	echo '
	<html> 
	<head>
		<title>Переадресация на сайт платёжной системы...</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta http-equiv="Content-Language" content="ru">
		<meta http-equiv="Pragma" content="no-cache">
		<meta name="robots" content="noindex,nofollow">
	</head>
	<body>
		<form name="oplata" method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">
			<input type="hidden" name="LMI_PAYMENT_NO" value="'.$_POST['banid'].'">
			<input type="hidden" name="LMI_PAYMENT_DESC_BASE64" value="'.base64_encode($info).'">
			<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.Configuration::$main['m_cost'].'">
			<input type="hidden" name="LMI_PAYEE_PURSE" value="'.Configuration::$main['m_purse'].'">
			<input type="hidden" name="banid" value="'.$_POST['banid'].'">
			<noscript><input type="submit" value="Нажмите, если не хотите ждать!" onclick="document.oplata.submit();"></noscript>
		</form>
		<script language="Javascript" type="text/javascript">
			document.oplata.submit();
		</script>
	</body>
	</html>';
?>