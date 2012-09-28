<?php
//--------連結通道,也選用了資料庫
function uconnect($lc_db){
	/* 定義連接資料庫的有關參數  此define()函數所傳回的是常數,是不可變的數*/
    /*define('DB_USER', 'lib13'); 
    define('DB_PASSWORD', 'mypassword');
    define('DB_HOST', 'localhost');
    define('DB_DATABASE', 'lib13'); */
	$servername = "localhost" ;
	//東王$username = "root" ;
		//$password = "9957!!@@##" ;
	//需尼
	/*	$username = "aigulic2" ;
		$password = "9957!!@@##" ;*/
		$username = "enter30" ;
		$password = "ACE7880903zor" ;
	//連結資料庫
	$link = mysql_connect($servername,$username,$password)
		or die("無法連接資料庫".mysql_error()) ;
	//選用資料庫	
	$select = mysql_select_db($lc_db,$link)
		or die("無法選用資料庫".mysql_error()) ;
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

