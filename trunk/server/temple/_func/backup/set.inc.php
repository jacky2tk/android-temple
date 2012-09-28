<?php
#****************************設定值***********************************start

#產品名稱固定顯示欄位
$GLOBALS["BASIC_FILED"]["Fld"] = Array("title","specif");
$GLOBALS["BASIC_FILED"]["Title"] = Array("產品名稱","規格");
$GLOBALS["BASIC_FILED"]["Percent"] = Array("0.60","0.40");//其顯示版面的欄位寬度所佔的比例(值加總需等於1)

#產品編號基本資料搜尋欄位
$GLOBALS["SEARCH_FILED"] = Array("serial","barcode");
$GLOBALS["SEARCH_FILED_Percent"] = Array("0.5","0.5");

#輸入產品編號&條碼編號 [ 加入 ]功能欄位
$GLOBALS["JOIN_FILED"] = Array("pserial","barcode");//第一碼為主要的關鍵值
$GLOBALS["JOIN_TITLE"] = Array("產品編號","條碼編號");
$GLOBALS["JOIN_TITLE"]["Percent"] = Array("0.50","0.50");//其欄位寬度所佔的比例(值加總需等於1)

#多公司別設定值
$GLOBALS["COMPANY"]["Exist"] = 0;     #是否有多公司或連鎖點 , 1為多公司 , 2為連鎖店 , 0為否

#基本幣別設定
$GLOBALS["VALUTA_ID"] = "NTD";#預設幣別
$GLOBALS["VALUTA_TITLE"] = "新台幣";#預設幣別
$GLOBALS["VALUTA_EXCHANG"] = "1";#預設幣別匯率
$GLOBALS["VALUTA"]["Exist"] = 0;     #是否有多國貨幣,1為是,0為否

#其他設定值
$GLOBALS["PAGE_NUM"] = 50;          #每頁顯示資料筆數
$GLOBALS["FLOAT_NUM"] = 2;          #小數點位數
$GLOBALS["ORDERPAYNOTICE"] = 1;     #系統是否有匯款通知功能 1-> 有 0->無
$GLOBALS["CUSTFLDYPE"] = 1;         #系統類別  1->一般電子商務 2->進銷存
$GLOBALS["SERIAL_UPDATE"] = "readonly"; #系統所有功能的代碼能不能被修改 , 若不行的話 輸入 "readonly" , 可以的話 輸入 ""
$GLOBALS["PWD"]='<span style="color:#c00;"><b>不更新，則不填</b></span>';#密碼編修顯示步驟提示

#****************************設定值***********************************end


#文字編輯器
if($TmpPath[1] == "incompletion" OR $TmpPath[1] == "finish") $GLOBALS["PATH_WEBDISK"] = "/".$TmpPath[1]."/".$TmpPath[2]."/upload/";
elseif($TmpPath[2] == "_plugin") $GLOBALS["PATH_WEBDISK"] = "/".$TmpPath[1]."/upload/";
else $GLOBALS["PATH_WEBDISK"] = "/upload/";


#系統遇定不得刪除的編號
$GLOBALS["DEPT_SERIAL"] = Array("P01"); #人員管理的部門不得刪除的編號
$GLOBALS["JOB_SERIAL"] = Array("P01"); #人員管理的職務不得刪除的編號
$GLOBALS["PERSONAL_SERIAL"] = Array("admin","kceverywhere"); #人員管理的代碼不得刪除的編號
$GLOBALS["CUS_CATE"] = Array(""); #會員分類管理不得刪除的編號
$GLOBALS["CUS_SERIAL"] = Array(""); #會員管理不得刪除的編號
?>
