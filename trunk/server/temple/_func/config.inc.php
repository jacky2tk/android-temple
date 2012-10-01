<?php
include dirname(__FILE__)."/db.fun.php";
//include dirname(__FILE__)."/compo.fun.php";	// 放到 Summerhost 免空會出現問題，暫時不載入此檔

$TmpPath = explode("/",$_SERVER["PHP_SELF"]);


# MySQL
if ($_SERVER['SERVER_NAME'] == "localhost") {
	$GLOBALS["SQL_HOST"] = "localhost";
} else {
	$GLOBALS["SQL_HOST"] = "localhost";
}

// 依照目前 Server 名稱, 選擇對應的 MySQL 登入設定
if ($_SERVER["HTTP_HOST"] == "localhost") {
    $GLOBALS["SQL_NAME"] = "temple";
    $GLOBALS["SQL_USER"] = "root";
    $GLOBALS["SQL_PASS"] = "1";
} else {
    // $GLOBALS["SQL_NAME"] = "sum_10838368_danding_book";
    // $GLOBALS["SQL_USER"] = "sum_10838368";
    // $GLOBALS["SQL_PASS"] = "chenboy";
	
	$GLOBALS["SQL_NAME"] = "temple";
    $GLOBALS["SQL_USER"] = "root";
    $GLOBALS["SQL_PASS"] = "1";
}


#DataBase

$GLOBALS["DB_TEMPLE"] = "temple";


$GLOBALS["PAGE_NUM"] = 50;      #每頁顯示資料筆數
$GLOBALS["FLOAT_NUM"] = 0;      #小數點位數

$GLOBALS["PATH_PRODUCRT_BACK"] = "../../upload/product";#商品圖片
$GLOBALS["PATH_PRODUCRT_FRONT"] = "./upload/product";#商品圖片



#文字編輯器
if($TmpPath[1] == "incompletion" OR $TmpPath[1] == "finish") $GLOBALS["PATH_WEBDISK"] = "/".$TmpPath[1]."/".$TmpPath[2]."/upload/";
elseif($TmpPath[2] == "_plugin") $GLOBALS["PATH_WEBDISK"] = "/".$TmpPath[1]."/upload/";
else $GLOBALS["PATH_WEBDISK"] = "/upload/";

$GLOBALS["PERSONAL"] = array("admin");

?>
