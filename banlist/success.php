<?php 
	define('GUARD', true);
	include 'include/core.php';
	DataBase::connect();
	
	if($_POST['LMI_PREREQUEST'] == 1) 
	{
		$ban = mysql_query('SELECT * FROM `superban` WHERE banid='.$_POST['banid'].'');
	
		if(mysql_num_rows($ban) == 0){
			echo 'Неверный id игрока!';
			exit;
		}
		if(trim($_POST['LMI_PAYMENT_AMOUNT']) != Configuration::$main['m_cost']){
			echo 'Неверная сумма!';
			exit;
		} else if((trim($_POST['LMI_PAYEE_PURSE']) != Configuration::$main['m_purse'])){
			echo 'Неверный кошелек получателя!';
			exit;
		}
		echo 'YES';
	} else {
		$secret_key = Configuration::$main['m_secret_key'];
		$common_string = $_POST['LMI_PAYEE_PURSE'].$_POST['LMI_PAYMENT_AMOUNT'].$_POST['LMI_PAYMENT_NO'].
		$_POST['LMI_MODE'].$_POST['LMI_SYS_INVS_NO'].$_POST['LMI_SYS_TRANS_NO'].
		$_POST['LMI_SYS_TRANS_DATE'].$secret_key.$_POST['LMI_PAYER_PURSE'].$_POST['LMI_PAYER_WM'];
		$hash = strtoupper(md5($common_string));
		if($hash!=$_POST['LMI_HASH']) exit;
		
		$delete = mysql_query ("UPDATE `superban` SET unbantime='-1' WHERE banid='".$_POST['banid']."'") or die(mysql_error());
	}
?>