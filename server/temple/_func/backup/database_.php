<?php
//--------�s���q�D,�]��ΤF��Ʈw
function uconnect($lc_db){
	/* �w�q�s����Ʈw�������Ѽ�  ��define()��ƩҶǦ^���O�`��,�O���i�ܪ���*/
    /*define('DB_USER', 'lib13'); 
    define('DB_PASSWORD', 'mypassword');
    define('DB_HOST', 'localhost');
    define('DB_DATABASE', 'lib13'); */
	$servername = "localhost" ;
	//�F��$username = "root" ;
		//$password = "9957!!@@##" ;
	//�ݥ�
	/*	$username = "aigulic2" ;
		$password = "9957!!@@##" ;*/
		$username = "enter30" ;
		$password = "ACE7880903zor" ;
	//�s����Ʈw
	$link = mysql_connect($servername,$username,$password)
		or die("�L�k�s����Ʈw".mysql_error()) ;
	//��θ�Ʈw	
	$select = mysql_select_db($lc_db,$link)
		or die("�L�k��θ�Ʈw".mysql_error()) ;
	return $link ;	
}

function ucode($ln_link="NOPARAMETERS"){
	
	if($ln_link=="NOPARAMETERS"){
	//	mysql_query("SET NAMES 'big5'") ;
	}
	else{
	//	mysql_query("SET NAMES 'big5'",$ln_link) ;
	}
}

?>	

