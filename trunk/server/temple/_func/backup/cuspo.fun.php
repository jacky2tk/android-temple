<?php
#----------------2009.07.09-----------------------------
#說明:找尋sys1788位置
#輸入:網址陣列,尋找值
#輸出:../或空值
#-------------------------------------------------------
function Gettmppath($TmpPath,$sekey='sys1788')
{
    $m=count($TmpPath);
    $TmpP=array_reverse($TmpPath);
    $data='';
    $nowpath=0;
    for($i=0;$i<$m;$i++){
        if($TmpP[$i]==$sekey){
            $nowpath=$i;
            break;
        }
    } 
    if($nowpath>0) for($i=0;$i<$nowpath;$i++) $data .='../';
    return $data;
}

#----------------2009.08.13-----------------------------
#說明:刪除資料與檔案資料夾
#輸入:serial值,檔案路徑
#輸出:無
#-------------------------------------------------------
function Delvaldata($id,$Path)
{   
    $IdList = explode(",",$id);
    $m=count($IdList);
    for($i=0;$i<$m;$i++)
    {
        DB_DELETE($GLOBALS["DB_NEWS"]," serial='$IdList[$i]'");
        if($Path!='') DelFDir($Path.$IdList[$i]);     
    }
}

#----------------2009.04.07-----------------------------
#說明:刪除資料與(資料夾)
#輸入:資料表,欄位，欄位值(以,串連),資料夾路徑,1->MD5資料,編碼值
#輸出:無
#-------------------------------------------------------
function DelDF($db,$fld,$Id,$path="",$endata=0,$code='s')
{
    if($Id){
        $IdList = explode(",",$Id);
        $m=count($IdList);
        for($i=0;$i<$m;++$i){
            DB_DELETE($db,$fld."='".$IdList[$i]."'");
            if($path!="") {
                if($endata==1) $IdList[$i]=md5($IdList[$i].$code);
                DelFiles($path.$IdList[$i]);            
            }
        }
    }
}

#----------------2009.07.07-----------------------------
#說明:檔案下載
#輸入:檔案路徑
#輸出:下載檔案
#-------------------------------------------------------
function dl_file($file){

   //First, see if the file exists
   if (!is_file($file)) { die("<b>404 File not found!</b>"); }

   //Gather relevent info about file
   $len = filesize($file);
   $filename = basename($file);
   $file_extension = strtolower(substr(strrchr($filename,"."),1));

   //This will set the Content-Type to the appropriate setting for the file
  switch( $file_extension ) {
     case "pdf": $ctype="application/pdf"; break;
     case "exe": $ctype="application/octet-stream"; break;
     case "zip": $ctype="application/zip"; break;
     case "doc": $ctype="application/msword"; break;
     case "xls": $ctype="application/vnd.ms-excel"; break;
     case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
     case "gif": $ctype="image/gif"; break;
     case "png": $ctype="image/png"; break;
     case "jpeg":
     case "jpg": $ctype="image/jpg"; break;
     case "mp3": $ctype="audio/mpeg"; break;
     case "wav": $ctype="audio/x-wav"; break;
     case "mpeg":
     case "mpg":
     case "mpe": $ctype="video/mpeg"; break;
     case "mov": $ctype="video/quicktime"; break;
     case "avi": $ctype="video/x-msvideo"; break;

     //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
     case "php":
     case "htm":
     case "html":
     case "txt": die("<b>Cannot be used for ". $file_extension ." files!</b>"); break;

     default: $ctype="application/force-download";
   }

   //Begin writing headers
   header("Pragma: public");
   header("Expires: 0");
   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   header("Cache-Control: public");
   header("Content-Description: File Transfer");
   
   //Use the switch-generated Content-Type
   header("Content-Type: $ctype");

   //Force the download
   $header="Content-Disposition: attachment; filename=".$filename.";";
   header($header );
   header("Content-Transfer-Encoding: binary");
   header("Content-Length: ".$len);
   @readfile($file);
   exit;
}

#----------------2009.08.17-----------------------------
#說明:回傳主類別
#輸入:類別id
#輸出:主類別id
#-------------------------------------------------------
function GetMcate($uid)
{
    $res=DB_QUERY("select serial,uid from ".$GLOBALS["DB_PROCATE"]." where serial='$uid'");
    $row=mysql_fetch_assoc($res);
    while($row['uid']>0)
    {
        $res=DB_QUERY("select serial,uid from ".$GLOBALS["DB_PROCATE"]." where serial='$row[uid]'");
        $row=mysql_fetch_assoc($res);
    }
    return $row['serial'];
}

#----------------2009.08.25-----------------------------
#說明:回傳產品頁或分類頁
#輸入:類別id,產品id
#輸出:連結網址
#-------------------------------------------------------
function GetMcatePage($uid,$serial='')
{
    $id=GetMcate($uid);
    switch($id)
    {
        case '29'://鼓組
            if($serial=='') $url='products.php?u='.$uid;
            else $url='products_01.php?u='.$uid.'&s='.$serial;
            break;
        case '30'://其他
            if($serial=='') $url='products11.php?u='.$uid;
            else $url='products_111.php?u='.$uid.'&s='.$serial;
            break;
        case '31'://架子
            if($serial=='') $url='products22.php?u='.$uid;
            else $url='products_222.php?u='.$uid.'&s='.$serial;
            break;
        default:break;
    }
    return $url;
}

#----------------2009.08.17-----------------------------
#說明:代言人where列
#輸入:代言人姓名排序區間
#輸出:where值
#-------------------------------------------------------
function Spokemen($str)
{
    switch($str)
    {
        case 'A-E':
            $where=" and (title like 'A%' or title like 'B%' or title like 'C%' or title like 'D%' or title like 'E%')";
            break;
        case 'F-I':
            $where=" and (title like 'F%' or title like 'I%' )";
            break;
        case 'J-M':
            $where=" and (title like 'J%' or title like 'K%' or title like 'L%' or title like 'M%')";
            break;
        case 'N-Q':
            $where=" and (title like 'N%' or title like 'O%' or title like 'P%' or title like 'Q%' )";
            break;
        case 'R-U':
            $where=" and (title like 'R%' or title like 'S%' or title like 'T%' or title like 'U%' )";
            break;
        case 'V-Z':
            $where=" and (title like 'V%' or title like 'W%' or title like 'X%' or title like 'Y%' or title like 'Z%')";
            break;
        default:
            $where='';
            //$where=" and (title like 'A%' or title like 'B%' or title like 'C%' or title like 'D%' or title like 'E%')";
            break;
    }
    return $where;
}

#----------------2008.12.15-----------------------------
#說明：設定頁數(與FPagebar一起使用)
#輸入：總頁數,目前頁數,列數
#輸出：開始頁，結束頁
#-------------------------------------------------------
function SetPage($show,$page,$num)
{
    
    $mypage=array();
	if($show<=$num)
	{
		$mypage['p']=1;
		$mypage['n']=$show;
	}
	else
	{
		if($page-5<=1)
	 	{
	   	    $mypage['p']=1;
			$mypage['n']=$num;
		}
		else
		{
			if($show-($page-5)>=($num-1))
			{
				$mypage['p']=$page-5;
				$mypage['n']=$page-5+($num-1);
			}
			else
			{
				$mypage['p']=$show-($num-1);
				$mypage['n']=$show;
			}
		}
	}
	return $mypage;
}

#----------------2008.12.15-----------------------------
#說明：設定頁數
#輸入：SQL,目前頁數,每頁比數,額外連結,css字型,每列顯示幾頁
#輸出：頁數列
#-------------------------------------------------------
function FPagebar($SQL,$NowPage,$Num,$STR="",$CSS="",$Barnum="10")
{
     
    //if($CSS!='') $css=$CSS;
    //else $css="fpagebar";
     
    $RES = DB_QUERY($SQL);
	$Total = mysql_num_rows($RES);
	$_SESSION['TP']=$Total;
	if($Total>0)
	{
    	$Total_page = ceil($Total/$Num);
        //$PageL='<span class="page_title">共 <span class="page_bar">'.$Total.'</span> 筆</span>';
        //$PageR='<span class="page_title">共 <span class="page_bar">'.$Total_page.'</span> 頁</sapn>';
        $mypage=SetPage($Total_page,$NowPage,$Barnum);
    	#上下頁連結
    	//$FirstLink = '<a href="'.$_SERVER["PHP_SELF"]."?ipage=0".$STR.'" ><span class="page_pn"><<&nbsp;First</span></a> ';
        $BackLink = '<a href="'.$_SERVER["PHP_SELF"]."?ipage=".(($NowPage!=0)?$NowPage-1:0).$STR.'" ><span class="page_pn"><&nbsp;Prev</span></a> ';
        $NextLink = ' <a href="'.$_SERVER["PHP_SELF"]."?ipage=".(($NowPage!=($Total_page-1))?$NowPage+1:$NowPage).$STR.'" ><span class="page_pn">Next&nbsp;></span></a> ';
        //$LastLink = '<a href="'.$_SERVER["PHP_SELF"]."?ipage=".($Total_page-1).$STR.'" ><span class="page_pn">Last&nbsp;>></span></a> ';
        
    	$restr='&nbsp;';
    	for($p=$mypage['p'];$p<=$mypage['n'];$p++)
    	{
    		if($p!=($NowPage+1)) $restr.='<a href="'.$_SERVER["PHP_SELF"].'?ipage='.($p-1).$STR.'" ><span class="page_num">'.$p.'</span></a>&nbsp;';
    		else $restr.='<a href="'.$_SERVER["PHP_SELF"].'?ipage='.($p-1).$STR.'"><span class="page_nums">'.$p.'</span></a>&nbsp;';
    	}
    	$PageBar = '<table width="100%" border="0"><tr><td width="18%">'.$PageL.'</td><td align="center">'.$FirstLink.$BackLink.$restr.$NextLink.$LastLink.'</td><td width="15%" align="right">'.$PageR.'</td></table>';
    }
	return $PageBar;
}

#----------------2009.08.20-----------------------------
#說明:刪除資料與(資料夾)-宇韻特有
#輸入:欄位值(以,串連),資料夾路徑
#輸出:無
#-------------------------------------------------------
function DelDFSP($Id,$path="")
{
    if($Id){
        $IdList = explode(",",$Id);
        $m=count($IdList);
        for($i=0;$i<$m;++$i){
            DB_DELETE($GLOBALS["DB_PRODUCT"],"serial='".$IdList[$i]."'");
            DB_DELETE($GLOBALS["DB_PROFILE"],"uid='".$IdList[$i]."'");
            if($path!="") {
                DelFiles($path.$IdList[$i]);
                DelFiles($path.md5($IdList[$i].'s'));                            
            }
        }
    }
}

#----------------2009.08.20-----------------------------
#說明:回傳類別
#輸入:類別id
#輸出:到主類別所有類別
#-------------------------------------------------------
function GetMcateList($uid)
{
    $data=array();
    $res=DB_QUERY("select serial,uid,title from ".$GLOBALS["DB_PROCATE"]." where serial='$uid'");
    $row=mysql_fetch_assoc($res);
    $data[]=$row['title'];
    while($row['uid']>0)
    {
        $res=DB_QUERY("select serial,uid,title from ".$GLOBALS["DB_PROCATE"]." where serial='$row[uid]'");
        $row=mysql_fetch_assoc($res);
        $data[]=$row['title'];
    }
    array_pop($data);
    $data=array_reverse($data);
    return implode(' > ',$data);
}

#----------------2009.08.20-----------------------------
#說明：產品點閱紀錄
#輸入：產品id
#輸出：無
#-------------------------------------------------------
function ProClick($serial,$title)
{
    $date=date("Y-m-d");
    $res=DB_QUERY("select serial from ".$GLOBALS["DB_STATS"]." where serial='$serial' and date='".$date."'");
    if(mysql_num_rows($res)>0) DB_QUERY("update ".$GLOBALS["DB_STATS"]." set click=click+1 where serial='$serial' and date='$date' "); 
    else DB_INSERT($GLOBALS["DB_STATS"],array("serial","date","title","click","company"),array($serial,$date,$title,1,'everywhere'));
}

?>