<?php
include dirname(__FILE__)."/db.fun.php";

$TmpPath = explode("/",$_SERVER["PHP_SELF"]);


# MySQL
$GLOBALS["SQL_HOST"] = "localhost";

if($_SERVER["HTTP_HOST"]=="127.0.0.1"){
    $GLOBALS["SQL_NAME"] = "localhost";
    $GLOBALS["SQL_USER"] = "root";
    $GLOBALS["SQL_PASS"] = "111111";
}else{
    $GLOBALS["SQL_NAME"] = "localhost";
    $GLOBALS["SQL_USER"] = "root";
    $GLOBALS["SQL_PASS"] = "111111";
}


#DataBase

$GLOBALS["DB_USER"] = "fb_user";
$GLOBALS["DB_RECORD"] = "fb_record";


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
