<?php
function uimage_reduce($lc_src,$lc_dst,$ln_width=0,$ln_height=0){
	/* $lc_src 圖片來源
	   $lc_dst 壓縮後的圖片存放路徑
	   $ln_width 設定之寬度
	   $ln_height 設定之高度*/
	$src = imagecreatefromjpeg($lc_src);
	// 取得來源圖片長寬
	$src_w = imagesx($src);
	$src_h = imagesy($src);
	if($ln_width<>0&&$ln_height==0){
		$thumb_w = $ln_width;
		$thumb_h = intval($src_h / $src_w * $ln_width);
	}
	else if($ln_width==0&&$ln_height<>0){
		$thumb_h = $ln_height;
		$thumb_w = intval($src_w / $src_h * $ln_height);
	}
	else if($ln_width<>0&&$ln_height<>0){
		$thumb_w = $ln_width;
		$thumb_h = $ln_height;
	}
	else{
		if($src_w>$src_h){
			$thumb_w = 200;
			$thumb_h = intval($src_h / $src_w * 200);
		}
		else{
			$thumb_h = 200;
			$thumb_w = intval($src_w / $src_h * 200);
		}
	}
	// 建立縮圖
	$thumb = imagecreatetruecolor($thumb_w, $thumb_h);
	// 開始縮圖
	$m_pled = imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
	// 儲存縮圖到指定 thumb 目錄
	$m_jpeg = imagejpeg($thumb, $lc_dst);
	// 複製上傳圖片到指定 images 目錄
	if(!($m_pled)||!($m_jpeg)){
		return false ;
	}
	else{
		return true ; 
	}
//	copy($lc_src,$lc_dst);
}
#轉換網址
function GoUrl($Url){
    echo "<script>\n\rdocument.location=\"$Url\";\n\r</script>\n\r";
}

function GoUrl2($Url){
	echo '<script language="JavaScript">
  window.location.replace("'.$Url.'");
  </script>';
}

#警告視窗
function Alert($Msg){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo "<script language=\"JavaScript\">\n\rJavascript:alert(\"$Msg\");\n\r</script>\n\r";
}

#關閉視窗
function WindowClose(){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo "<script language=\"JavaScript\">\n\rJavascript:window.close();\n\r</script>\n\r";
}

#上傳檔案含複製
function UpFile($Files,$FDir,$Name){
	if(!is_uploaded_file($Files["tmp_name"])) return;
	if(!is_dir($FDir)) mkdir($FDir,0777);
	$FileList=opendir($FDir);
	
	while($DFiles=readdir($FileList)){
        if(strstr($FDir."/".$DFiles,$FDir."/".$Name)){
            DelFDir("$FDir/$DFiles");
        }
    }
    closedir($FileList);
    
	$SName=strtolower(substr($Files["name"],(strrpos($Files["name"],".")+1)));
	$Name.=".$SName";
	$tmp=move_uploaded_file($Files["tmp_name"],"$FDir/$Name");
	return $Name;
}


#上傳小圖  若NewName空白的話 , 本身裁切成指定尺寸 ; 若有指定 , 裁切成新檔 ; 原始檔不變
function UpImg($FDir,$Name,$NewName='',$size_w,$size_h=0){

	//	exit;
	if(!empty($NewName)){
	   DelFile($FDir,$NewName);
	}

	$pic = GetFiles($FDir,$Name);
	$src = GetImageSize($pic);
	// get the source image's widht and hight
	$src_w = $src[0];
	$src_h = $src[1];
	// if old_img_size < new_img_size => new_img_size= old_img_size
	if($size_w > $src_w) $size_w = $src_w;
	if($size_h > $src_h) $size_h = $src_h;

	// assign thumbnail's widht and hight
	if(!$size_h){
		$thumb_w = $size_w;
		$thumb_h = intval($src_h * $size_w / $src_w);
	}else{
		if($src_w > $src_h){
			$thumb_w = $size_w;
			$thumb_h = intval($src_h / $src_w * $size_w);
			if($thumb_h > $size_h){
				$h = $size_w - 1;
				while($thumb_h > $size_h){
					$thumb_w = $h;
					$thumb_h = intval($src_h / $src_w * $h);
					$h--;
				}
			}
		}else{
			$thumb_h = $size_h;
			$thumb_w = intval($src_w / $src_h * $size_h);
		}
	}
	// if you are using GD 1.6.x, please use imagecreate()
	$thumb = imagecreatetruecolor($thumb_w, $thumb_h);
	// start resize
	// save thumbnail
	$cmds=explode(".",$pic);

	switch($src[2]){
		case "1":
			$src = imagecreatefromgif($pic);
			imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
			imagegif($thumb, $FDir."/".($NewName==''?$Name:$NewName).".".$cmds[5]);
			break;
		case "2":
			$src = imagecreatefromjpeg($pic);
			imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
			imagejpeg($thumb, $FDir."/".($NewName==''?$Name:$NewName).".".$cmds[5]);
			break;
		case "3":
			$src = imagecreatefrompng($pic);
			imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
			imagepng($thumb, $FDir."/".($NewName==''?$Name:$NewName).".".$cmds[5]);
			break;
		default:
			exit;
	}
}

#複製檔案OR資料夾(包含底下資料)到指定路徑底下
function CopyDir($source,$target){
    if(!is_dir($target)) MkDir($target,0777); 
    if(is_dir($source)){    
        $tmp = explode("/",$source);
        $path = $target."/".$tmp[count($tmp)-1];
        if(!is_dir($path)) MkDir($path,0777);    
        $dir = dir($source);
        while($row = $dir->read()){
            if($row != "." && $row !=".."){                
                if(is_dir($source."/".$row)){
                    CopyDir($source."/".$row , $path);
                }else{
                    copy($source."/".$row , $path."/".$row);
                }
            }

        }
    }
    if(is_file($source))    copy($source,$target);
}

function UpImgs($Path,$Old_Img,$New_Img,$Size_w,$Size_h=0){
    //取出圖片原始大小
    $Pic = GetFiles($Path,$Old_Img);
    if(!$Pic) return;
    $Src = GetImageSize($Pic);
    list($Src_w,$Src_h) = Array($Src[0],$Src[1]);
    
    //取副檔名
    $tmp = explode(".",$Pic);
    $Cmds = $tmp[count($tmp)-1];
    
    if($Src_w <= $Size_w AND ($Src_h <= $Size_h OR !$Size_h)){
        //原圖尺寸小於新圖尺寸則不執行縮圖
        copy($Pic,$Path."/".$New_Img.".".$Cmds);
    }else{
        //計算等比例縮放後的圖片尺寸
        if(!$Size_h){
            $Thumb_w = $Size_w;
            $Thumb_h = intval($Src_h / $Src_w * $Size_w);
        }else{
            if($Src_w > $Src_h){
                $Thumb_w = $Size_w;
                $Thumb_h = intval($Src_h / $Src_w * $Size_w);
                if($Thumb_h > $Size_h){
                    $w = $Size_w - 1;
                    while($Thumb_h > $Size_h){
                        $Thumb_w = $w;
                        $Thumb_h = intval($Src_h / $Src_w * $w);
                        $w--;
                    }
                }
            }else{
                $Thumb_h = $Size_h;
                $Thumb_w = intval($Src_w / $Src_h * $Size_h);
                if($Thumb_w > $Size_w){
                    $h = $Size_h - 1;
                    while($Thumb_w > $Size_w){
                        $Thumb_h = $h;
                        $Thumb_w = intval($Src_w / $Src_h * $h);
                        $h--;
                    }
                }
            }
        }
        //開始裁切圖片
        $Thumb = imagecreatetruecolor($Thumb_w, $Thumb_h);
                 
        switch($Src[2]){
            case "1":
                
                $Thumb    = imagecreatetruecolor($Thumb_w, $Thumb_h);
                $white = imagecolorallocate($Thumb, 255, 255, 255);
                imagefill($Thumb, 0, 0, $white);
                imagecolortransparent($Thumb,$white);
            
            
                $Old_Path = imagecreatefromgif($Pic);
    			imagecopyresampled($Thumb, $Old_Path, 0, 0, 0, 0, $Thumb_w, $Thumb_h, $Src_w, $Src_h);
    			imagegif($Thumb,$Path."/".$New_Img.".".$Cmds);
                break;
            case "2";
                $Old_Path = imagecreatefromjpeg($Pic);
    			imagecopyresampled($Thumb, $Old_Path, 0, 0, 0, 0, $Thumb_w, $Thumb_h, $Src_w, $Src_h);
    			imagejpeg($Thumb,$Path."/".$New_Img.".".$Cmds);
                break;
            case "3";
                $Old_Path = imagecreatefrompng($Pic);
    			imagecopyresampled($Thumb, $Old_Path, 0, 0, 0, 0, $Thumb_w, $Thumb_h, $Src_w, $Src_h);
    			imagepng($Thumb,$Path."/".$New_Img.".".$Cmds);
                break;
            default: break;
        }
        imagedestroy($Thumb);
    }
}

#刪除檔案
function DelFile($Path,$Name){
	if(!is_dir($Path)) return;
	$FileList=opendir($Path);
	while($Files=readdir($FileList)) if(strstr($Path."/".$Files,$Path."/".$Name.".")) DelFDir("$Path/$Files");
	closedir ($FileList);
}

#刪除目錄
function DelFDir($Path, $DB=""){
  	if(is_dir($Path) || is_file($Path)){
		if(!is_dir($Path)){
			unlink($Path);
      if( $DB=="Y" )  DB_DELETE($GLOBALS["DB_FILES"],"CONCAT(path, name)='$Path'");
			return $Path;
		}
		$FileList=opendir($Path);
		$Str = array();
		while($Files=readdir($FileList)){
			if($Files!=".." && $Files!="."){
        if( $DB=="Y" )  DB_DELETE($GLOBALS["DB_FILES"],"CONCAT(path, name)='".DelFDir("$Path/$Files","Y")."'");
        else DelFDir("$Path/$Files");
      }
		}
		closedir ($FileList);
		rmdir($Path);
		if( $DB=="Y" )  DB_DELETE($GLOBALS["DB_FILES"], "CONCAT(path, name)='$Path'");
	}
}

#取得檔案
function GetFiles($Path,$Name){
    if(!is_dir($Path)) return;
    $FileList=opendir($Path);
    while($Files=readdir($FileList)) if(strstr($Path."/".$Files,$Path."/".$Name.".")) break;
    closedir ($FileList);
    if($Files) return "$Path/$Files";
}

//文字編輯器模組
function fckeditor($name,$value,$width,$hight,$type=''){
    if(substr(myUrl(1),-3) == "php"){
        $Path = "../../";
        $Val = true;
        $Url = myUrl(2);
    }else{
        $Path = "";
        $Val = false;
        $Url = myUrl();
    }
    include_once($Path."_plugin/FCKeditor/fckeditor.php") ;

    $FCKeditor = new FCKeditor($name);
    $FCKeditor->BasePath = $Path.'_plugin/FCKeditor/';
    $FCKeditor->Config['ImageBrowser'] = $Val;
    $FCKeditor->Config['FlashBrowser'] = $Val;
    $FCKeditor->Config['LinkBrowser'] = $Val;
    $FCKeditor->Config['BaseHref'] = $Url."/";
    $FCKeditor->Width = $width;
    $FCKeditor->Height = $hight;
    $FCKeditor->Value = $value;
    if($type!=''){$FCKeditor->ToolbarSet = "Basic";}
    return $FCKeditor->CreateHtml();
}

#回前頁
function GoBack($Num){
        echo "<script language='JavaScript'>window.location='javascript:history.go($Num)';</script>";
}

#分頁 Bar ( 要算頁數的資料,目前頁數,每頁顯示資料數,css字型,額外連結 )
function PageBar($SQL,$NowPage,$Num,$Css,$STR=""){
    $RES = DB_QUERY($SQL);
    $Total = mysql_num_rows($RES);
    #目前頁數
    $Total_page = ceil($Total/$Num);
    $Page["left"] = "<b>".($NowPage+1)."</b> / ".$Total_page;
    #上下頁連結
    if(($NowPage-1)<0) $BackPage = 0;
    else $BackPage = $NowPage - 1;
    $BackLink = $_SERVER["PHP_SELF"]."?ipage=".$BackPage.$STR;
    if(($NowPage+1)==$Total_page) $NextPage = $Total_page - 1;
    else $NextPage = $NowPage + 1;
    if(!$Total_page) $NextPage = 0;
    $NextLink = $_SERVER["PHP_SELF"]."?ipage=".$NextPage.$STR;
    
    ##紀錄列表的問號參數值
	$_SESSION["Ipage"]="ipage=".$NowPage.$STR;
    #頁數下拉式選單
    $PageList = "<select name=\"ipage\" class=\"$Css\" onChange=javascript:jumppage(this.value);>";
    for($i=0;$i<$Total_page;$i++){
        $PageList .= "<option value=\"$i\"";
        if($NowPage == $i) $PageList .= " selected>".($i+1)."</option>";
        else $PageList .= ">".($i+1)."</option>";
    }
    $PageList .= "</option>";
    #跳頁function
    $Url = $_SERVER["PHP_SELF"]."?ipage=";
    $Bar = "<script language=\"JavaScript\">
            <!--
                function jumppage(obj) {
                    document.location='".$Url."'+obj+'".$STR."';
                }
            //-->
            </script>";
    $PageBar = "<tr>
                    <td width=\"33%\" height=\"25\" align=\"left\" class=\"$Css\">頁數：$Page[left] 頁</td>
                    <td width=\"33%\" height=\"25\" align=\"center\" class=\"$Css\"><a href=\"$BackLink\" class=\"$Css\">上一頁</a> | <a href=\"$NextLink\" class=\"$Css\">下一頁</a></td>
                    <td width=\"33%\" height=\"25\" align=\"right\" class=\"$Css\">跳至第 $PageList 頁</td>
                </tr>".$Bar;
    return $PageBar;
}

#權限選項 ( 資料庫名稱,條件,欄位名稱,資料索引值, 權限種類 )
function KeyenList($DB,$WHERE,$Filed,$FNum,$Comp, $power = array("檢視", "新增", "編修", "刪除"),$dis=""){
    if(!count($power)) $power = array("檢視", "新增", "編修", "刪除");
	$RES = DB_QUERY("SELECT * FROM ".$DB." WHERE ".$WHERE);
	$ROW = mysql_fetch_array($RES);
	$Keygn = explode(",",$ROW[$Filed]);
	for($i=0; $i<count($power); $i++){
    $$chk="";
		if(substr($Keygn[($FNum)],$i,1) == "1"){
			$chk = "chk".$i;
			$$chk = "checked";
		}
    $STR .= "<input type=\"checkbox\" name=\"".$Filed."[$Comp][".($FNum)."][".$i."]\" value=\"1\" ".$$chk." ".$dis.">".$power[$i]."";
  }
  $STR .= "<input type=\"hidden\" name=\"".$Filed."[$Comp][".($FNum)."][num]"."\" value=\"".count($power)."\">";

  return $STR;
}


#取權限 ( 資料庫名稱,登入者ID,欄位名稱,資料索引值,取第幾個值 )
function GetKeygn($DB,$ID,$Filed,$FNum,$Num){
    $RES = DB_QUERY("SELECT * FROM ".$DB." WHERE id='".$ID."'");
    $ROW = mysql_fetch_array($RES);
    if($ROW["uid"] != "0"){
        $Keygn = explode(",",$ROW[$Filed]);
        if(substr($Keygn[($FNum-1)],$Num-1,1) == "1") return "true";
        else return "false";
    }
    return "true";
}

#組合權限 ( 權限欄位名 )
function KeygnMerge( $fld , $comp ){
	for($i=0;$i<count($_POST[$fld][$comp]);$i++){
		strlen($power)?$power .= ",":$power = "";
		for($j=0;$j<$_POST[$fld][$comp][$i]["num"];$j++){
			if( $_POST[$fld][$comp][$i][$j] )  $power .= $_POST[$fld][$comp][$i][$j];
			else $power .= "0";
		}
	}
	return $power;
}

#截字
function CuttingStr( $Str, $Ct ){
  if( mb_strlen( $Str, "UTF-8" )>$Ct ){
    return  mb_substr( $Str, 0, $Ct, "UTF-8" )."...";
  }else  return $Str;
}

#取得Id值
function GetId($Id){
    $IdList = explode(",",$Id);
    $id = $IdList[0];
    $IdList = substr(join(",",$IdList),strlen($id)+1);
    return Array($id,$IdList);
}

#新增or編修資料
function SaveData($id,$DB,$Filed,$Value,$Where=""){

    $Time = $id?Array("modifydate","modify"):
                Array("createdate","creat");
    $Filed = array_merge($Filed, $Time);
    array_push($Value, date("Y-m-d H:i:s"), $_SESSION['admin']['user']);
    $id?DB_UPDATE($DB,$Filed,$Value,"NO_='".$id."' ".$Where):DB_INSERT($DB,$Filed,$Value);
    return $id?$id:mysql_insert_id();
}

#跳頁
function GoTo($Id,$page=""){
    list($id,$IdList) = GetId($Id);
    if(!$id){
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
              <script language=\"JavaScript\" type=\"text/JavaScript\">
                if(confirm(\"資料已新增完成, 是否繼續新增??\")) window.location.href = \"".$_SERVER["PHP_SELF"]."\"
              </script>";
    }elseif(!strlen($IdList)) Alert("資料已編修完成!!");
    else GoUrl($_SERVER["PHP_SELF"]."?id=".$IdList); 
    GoUrl($page?$page:(substr($_SERVER["PHP_SELF"],0,-8)."list.php"));
}

#跳頁
function GoToEdit($Id,$page=""){
    list($id,$IdList) = GetId($Id);
    if(!$id){
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
              <script language=\"JavaScript\" type=\"text/JavaScript\">
                if(confirm(\"資料已新增完成, 是否繼續新增??\")) window.location.href = \"".$_SERVER["PHP_SELF"]."\"
              </script>";
    }
    elseif(strlen($IdList)) GoUrl($_SERVER["PHP_SELF"]."?id=".$IdList);
    GoUrl($page?$page:(substr($_SERVER["PHP_SELF"],0,-8)."list.php"));
}

#刪除資料
function DelItem($DB,$Fld,$Id){
    if($Id){
        $IdList = explode(",",$Id);
        for($i=0;$i<count($IdList);$i++){
            DB_DELETE($DB,$Fld."='".$IdList[$i]."'");
        }
    }
}

#更新排序
function SaveItem($DB,$Fld,$Id,$Val){
    $IdList = explode(",",$Id);
    $ValList = explode(",",$Val);
    for($i=0;$i<count($IdList);$i++){
        DB_UPDATE($DB,Array("sort"),Array($ValList[$i]),$Fld."='".$IdList[$i]."'");
    }
}

#刪除不限層次分類及資料 ( 分類資料庫,物件資料庫,分類id,檔案存放路徑 )
function DelData($DB,$DB1,$ID,$BOM="",$FPATH=""){
    #刪除自己的資料
    DB_DELETE($DB,"id='".$ID."'");
    #刪除自己下線資料
    $RES = DB_QUERY("SELECT * FROM $DB1 WHERE uid='$ID'");
    while($ROW=mysql_fetch_array($RES)){
        DB_DELETE($DB1,"id='".$ROW["id"]."'");
        $path = $FPATH.$ROW["id"];
        if(is_dir($path)) DelFDir($path);
        if($BOM){
            DB_DELETE($GLOBALS["DB_BOM"],"myid='".$ROW["id"]."'");
            DB_DELETE($GLOBALS["DB_BOM"],"pid='".$ROW["id"]."'");
        }
    }
    #刪除自己下線全部資料
    $last = 0;
    $i = 0;
    $RES = DB_QUERY("SELECT * FROM $DB WHERE uid='".$ID."'");
    while($ROW=mysql_fetch_array($RES)){
            $myid[] = $ROW;
    }
    while(!$last AND count($myid) AND $i < count($myid)){
        $uid = $myid[$i]['id'];
        $RES = DB_QUERY("SELECT * FROM $DB WHERE uid='".$myid[$i]['id']."'");
        $num = mysql_num_rows($RES);
        while($ROW=mysql_fetch_array($RES)){
            $uid = $ROW["id"];
            $RES = DB_QUERY("SELECT * FROM $DB WHERE uid='$ROW[id]'");
        }
        #刪除分類
        DB_DELETE($DB,"id='$uid'");
        $RES = DB_QUERY("SELECT * FROM $DB1 WHERE uid='$uid'");
        #刪除資料
        while($ROW=mysql_fetch_array($RES)){
            DB_DELETE($DB1,"id='$ROW[id]'");
            $path = $FPATH.$ROW["id"];
            if(is_dir($path)) DelFDir($path);
            if($BOM){
                DB_DELETE($GLOBALS["DB_BOM"],"myid='".$ROW["id"]."'");
                DB_DELETE($GLOBALS["DB_BOM"],"pid='".$ROW["id"]."'");
            }
        }
        if(!$num) $i++;
        if($i > count($myid)) $last = 1;
    }
}

#找出目前的AUTOINDEX
function AutoIndex($DB){
    $RES = DB_QUERY("SHOW TABLE STATUS LIKE '$DB'");
    $ROW = mysql_fetch_array($RES);
    return $ROW["Auto_increment"];
}

#上n層的網址 (如：$num=1表上一層)
function myUrl($num=0){
  $exp=explode("/", $_SERVER["PHP_SELF"]);
  for($i=0; $i<=$num; $i++)
    array_pop($exp);
  $imp=implode("/", $exp);

  if( $_SERVER["HTTPS"]=="on" )  $http="https";
    else  $http="http";
    
  return $http."://".$_SERVER["HTTP_HOST"].$imp;
}

function SendMail($To, $Subject, $Body, $FromMail="", $ToName="", $Cc="", $Bcc=""){
    $Headers .= "Mime-Version: 1.0\n";
    $Headers .= "Content-Type: text/html; charset=utf-8\n";
    $Headers .= "Content-Transfer-Encoding: base64\n";

    if( !$FromMail ){
    $sql="SELECT * FROM $GLOBALS[DB_COMP]";
    $res=DB_QUERY($sql);
    $comp=mysql_fetch_array($res);
      $From="=?UTF-8?B?".base64_encode($comp["comp_name_tw"])."?=<$comp[email]>";
  }else  $From="=?UTF-8?B?".base64_encode($FromMail)."?=<$FromMail>";

    $Headers .= "From: $From\n";
    if($ToName) $Headers .= "To: =?UTF-8?B?".base64_encode($ToName)."?=<$To>\n";
    if($Cc) $Headers .= "Cc: $Cc\n";
    if($Bcc) $Headers .= "Bcc: $Bcc\n";
    if(@Mail($To, "=?UTF-8?B?".base64_encode($Subject)."?=", base64_encode($Body), $Headers)) return True;
}

function SendMail2($To, $Subject, $Body, $FromMail="", $ToName="", $Cc="", $Bcc=""){
	$Headers .= "Mime-Version: 1.0\n";
	$Headers .= "Content-Type: text/html; charset=utf-8\n";

	if( !$FromMail ){
    $sql="SELECT * FROM $GLOBALS[DB_COMP]";
    $res=DB_QUERY($sql);
    $comp=mysql_fetch_array($res);
      $From="=?UTF-8?B?".base64_encode($comp["comp_name_tw"])."?=<$comp[email]>";
  }else  $From="=?UTF-8?B?".base64_encode($FromMail)."?=<$FromMail>";

	$Headers .= "From: $From\n";
	if($ToName) $Headers .= "To: =?UTF-8?B?".base64_encode($ToName)."?=<$To>\n";
	if($Cc) $Headers .= "Cc: $Cc\n";
	if($Bcc) $Headers .= "Bcc: $Bcc\n";
	if(@Mail($To, "=?UTF-8?B?".base64_encode($Subject)."?=", $Body, $Headers)) return True;
}

###給$_FILES["file"] 將$_FILES["file"]["name"]["i"] => $_FILES["file"]["i"]["name"]
function reArrayFiles(&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    $name_keys = array_keys($file_post["name"]);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$name_keys[$i]][$key] = $file_post[$key][$name_keys[$i]];
        }
    }
    return $file_ary;
}

#取得陣列索引值
function GetArrKey($Arr,$key){
    for($i=0;$i<count($Arr);$i++){
        if($Arr[$i] == $key){
            return $i;
            break;
        }
    }
}

#取得圖片解析度
function GetImgSize($Path,$Name){
    $pic = GetFiles($Path,$Name);
    $src = GetImageSize($pic);
    $src_w = $src[0];
    $src_h = $src[1];
    return $src_w.",".$src_h;
}

#取得中文大寫數字
function getChineseNumber( $money ){
  $cnArr = array( "零", "壹", "貳", "參", "肆", "伍", "陸", "柒", "捌", "玖" );
  $unitArr = array( "", "", "拾", "佰", "仟", "萬", "拾", "佰", "仟", "億", "拾", "佰", "仟" );

  for( $j=strlen($money), $i=0; $j>=1;  $j--, $i++ ){
    $num = substr($money, $i, 1);       //數字
    $cn = $cnArr[$num];                 //中文數字
    $unit = $unitArr[$j];               //單位

    if( $num==0 ){          //當數字是0時
      $count++;             //計算0的個數
      if( ($unit=="萬" or $unit=="億") and $count>=4 ){
        $output .= "";
      }elseif( $unit=="萬" or $unit=="億" ){
        $output .= $unit;
      }
    }else{
      if( $count ){         //當有0時補"零"
        $output .= "零";
        $count = 0;
      }
      $output .= $cn.$unit;
    }
  }

  return $output;
}

#數字格式化
function NumFormat($Num){
    if(strpos($Num,".")) return number_format($Num,$GLOBALS["FLOAT_NUM"]);
    else return number_format($Num);
}
#數字格式化(不要有逗號)
function NumFormat_del($Num){
    if(strpos($Num,".")){
         return number_format($Num,$GLOBALS["FLOAT_NUM"],".","");
    }
    else{
         return number_format($Num,2,".","");
    }
}
#----------------2009.09.13-----------------------------
#說明：數字四捨五入
#輸入：處裡的數值 , 1->顯示用 2->計算用 , 值
#輸出：處理後的數字 19853.2569  $Type=1 => 19,853.26 ; $Type=2 => 19853.26
#-------------------------------------------------------
function NumRound($Num,$Len=""){
    if($Len=="") $Len = $GLOBALS["FLOAT_NUM"];
    return number_format($Num,$GLOBALS["FLOAT_NUM"],".","");
}

#頁面標題(層次，標題，頁面類型)
function HeaderLine($Str,$Title,$Type){
    $Src = explode("/",myUrl());
    if($Src[count($Src)-1] == "php") $Path = "";
    else $Path = "../";
    for($i=0;$i<count($Str)-1;$i++){
        $Msg?$Msg .= "<span class=\"page_main_right_title_02\">".$Str[$i]."</span> &gt; ":$Msg = "<span class=\"page_main_right_title_02\">".$Str[$i]."</span> &gt; ";
    }
    
    $Msg .= "<span class=\"page_main_right_title_03\">".$Str[count($Str)-1]."</span>";
    $HeadMsg = "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                  <tr align=\"left\" valign=\"top\">
                    <td><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                      <tr align=\"left\" valign=\"middle\">
                        <td></td>
                        <td valign=\"bottom\" class=\"page_main_right_title\">".$Title."</td>
                        <td></td>
                      </tr>
                    </table></td>
                    <td width=\"60\" align=\"center\" valign=\"bottom\" class=\"page_main_right_view\">".$Type."</td>
                  </tr>
                </table>
                <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                  <tr>
                    <td background=\"".$Path."../_images/system/line.gif\" width=\"100%\"><img src=\"".$Path."../_images/system/line.gif\" height=\"3\" width=\"5\"></td>
                  </tr>
                </table>
                <table width=\"100%\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">
                  <tr>
                    <td height=\"17\" background=\"".$Path."../_images/system/title_03.gif\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                      <tr align=\"left\" valign=\"middle\">
                          <td width=\"5\"></td>
                          <td><span class=\"page_main_right_title_02\">首頁</span> &gt; ".$Msg."</td>
                      </tr>
                    </table></td>
                  </tr>
                </table>";
    return $HeadMsg;
}

#清除單據
function ClearPaper($DBLIST,$DBITEM,$Title){
    DB_QUERY("DELETE $DBLIST, $DBITEM
              FROM $DBLIST, $DBITEM
              WHERE $DBITEM.uid=$DBLIST.serial
              AND $DBLIST.company='".Company()."'
              AND !$DBLIST.type");
    DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=0
              WHERE paper='$Title' AND company='".Company()."'");
}

#刪除單據
function DelPaper($DBLIST,$DBITEM,$Title){
    DelItem($DBLIST,"serial",$_GET["id"]);
    DelItem($DBITEM,"uid",$_GET["id"]);
    DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=wait-".count(explode(",",$_GET["id"]))."
              WHERE paper='$Title' AND company='".Company()."'");
}

#更新單據處理狀態
function UpdType($DB,$Id,$Type = "2"){
    DB_QUERY("UPDATE $DB SET type='2' WHERE serial='".$Id."'");
}

#更新待處理數量
function UdpWait($Title,$reckon = "-"){
    DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=wait".$reckon.count(explode(",",$_GET["id"]))." WHERE paper='$Title' AND company='".Company()."'");
}

#增加待處理數量
function AddWait($Title,$Num = 1){
    DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=wait+".$Num."
              WHERE paper='".$Title."' AND company='".Company()."'");
}

##傳票號碼指定日期最後一筆＋１
function VoucSerial($toDay,$Paper){
    $RES = DB_QUERY("SELECT num_len FROM $GLOBALS[DB_PAPER] WHERE paper='$Paper'");
    $str_num = mysql_fetch_array($RES);
    $RES=DB_QUERY("SELECT IFNULL(MAX(serial),0) as max_serial FROM $GLOBALS[DB_VOUCLIST] WHERE date='".$toDay."'");
    $ROW=mysql_fetch_array($RES);
    if($ROW["max_serial"]) {
        $new_serial=$ROW["max_serial"]+1;
    }
    else{
        $tmp = explode("-",$toDay);
        $new_serial = join("",$tmp);
        $new_serial.=sprintf('%0'.$str_num["num_len"].'d',1);
        
    }
    return $new_serial;
}


#----------------2009.05.18-----------------------------
#說明：單據設定
#輸入：單據paper , 指定日期 , 使用的資料表 , 其日期欄位名稱 , 其單號欄位名稱 , 其MAX_SERIAL額外的where
#輸出：單號
#-------------------------------------------------------
function Serial($Paper,$Date="",$DB="",$Field="date",$FLD="serial",$WHERE=""){

    if(empty($Date)){
        if($Paper=="sale_list" || $Paper=="order_list") $Date=date("Y-m-d H");//預設為當天日期+小時
        else $Date=date("Y-m-d");//預設為當天日期
    }
    $bok = 1;
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_PAPER] WHERE paper='$Paper'");
    $ROW = mysql_fetch_array($RES);
    $ModifyType = $ROW["serial"].$ROW["year"].$ROW["num_len"].$ROW["reset"];
    $Title = $ROW["serial"];//識別碼
    $NewDate=explode("-",$Date);//指定日期
    $NewYear = $NewDate[0];//西元年 - 年份顯示
    if($ROW["year"]==2) $NewYear = sprintf("%03d",($NewDate[0]-1911)); //民國年 - 年份顯示
    
    if($Paper=="sale_list" || $Paper=="order_list"){
        $NewTime = explode(" ",$NewDate[2]);
        $ThisDate = $NewYear.$NewDate[1].$NewTime[0].$NewTime[1];
    }else $ThisDate = $NewYear.$NewDate[1].$NewDate[2];//指定日期組合成的年月日
    $ThisDateNum = strlen($ThisDate);//組合成的年月日字數
    if($ROW["reset"]=="Day" && $DB){//以日
        $sysDate = date("Y-m-d");
        if($Paper=="sale_list" || $Paper=="order_list") $sysDate=date("Y-m-d H");

        if($Date==$sysDate){#指定日期為系統日期時
            if($ROW["modify_type"] != ($ModifyType) ){
                #指定資料表的最大單據日期//LENGTH
                $RES = DB_QUERY("SELECT MAX($FLD) as max_serial FROM $DB WHERE $FLD LIKE '%".$Title.$ThisDate."%' AND LENGTH(".$FLD.")='".(strlen($Title.$ThisDate)+intval($ROW["num_len"]))."' ".$WHERE);
                $DATA=mysql_fetch_array($RES);
                $WaterNum = 0;
                
                if($DATA["max_serial"]){#有 , 指定日有類似的樣式存在

                    $WaterNum=substr($DATA["max_serial"],(strpos($DATA["max_serial"],$ThisDate)+$ThisDateNum));//流水碼
                    $num_len=strlen($WaterNum);
                    $WaterNumBack=intval($WaterNum)+intval(1);
                    $ShowSerial=$ROW["serial"].$ThisDate.sprintf('%0'.$num_len.'d',$WaterNumBack);
                    $bok = 0;
                }
                DB_QUERY("UPDATE `paper` set last_serial='".$WaterNum."',modify_type='".($ModifyType)."' WHERE paper='".$Paper."'");
            }
        }
        else{
            $RES=DB_QUERY("SELECT MAX($FLD) as max_serial FROM $DB WHERE $FLD LIKE '%".($Title.$ThisDate)."%' AND LENGTH(".$FLD.")='".(strlen($Title.$ThisDate)+intval($ROW["num_len"]))."' ".$WHERE);
            $DATA=mysql_fetch_array($RES);
            if($DATA["max_serial"]){

                $WaterNum=substr($DATA["max_serial"],(strpos($DATA["max_serial"],$ThisDate)+$ThisDateNum));//流水碼
                $num_len=strlen($WaterNum);
                $WaterNumBack=intval($WaterNum)+intval(1);
                $ShowSerial=$ROW["serial"].$ThisDate.sprintf('%0'.$num_len.'d',$WaterNumBack);
            }
            else $ShowSerial=$Title.$ThisDate.sprintf('%0'.$ROW["num_len"].'d',1);
            $bok = 0;
        }
    }else{
        $iftype = 1;
        if(date("Y-m")==$NewDate[0]."-".$NewDate[1] || $ROW["reset"]==""){
            if($ROW["modify_type"] == ($ModifyType)) $iftype = 0; 
        }
        if($iftype && $DB){
            if($ROW["reset"]=="Month"){//以月
                $SQL = "SELECT MAX(substr($FLD,".(strlen($Title.$ThisDate)+1).")) as max_serial FROM $DB WHERE $FLD LIKE '%".($Title.$NewYear.$NewDate[1])."%' AND LENGTH(".$FLD.")='".(strlen($Title.$ThisDate)+intval($ROW["num_len"]))."' ".$WHERE;
            }else if($ROW["reset"]=="Year"){// 以年
                $SQL = "SELECT MAX(substr($FLD,".(strlen($Title.$ThisDate)+1).")) as max_serial FROM $DB WHERE $FLD LIKE '%".($Title.$NewYear)."%' AND LENGTH(".$FLD.")='".(strlen($Title.$ThisDate)+intval($ROW["num_len"]))."' ".$WHERE;
            }
            else{//不重計
                $SQL = "SELECT MAX(substr($FLD,".(strlen($Title.$ThisDate)+1).")) as max_serial FROM $DB WHERE $FLD LIKE '%".($Title)."%' AND LENGTH(".$FLD.")='".(strlen($Title.$ThisDate)+intval($ROW["num_len"]))."' ".$WHERE;
            }
            $RES=DB_QUERY($SQL);
            $DATA=mysql_fetch_array($RES);
            if($DATA["max_serial"]){
                $WaterNum=$DATA["max_serial"];//流水碼
                $num_len=strlen($WaterNum);
                $WaterNumBack=intval($WaterNum)+intval(1);
                $ShowSerial=$Title.$ThisDate.sprintf('%0'.$num_len.'d',$WaterNumBack);
            }
            else $ShowSerial=$Title.$ThisDate.sprintf('%0'.$ROW["num_len"].'d',1);
            DB_QUERY("UPDATE `paper` set last_serial='".$WaterNum."',modify_type='".($ModifyType)."' WHERE paper='".$Paper."'");
            $bok = 0;
        }
        else{
            if(date("Y-m-d")!=$Date) DB_QUERY("UPDATE $GLOBALS[DB_PAPER] SET last_serial=last_serial+1, last_date='".date("Y-m-d")."' WHERE paper='".$Paper."' ");
        }
    }
    if($bok) $ShowSerial=$Title.$ThisDate.sprintf('%0'.$ROW["num_len"].'d',($ROW["last_serial"]+1));
    return $ShowSerial;
}

#----------------2009.05.18-----------------------------
#說明：虛擬帳戶單號
#輸出：單號
#-------------------------------------------------------
function Vir_Serial(){
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_VIRTUALACCOUNT] ");
    $ROW = mysql_fetch_array($RES);
    if(date("Y")!=$ROW["year"] || date("m")!=$ROW["month"]){
        DB_QUERY("UPDATE $GLOBALS[DB_VIRTUALACCOUNT] SET year='".date("Y")."',month='".date("m")."',last_serial=0  ");
    }
    $ShowSerial=$GLOBALS["BUSACCOUNT"].substr(date("Y"),-1).date("m").sprintf('%04d',($ROW["last_serial"]+1));
    return $ShowSerial;
}


#自動轉入庫單
function AddStore_make($DBLIST,$DBITEM,$Id,$Title,$NFid = ""){
    #入庫單明細欄位
    $STORE = Array();
    $RES = DB_QUERY("SHOW COLUMNS FROM $GLOBALS[DB_STOREITEM] WHERE Field!='id' AND Field!='uid'");
    while($row=mysql_fetch_array($RES)) $STORE[] = $row["Field"];
    #單據基本資料
    $RES = DB_QUERY("SELECT * FROM $DBLIST WHERE serial='".$Id."'");
    $ROW = mysql_fetch_array($RES);

    list($ROW["date"],$ROW["type"]) = Array(date("Y-m-d"),2);
    $Filed = Array("date","company","serial","uid","source","dept","dept_title","person","person_name","sto_cate","sto_id","sto_title","description","type");
    list($ROW["serial"],$ROW["uid"],$ROW["source"]) = Array(Serial("store_list","",$GLOBALS["DB_STORELIST"]),$Id,$Title);
    for($j=0;$j<count($Filed);$j++){
        $Value[] = $ROW[$Filed[$j]];
    }
    SaveData($id,$GLOBALS["DB_STORELIST"],$Filed,$Value,"serial");
    DB_QUERY("UPDATE $GLOBALS[DB_PAPER] SET last_serial=last_serial+1, last_date='".date("Y-m-d")."' WHERE paper='store_list'");
    DB_QUERY("INSERT INTO $GLOBALS[DB_STOREITEM] (uid,".join(",",$STORE).")
             (SELECT DB2.serial,DB1.".($NFid?(str_replace("DB1.num,",("DB1.".$NFid.","),join(",DB1.",$STORE))):(join(",DB1.",$STORE)))." FROM $DBITEM AS DB1
             LEFT JOIN $GLOBALS[DB_STORELIST] AS DB2 ON DB2.uid=DB1.uid
             WHERE DB1.uid='".$Id."' AND DB2.company='".Company()."')");
}

#----------------2009.06.12-----------------------------
#說明：自動轉入庫單(倉庫於明細中)
#輸入：指定資料表主檔 , 指定資料表明細 , 單據編號 , 來源名稱 , 特殊欄位名稱
#-------------------------------------------------------
function AddStoreItem($DBLIST,$DBITEM,$Id,$Title,$NFid = ""){
    
    #明細倉庫主類
    $RES = DB_QUERY("SELECT * FROM $DBITEM WHERE uid='".$Id."' GROUP BY sto_id");
    $StoArray = Array();
    while($ROW = mysql_fetch_array($RES)){
        $StoArray[] = $ROW["sto_cate"]."||".$ROW["sto_id"]."||".$ROW["sto_title"];
    }

    for($k=0;$k<count($StoArray);$k++){
        #入庫單明細欄位
        $STORE = Array();
        $RES = DB_QUERY("SHOW COLUMNS FROM $GLOBALS[DB_STOREITEM] WHERE Field!='id' AND Field!='uid'");
        while($row=mysql_fetch_array($RES)) $STORE[] = $row["Field"];
        $StoData = explode("||",$StoArray[$k]);
        #單據基本資料
        $RES = DB_QUERY("SELECT * FROM $DBLIST WHERE serial='".$Id."'");
        $ROW = mysql_fetch_array($RES);
        list($ROW["date"],$ROW["type"]) = Array(date("Y-m-d"),2);
        $Filed = Array("date","company","serial","uid","source","dept","dept_title","person","person_name","sto_cate","sto_id","sto_title","description","type");
        $Value = Array();
        list($ROW["serial"],$ROW["uid"],$ROW["source"],$ROW["sto_cate"],$ROW["sto_id"],$ROW["sto_title"]) = Array(Serial("store_list","",$GLOBALS["DB_STORELIST"]),$Id,$Title,$StoData[0],$StoData[1],$StoData[2]);
        for($j=0;$j<count($Filed);$j++){
            $Value[] = $ROW[$Filed[$j]];
        }
        SaveData($id,$GLOBALS["DB_STORELIST"],$Filed,$Value,"serial");
        DB_QUERY("UPDATE $GLOBALS[DB_PAPER] SET last_serial=last_serial+1, last_date='".date("Y-m-d")."' WHERE paper='store_list'");

        #入庫數量 改成 數量+贈品數量，故將欄位名稱固定
        if(!strcmp($DBITEM,$GLOBALS["DB_SRTNITEM"]) ){
            $present = " + DB1.gift";
        }else{
            $present = "";
        }
//         DB_QUERY("INSERT INTO $GLOBALS[DB_STOREITEM] (uid,".join(",",$STORE).")
//                  (SELECT DB2.serial,DB1.".($NFid?(str_replace("DB1.num$present,",("DB1.".$NFid.",".$present),join(",DB1.",$STORE))):(join(",DB1.",$STORE)))." FROM $DBITEM AS DB1
//                  LEFT JOIN $GLOBALS[DB_STORELIST] AS DB2 ON DB2.uid=DB1.uid AND DB2.sto_id='".$StoData[1]."'
//                  WHERE DB1.uid='".$Id."' AND DB1.sto_id='".$StoData[1]."' AND DB2.company='".Company()."')");
                 
        DB_QUERY("INSERT INTO $GLOBALS[DB_STOREITEM]
    	(uid,barcode,pserial,title,specif,base_unit,unit,numerator,denominator,num,fid) 
        (SELECT DB2.serial,DB1.barcode,DB1.pserial,DB1.title,DB1.specif,DB1.base_unit,
         DB1.unit,DB1.numerator,DB1.denominator,DB1.num$present,DB1.fid 
    	 FROM $DBITEM AS DB1 
         LEFT JOIN $GLOBALS[DB_STORELIST] AS DB2 ON DB2.uid=DB1.uid AND DB2.sto_id='".$StoData[1]."'
         WHERE DB1.uid='".$Id."' AND DB1.sto_id='".$StoData[1]."' AND DB2.company='".Company()."')");
    }
}


#自動轉入庫單
function AddStore($DBLIST,$DBITEM,$Id,$Title,$NFid = ""){
    #入庫單明細欄位
    $STORE = Array();
    $RES = DB_QUERY("SHOW COLUMNS FROM $GLOBALS[DB_STOREITEM] WHERE Field!='id' AND Field!='uid'");
    while($row=mysql_fetch_array($RES)) $STORE[] = $row["Field"];
    #單據基本資料
    $RES = DB_QUERY("SELECT * FROM $DBLIST WHERE serial='".$Id."'");
    $ROW = mysql_fetch_array($RES);

    list($ROW["date"],$ROW["type"]) = Array(date("Y-m-d"),2);
    $Filed = Array("date","company","serial","uid","source","dept","dept_title","person","person_name","sto_cate","sto_id","sto_title","description","type");
    list($ROW["serial"],$ROW["uid"],$ROW["source"]) = Array(Serial("store_list","",$GLOBALS["DB_STORELIST"]),$Id,$Title);
    for($j=0;$j<count($Filed);$j++){
        $Value[] = $ROW[$Filed[$j]];
    }
    SaveData($id,$GLOBALS["DB_STORELIST"],$Filed,$Value,"serial");
    
    #入庫數量 改成 數量+贈品數量，故將欄位名稱固定
    if(!strcmp($DBITEM,$GLOBALS["DB_SRTNITEM"]) ){
        $present = " + DB1.gift";
    }else{
        $present = "";
    }
    
    DB_QUERY("UPDATE $GLOBALS[DB_PAPER] SET last_serial=last_serial+1, last_date='".date("Y-m-d")."' WHERE paper='store_list'");
    DB_QUERY("INSERT INTO $GLOBALS[DB_STOREITEM]
    	(uid,barcode,pserial,title,specif,base_unit,unit,numerator,denominator,num,fid) 
        (SELECT DB2.serial,DB1.barcode,DB1.pserial,DB1.title,DB1.specif,DB1.base_unit,
         DB1.unit,DB1.numerator,DB1.denominator,DB1.num$present,DB1.fid 
    	 FROM $DBITEM AS DB1 
         LEFT JOIN $GLOBALS[DB_STORELIST] AS DB2 ON DB2.uid=DB1.uid 
         WHERE DB1.uid='".$Id."'  AND DB2.company='".Company()."')");
}

#自動轉出庫單
function AddCarry($DBLIST,$DBITEM,$Id,$Title){
    #出庫單明細欄位
    $Carry = Array();
    $RES = DB_QUERY("SHOW COLUMNS FROM $GLOBALS[DB_CARRYITEM] WHERE Field!='id' AND Field!='uid'");
    while($row=mysql_fetch_array($RES)) $Carry[] = $row["Field"];
    
    #單據基本資料
    $RES = DB_QUERY("SELECT * FROM $DBLIST WHERE serial='".$Id."'");
    $ROW = mysql_fetch_array($RES);
    
    list($ROW["date"],$ROW["type"]) = Array(date("Y-m-d"),2);
    $Filed = Array("date","company","serial","uid","source","dept","dept_title","person","person_name","sto_cate","sto_id","sto_title","description","type");
    list($ROW["serial"],$ROW["uid"],$ROW["source"]) = Array(Serial("carry_list","",$GLOBALS["DB_CARRYLIST"]),$Id,$Title);
    for($j=0;$j<count($Filed);$j++){
        $Value[] = $ROW[$Filed[$j]];
    }
    SaveData($id,$GLOBALS["DB_CARRYLIST"],$Filed,$Value,"serial");
    DB_QUERY("UPDATE $GLOBALS[DB_PAPER] SET last_serial=last_serial+1, last_date='".date("Y-m-d")."' WHERE paper='carry_list'");

    #出庫數量 改成 數量+贈品數量，故將欄位名稱固定
    if(!strcmp($DBITEM,$GLOBALS["DB_SALEITEM"]) || !strcmp($DBITEM,$GLOBALS["DB_SRTNITEM"]) ){
        $present = " + DB1.gift";
    }else{
        $present = "";
    }
    DB_QUERY("INSERT INTO $GLOBALS[DB_CARRYITEM] 
    	(uid,barcode,pserial,title,specif,base_unit,unit,numerator,denominator,num,fid) 
        (SELECT DB2.serial,DB1.barcode,DB1.pserial,DB1.title,DB1.specif,DB1.base_unit,
         DB1.unit,DB1.numerator,DB1.denominator,DB1.num$present,DB1.fid 
    	 FROM $DBITEM AS DB1 LEFT JOIN $GLOBALS[DB_CARRYLIST] AS DB2 
         ON DB2.uid=DB1.uid WHERE DB1.uid='".$Id."')
        ");
}

#新增出入庫明細
function AddReserve($DBLIST,$DBITEM,$Id,$Type,$Title,$NFid = ""){
    #出入庫數量 改成 數量+贈品數量，故將欄位名稱固定
    if(!strcmp($DBITEM,$GLOBALS["DB_SALEITEM"]) || !strcmp($DBITEM,$GLOBALS["DB_SRTNITEM"]) ){
        $present = " + DB2.gift";
    }else{
        $present = "";
    }
    DB_QUERY("INSERT INTO $GLOBALS[DB_RESERVEHIS] (date,serial,company,s_cate,sid,s_title,sto_cate,sto_id,sto_title,fid,".join(",",$GLOBALS["JOIN_FILED"]).",".join(",",$GLOBALS["BASIC_FILED"]["Fld"]).",unit,num)
             (SELECT DB1.date,DB1.serial,DB1.company,DB1.dept,DB1.person,DB1.person_name,DB1.sto_cate,DB1.sto_id,DB1.sto_title,DB2.fid,DB2.".join(",DB2.",$GLOBALS["JOIN_FILED"]).",DB2.".join(",DB2.",$GLOBALS["BASIC_FILED"]["Fld"]).",DB2.unit,DB2.".($NFid?$NFid:"num").$present."
             FROM $DBLIST AS DB1
             LEFT JOIN $DBITEM AS DB2 ON DB2.uid=DB1.serial
             WHERE DB1.serial='".$Id."')");
    DB_UPDATE($GLOBALS["DB_RESERVEHIS"],Array("reserves","source"),Array($Type,$Title),"serial='".$Id."' AND company='".Company()."' AND source=''");
}

#----------------2009.06.12-----------------------------
#說明：新增出入庫明細
#輸入：指定資料表主檔 , 指定資料表明細 , 單據編號 , 來源名稱 , 特殊欄位名稱
#-------------------------------------------------------
function AddReserveItem($DBLIST,$DBITEM,$Id,$Type,$Title,$NFid = ""){
    #出入庫數量 改成 數量+贈品數量，故將欄位名稱固定
    if(!strcmp($DBITEM,$GLOBALS["DB_SALEITEM"]) || !strcmp($DBITEM,$GLOBALS["DB_SRTNITEM"]) ){
        $present = " + DB2.gift";
    }else{
        $present = "";
    }
    DB_QUERY("INSERT INTO $GLOBALS[DB_RESERVEHIS] (date,serial,company,s_cate,sid,s_title,sto_cate,sto_id,sto_title,fid,".join(",",$GLOBALS["JOIN_FILED"]).",".join(",",$GLOBALS["BASIC_FILED"]["Fld"]).",unit,num)
             (SELECT DB1.date,DB1.serial,DB1.company,DB1.dept,DB1.person,DB1.person_name,DB2.sto_cate,DB2.sto_id,DB2.sto_title,DB2.fid,DB2.".join(",DB2.",$GLOBALS["JOIN_FILED"]).",DB2.".join(",DB2.",$GLOBALS["BASIC_FILED"]["Fld"]).",DB2.unit,DB2.".($NFid?$NFid:"num").$present."
             FROM $DBLIST AS DB1
             LEFT JOIN $DBITEM AS DB2 ON DB2.uid=DB1.serial
             WHERE DB1.serial='".$Id."')");
    DB_UPDATE($GLOBALS["DB_RESERVEHIS"],Array("reserves","source"),Array($Type,$Title),"serial='".$Id."' AND company='".Company()."' AND source=''");
}

#新增指定倉庫架位
function InsertStoNum($DBLIST,$DBITEM,$Id,$Type,$NFid = "",$STid=""){
    $RES=DB_QUERY("SELECT DB1.pserial,DB1.".($NFid?$NFid:"fid").",DB2.".($STid?$STid:"sto_id").",DB1.barcode,DB1.title,DB1.specif,DB1.base_unit,DB3.make
                            FROM $DBITEM AS DB1 
                            LEFT OUTER JOIN $DBLIST AS DB2 ON DB2.serial=DB1.uid
                            LEFT OUTER JOIN $GLOBALS[DB_PRODUCT] AS DB3 ON DB3.serial=DB1.pserial 
                            WHERE DB2.serial='".$Id."' ");
    while($ROW=mysql_fetch_array($RES)){
        $res = DB_QUERY("SELECT * FROM $GLOBALS[DB_PRO2FAM] WHERE sid='".$ROW[($STid?$STid:"sto_id")]."' AND fid='".$ROW[($NFid?$NFid:"fid")]."' AND pserial='".$ROW["pserial"]."' and beta=1");
        if(!mysql_num_rows($res)){
            $FLD = array("barcode","pserial","title","specif","unit","sid","fid","first_num","total_num","beta","make");
            $VAL = array($ROW["barcode"],$ROW["pserial"],$ROW["title"],$ROW["specif"],$ROW["base_unit"],$ROW[($STid?$STid:"sto_id")],$ROW[($NFid?$NFid:"fid")],0,0,1,$ROW["make"]);
            DB_INSERT($GLOBALS["DB_PRO2FAM"],$FLD,$VAL);
        }
    }
}
#更新庫存量
function UdpStoNum($DBLIST,$DBITEM,$Id,$Type,$NFid = ""){
    #出入庫數量 改成 數量+贈品數量，故將欄位名稱固定
    if(!strcmp($DBITEM,$GLOBALS["DB_SALEITEM"]) || !strcmp($DBITEM,$GLOBALS["DB_SRTNITEM"]) ){
        $present = " + DB1.gift";
    }else{
        $present = "";
    }

    $RES=DB_QUERY("SELECT DB1.pserial,sum((DB1.".($NFid?$NFid:"num").$present.") * DB1.denominator / DB1.numerator) as num,DB1.fid,DB2.sto_id 
                            FROM $DBITEM AS DB1 
                            LEFT OUTER JOIN $DBLIST AS DB2 ON DB2.serial=DB1.uid 
                            WHERE DB2.serial='".$Id."'
                            GROUP BY CONCAT(DB1.pserial,DB1.fid)");
    while($ROW=mysql_fetch_array($RES)){
        DB_QUERY("UPDATE $GLOBALS[DB_PRO2FAM] SET total_num=total_num".($Type?"+":"-")."$ROW[num] 
        WHERE pserial='".$ROW["pserial"]."' and fid='".$ROW["fid"]."' and sid='".$ROW["sto_id"]."' AND beta = 1");
    }
}

#----------------2009.06.12-----------------------------
#說明：更新庫存量(倉庫於明細中)
#輸入：指定資料表主檔 , 指定資料表明細 , 單據編號 , 出(0)還是進(1) , 特殊欄位名稱
#-------------------------------------------------------
function UdpStoNumItem($DBLIST,$DBITEM,$Id,$Type,$NFid = ""){
    #出入庫數量 改成 數量+贈品數量，故將欄位名稱固定
    if(!strcmp($DBITEM,$GLOBALS["DB_SALEITEM"]) || !strcmp($DBITEM,$GLOBALS["DB_SRTNITEM"]) ){
        $present = " + DB1.gift";
    }else{
        $present = "";
    }

    $RES=DB_QUERY("SELECT DB1.pserial,sum((DB1.".($NFid?$NFid:"num").$present.") * DB1.denominator / DB1.numerator) as num,DB1.fid,DB1.sto_id 
                            FROM $DBITEM AS DB1 
                            LEFT OUTER JOIN $DBLIST AS DB2 ON DB2.serial=DB1.uid 
                            WHERE DB2.serial='".$Id."'
                            GROUP BY CONCAT(DB1.pserial,DB1.sto_id,DB1.fid)");
    while($ROW=mysql_fetch_array($RES)){
        DB_QUERY("UPDATE $GLOBALS[DB_PRO2FAM] SET total_num=total_num".($Type?"+":"-")."$ROW[num] 
        WHERE pserial='".$ROW["pserial"]."' and fid='".$ROW["fid"]."' and sid='".$ROW["sto_id"]."' AND beta=1");
    }
}

#新增應收帳款主檔
function CreateRECList($DB,$Id,$PayBill,$Cus=""){
    if(!$Cus){
        $RES = DB_QUERY("SELECT * FROM $DB WHERE serial='".$Id."' AND company='".Company()."'");
        $ROW = mysql_fetch_array($RES);
        $Cus = $ROW["cus_serial"];
    }
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_RECEIVLIST] WHERE cus_serial='".$Cus."' and paybill='".$PayBill."' and !type AND company='".Company()."'");
    if(!mysql_num_rows($RES)){
        $Fld = "paybill";
        if($DB=="sale_list") $Fld = "paybillm";
        DB_QUERY("INSERT INTO $GLOBALS[DB_RECEIVLIST]
                    (cus_cate,cus_serial,cus_title,valuta,valuta_title,company,paybill)
                    (SELECT cus_cate,cus_serial,cus_title,valuta,valuta_title,company,$Fld 
                    FROM $DB WHERE serial='".$Id."' AND company='".Company()."')");
        $paper = "recmonthDetail_list";
        if($PayBill=="現金制") $paper = "reccashDetail_list";
        AddWait($paper);
    }
}

#更新應付帳款
function PayTab($DB,$Id){
    $RES = DB_QUERY("SELECT * FROM $DB WHERE serial='".$Id."' AND company='".Company()."'");
    $ROW = mysql_fetch_array($RES);
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_PAYTAB] WHERE sid='".$ROW["sup_serial"]."' AND company='".$ROW["company"]."' AND CONCAT(year,\"-\",month)='".($ROW["payup_month"])."'");
    if(mysql_num_rows($RES)) DB_QUERY("UPDATE $GLOBALS[DB_PAYTAB] SET stock_total=stock_total+".$ROW["total"]."  WHERE sid='".$ROW["sup_serial"]."' AND company='".$ROW["company"]."' AND CONCAT(year,\"-\",month)='".($ROW["payup_month"])."'");
    else{
        $payup=explode("-",$ROW["payup_month"]);
        $Filed = Array("sid","company","year","month","stock_total","discount_total");
        $Value = Array($ROW["sup_serial"],$ROW["company"],$payup[0],$payup[1],$ROW["total"],$ROW["distotal"]);
        DB_INSERT($GLOBALS["DB_PAYTAB"],$Filed,$Value);
    }
}

#新增應付帳款主檔
function CreatePayList($DB,$Id){
    $RES = DB_QUERY("SELECT * FROM $DB WHERE serial='".$Id."' AND company='".Company()."'");
    $ROW = mysql_fetch_array($RES);
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_PAYLIST] WHERE sup_serial='".$ROW["sup_serial"]."' and !type AND company='".Company()."'");
    if(!mysql_num_rows($RES)){
        DB_QUERY("INSERT INTO $GLOBALS[DB_PAYLIST]
                    (sup_cate,sup_serial,sup_title,valuta,valuta_title,company)
                    (SELECT sup_cate,sup_serial,sup_title,valuta,valuta_title,company 
                    FROM $DB WHERE serial='".$Id."' AND company='".Company()."')");
        AddWait("paytDetail_list");
    }
}
#付款沖帳其他選擇
function payType($sup){
    $Str = "";
    $RES = DB_QUERY("SELECT sup_serial FROM $GLOBALS[DB_PAYDEPOSIT] WHERE sup_serial='".$sup."' and company='".Company()."' and total_price>price");
    if(mysql_num_rows($RES)){
        $Str.="<input type=\"button\" style=\"width:40px\" class=\"button_input\" onClick=\"javascript:payType('".$sup."',this.parentNode.parentNode)\" value=\"訂金\">";
    }
    return $Str;
}
#更新應收帳款
function ReceivTab($DB,$Id){
    $RES = DB_QUERY("SELECT * FROM $DB WHERE serial='".$Id."'");
    $ROW = mysql_fetch_array($RES);
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_RECEIVTAB] WHERE cid='".$ROW["cus_serial"]."' AND company='".$ROW["company"]."' AND CONCAT(year,\"-\",month)='".($ROW["payup_month"]?$ROW["payup_month"]:date("Y-m"))."'");
    if(mysql_num_rows($RES)) DB_QUERY("UPDATE $GLOBALS[DB_RECEIVTAB] SET sale_total=sale_total+".$ROW["total"]."  WHERE cid='".$ROW["cus_serial"]."' AND company='".$ROW["company"]."' AND CONCAT(year,\"-\",month)='".($ROW["payup_month"]?$ROW["payup_month"]:date("Y-m"))."'");
    else{
        $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_CUSTOMER] WHERE serial='".$ROW["cus_serial"]."'");
        $row = mysql_fetch_array($RES);
        $payup=explode("-",($ROW["payup_month"]?$ROW["payup_month"]:date("Y-m")));
        $Filed = Array("cid","company","year","month","credit","sale_total","discount_total");
        $Value = Array($ROW["cus_serial"],$ROW["company"],$payup[0],$payup[1],$row["credit"],$ROW["total"],$ROW["distotal"]);
        DB_INSERT($GLOBALS["DB_RECEIVTAB"],$Filed,$Value);
    }
}

#功能列表
#----------------2009.05.22-----------------------------
#說明：產生按鈕列
#輸入：按鈕陣列 ,  統一多加的預設值名稱(加於名稱哪個位置 , 看個案而定)@寬度
#輸出：按鈕列
#-------------------------------------------------------
function ListAction($List,$OtherStr=""){
    $Tp = explode("?",$_SERVER["PHP_SELF"]);
    $Tmp = explode("/",$_SERVER["PHP_SELF"]);
    $Temp = explode("_",$Tmp[count($Tmp)-1]);
    $Page = $Temp[0]."_";
    $OtherStr = explode("@",$OtherStr);
    $OtherWidth = $OtherStr[1]?"style=\"width:".($OtherStr[1]+($_SESSION["font"]=='text'?0:20))."px\"":"";
    $EditList["Blank"] = "　&nbsp;";
    $EditList["Finish"] = "<input type=\"button\" class=\"button_input\" onClick=\"javascript:Finish()\" value=\"轉結案\"> ";
    $EditList["Viewmake"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Viewmake()\" value=\"".$OtherStr[0]."物料單\"> ";
    $EditList["Viewmake_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Viewmake(){
                                var obj = document.getElementById(\"MyList\");
                                var edit = 0;
                                var item = new Array();
                                for(var i=0;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        edit++;
                                    }
                                }
                                if(!edit) alert(\"請選擇欲檢視項目!!\");
                                else window.location.href = \"".$Page."view.php?id=\" +item +\"&main=".(substr($Temp[1],0,4)=="list"?"list":"history")."\";
                            }
                         </script>";
    $EditList["View"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:View()\" value=\"檢視".$OtherStr[0]."\"> ";
    $EditList["View_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function View(){
                                var obj = document.getElementById(\"MyList\");
                                var edit = 0;
                                var item = new Array();
                                for(var i=0;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        edit++;
                                    }
                                }
                                if(!edit) alert(\"請選擇欲檢視項目!!\");
                                else window.location.href = \"".$Page."view.php?id=\" +item +\"&main=".(substr($Temp[1],0,4)=="list"?"list":"history")."\";
                            }
                         </script>";
    $EditList["Recok"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Recok()\" value=\"確認付款\"> ";
    $EditList["Recok_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Recok(){
                                var obj = document.getElementById(\"MyList\");
                                var edit = 0;
                                var item = new Array();
                                for(var i=1;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        edit++;
                                    }
                                }
                                if(!edit) alert(\"請選擇確認付款的項目!!\");
                                else if(confirm(\"確定選擇的項目都已確認付款完畢?? \")) window.location.href = \"".$Tp[0]."\" +\"?type=apply&id=\" +item+\"&dept=\"+encodeURIComponent(document.search.dept.value)+\"&person=\"+encodeURIComponent(document.search.person.value);
                            }
                         </script>";
    $EditList["Add"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:window.location.href='".$Page."edit.php'\" value=\"新增".$OtherStr[0]."\"> ";
    $EditList["Addreturn"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:window.location.href='".$Page."add_edit.php'\" value=\"新增".$OtherStr[0]."\"> ";
    $EditList["Buy_Add"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:window.location.href='".$Page."add_edit.php'\" value=\"新增".$OtherStr[0]."\"> ";//採購新增
    $EditList["Edit"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Edit()\" value=\"編修".$OtherStr[0]."\"> ";
    $EditList["Edit_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Edit(){
                                var obj = document.getElementById(\"MyList\");
                                var edit = 0;
                                var item = new Array();
                                for(var i=0;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        edit++;
                                    }
                                }
                                if(!edit) alert(\"請選擇欲編修項目!!\");
                                    else window.location.href = \"".$Page."edit.php?id=\" +item;
                            }
                         </script>";
    $EditList["Update"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Update()\" value=\"列表更新\"> ";
    $EditList["Update_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Update(){
                                var obj = document.getElementById(\"MyList\");
                                var edit = 0;
                                var item = new Array();
                                var val = new Array();
                                var tmpData = SelIndex.split(\",\");
                                for(var i=0;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        var Str = \"\";
                                        for(var k=0;k<tmpData.length;k++){
                                        switch(obj.rows[i].cells[tmpData[k]].firstChild.type){
                                                case \"text\":
                                                    Str = Str+ (Str?'||':'')+(obj.rows[i].cells[tmpData[k]].firstChild.value);
                                                    break;
                                                case \"checkbox\":
                                                    Str = Str+ (Str?'||':'')+(obj.rows[i].cells[tmpData[k]].firstChild.checked?'1':'0');
                                                    break;
                                                default:break;
                                            }
                                        
                                            
                                        }
                                        val[val.length] = Str;
                                        edit++;
                                    }
                                }
                                if(!edit) alert(\"請選擇欲更新項目!!\");
                                else window.location.href = \"".$Tp[0]."\" +\"?type=save&id=\" +item +\"&val=\" +val;
                            }
                         </script>";
    $EditList["Save"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Save()\" value=\"排序更新".$OtherStr[0]."\"> ";
    $EditList["Save_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Save(){
                                var obj = document.getElementById(\"MyList\");
                                var edit = 0;
                                var item = new Array();
                                var val = new Array();
                                for(var i=0;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        val[val.length] = obj.rows[i].cells[2].firstChild.value;
                                        edit++;
                                    }
                                }
                                if(!edit) alert(\"請選擇欲儲存項目!!\");
                                    else window.location.href = \"".$Tp[0]."\" +\"?type=save&id=\" +item +\"&val=\" +val;
                            }
                         </script>";
    $EditList["Apply"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Apply()\" value=\"完成".$OtherStr[0]."\"> ";
    $EditList["Apply_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Apply(){
                                var obj = document.getElementById(\"MyList\");
                                var app = 0;
                                var item = new Array();
                                for(var i=obj.rows.length-1;i>=0;i--){
                                    
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        app++;
                                    }
                                }
                                if(!app) alert(\"請選擇已完成的項目!!\");
                                else if(confirm(\"確定選擇的項目都已".$Title."完成?? \")) window.location.href = \"".$Tp[0]."\" +\"?type=apply&id=\" +item;
                            }
                          </script>";
    $EditList["Voucok"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Voucok()\" value=\"轉正式傳票\"> ";
    $EditList["Voucok_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Voucok(){
                                var obj = document.getElementById(\"MyList\");
                                var app = 0;
                                var item = new Array();
                                for(var i=obj.rows.length-1;i>=0;i--){
                                    
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        app++;
                                    }
                                }
                                if(!app) alert(\"請選擇欲轉正式傳票的項目!!\");
                                else if(confirm(\"注意 : 轉正式傳票後 , 不得再更改 , 確定選擇的項目都欲轉正式傳票?? \")) window.location.href = \"".$Tp[0]."\" +\"?type=apply&id=\" +item;
                            }
                          </script>";
    $EditList["Subcarried"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Subcarried(document.form1)\" value=\"結轉\"> "; 
    $EditList["Delcarried"] = "<input type=\"button\" style=\"width:100px\" class=\"button_input\" onClick=\"javascript:Delcarried()\" value=\"刪除前次結轉\"> ";
    $EditList["Delete"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Delete()\" value=\"刪除".$OtherStr[0]."\"> ";
    $EditList["Delete_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Delete(){
                                var obj = document.getElementById(\"MyList\");
                                var del = 0;
                                var item = new Array();
                                var Num = document.getElementById(\"MyHead\")?0:1;
                                for(var i=Num;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                        item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                        del++;
                                    }
                                }
                                if(!del) alert(\"請選擇欲刪除的項目!!\");
                                else if(confirm(\"確定要刪除選擇的項目?? \")) window.location.href = \"".$Tp[0]."\" +\"?type=del&id=\" +item;
                            }
                           </script>";
//     $EditList["Clear"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Clear()\" value=\"清除".$OtherStr[0]."\"> ";
//     $EditList["Clear_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
//                             function Clear(){
//                                 if(confirm(\"確定要清除全部資料??\")){
//                                     window.location.href = \"".$Tp[0]."\" +\"?type=del&id=clear\";
//                                 }
//                             }
//                           </script>
//                           ";
    $EditList["AppVouc"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:AppVouc()\" value=\"拋轉傳票\">";
    $EditList["AppVouc_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                                function AppVouc(){
                                    var obj = document.getElementById(\"MyList\");
                                    var app = 0;
                                    var item = new Array();
                                    var bok=true;
                                    for(var i=0;i<obj.rows.length;i++){
                                        if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled && obj.rows[i].cells[0].firstChild.value!='on'){
                                            if(obj.rows[i].cells[6].innerHTML=='已拋轉'){
                                                bok=false;
                                                alert(\"編號 : \"+obj.rows[i].cells[3].innerHTML +\" 已拋轉!!\");
                                                obj.rows[i].cells[0].firstChild.checked=false;
                                            }
                                            else{
                                                item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                                app++;
                                            }
                                        }
                                    }
                                    if(bok){
                                        var ErpVocher = '';
                                        if(document.search.erptovocher){
                                            ErpVocher = \"&erptovocher=\"+document.search.erptovocher.value+\"\";
                                        }
                                        if(!app) alert(\"請選擇欲拋轉的項目!!\");
                                        else if(confirm(\"確定將選擇的項目拋轉傳票?? \")) window.location.href = \"".$Page."edit.php\" +\"?type=pay&id=\" +item+ErpVocher;
                                    }
                                }
                              </script>";
    $EditList["AddAcc"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\"  onClick=\"javascript:AddAcc()\" value=\"新增".$OtherStr[0]."\"> ";
    $EditList["EditAcc"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\"  onClick=\"javascript:EditAcc()\" value=\"編修".$OtherStr[0]."\"> ";
    $EditList["DelAcc"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\"  onClick=\"javascript:DelAcc()\" value=\"刪除".$OtherStr[0]."\"> ";
    $EditList["Buy"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\"  onClick=\"javascript:Buy()\" value=\"採購\"> ";
    $EditList["Store"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\"  onClick=\"javascript:Store()\" value=\"".$OtherStr[0]."存貨管理\"> ";
    $EditList["Deny"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Deny()\" value=\"拒絕登入\"> ";
    $EditList["Allow"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Allow()\" value=\"允許登入\"> ";
    $EditList["Batch"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:window.open('batch_price.php','_blank','location=no,top=' +(screen.height/4) +',left=' +(screen.width/4) +',width=500px,height=200px')\" value=\"".$OtherStr[0]."價格修改\"> ";
    $EditList["ImgCreate"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javscript:ImgCreate()\" value=\"建".$OtherStr[0]."資料夾\"> ";
    $EditList["ImgUpload"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javscript:ImgUpload()\" value=\"新增".$OtherStr[0]."檔案\"> ";
    $EditList["ImgMove"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javscript:ImgMove(document.form1)\" value=\"".$OtherStr[0]."檔案移動\"> ";
    $EditList["ImgCopy"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javscript:ImgCopy(document.form1)\" value=\"".$OtherStr[0]."檔案複製\"> ";
    $EditList["ImgDel"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javscript:ImgDel(document.form1)\" value=\"".$OtherStr[0]."檔案刪除\"> ";
    $EditList["ImgClear"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javscript:ImgClear(document.form1)\" value=\"".$OtherStr[0]."檔案清空\"> ";
    $EditList["Print"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Print()\" value=\"列印".$OtherStr[0]."\"> ";
    $Out = "";
    ###權限
    if(strlen($GLOBALS["ThisAuth"][0])){
    
        for($i=0;$i<count($GLOBALS["ThisAuth"][1]);$i++){
            if(!substr($GLOBALS["ThisAuth"][0],$i,1)){
                if($GLOBALS["ThisAuth"][1][$i] == "del"){
                    $EditList["Delete"] = "";
                    $EditList["Clear"] = "";
                }else{
                     $EditList[ucfirst($GLOBALS["ThisAuth"][1][$i])] = "";
                     $EditList[ucfirst($GLOBALS["ThisAuth"][1][$i])."_val"] = "";
                }
            }
        }
    }
    ###
    $Out = "";
    for($i=0;$i<count($List);$i++){
        $NewList = explode("@",$List[$i]);
        if($EditList[$NewList[0]]!=''){
            $strlen = $_SESSION["font"]=="text"?14:20;
            $NewButtonName = "<input type=\"button\" ".($NewList[2]?"style=\"width:".($NewList[2]*$strlen)."px\"":"")." class=\"button_input\" onClick=\"javascript:".$NewList[0]."()\" value=\"".$NewList[1]."\"> ";
            $Out .=($NewList[1]?$NewButtonName:$EditList[$NewList[0]]).$EditList[$NewList[0]."_val"];
        }
    }
    return $Out."　&nbsp;";
}
#----------------2009.05.22-----------------------------
#說明：產生按鈕列
#輸入：按鈕陣列 , list or edit , 統一多加的預設值名稱(加於名稱哪個位置 , 看個案而定)@寬度
#輸出：按鈕列
#-------------------------------------------------------
function EditAction($List,$IdList = "",$OtherStr = ""){
    $Tp = explode("?",$_SERVER["PHP_SELF"]);
    $Tmp = explode("/",$_SERVER["PHP_SELF"]);
    $Temp = explode("_",$Tmp[count($Tmp)-1]);
    $Page = $Temp[0]."_";
    $Tmp_Front = explode("?",$_SERVER["HTTP_REFERER"]);
    $Front = explode("_",$Tmp_Front[0]);
    $OtherStr = explode("@",$OtherStr);
    $OtherWidth = $OtherStr[1]?"style=\"width:".($OtherStr[1]+($_SESSION["font"]=='text'?0:20))."px\"":"";
    $EditList["Blank"] = "　&nbsp;";
    
    $EditList["Submit"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Sendsubmit(document.form1)\" value=\"送出".$OtherStr[0]."\"> ";
    $EditList["Save"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Sendsubmit(document.form1)\" value=\"存檔\"> ";
    $EditList["Reset"] = "<input type=\"button\" class=\"button_input\" ".($OtherStr[1]?"style=\"width:".$OtherStr[1]."\"":"")." onClick=\"javascript:str=true;window.location.reload()\" value=\"重填".$OtherStr[0]."\"> ";
    $EditList["Back"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:window.location.href = '".$Page.($_GET["main"]?$_GET["main"]:"list").".php'\" value=\"返回\"> ";
    $EditList["Preview"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:history.go(-1)\" value=\"上一筆\" style=\"display:".(($Front[count($Front)-1]=="list.php" OR $Front[count($Front)-1]=="history.php" OR $Front[count($Front)-1]=="edit.php")?"none":"")."\"> ";
    $EditList["Next"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Next('".$IdList."')\" value=\"下一筆\" style=\"display:".($IdList?"":"none")."\">
                            <script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Next(Id){
                                window.location.href = '".$Tp[0]."?id=' +Id +'&main=".$_GET["main"]."';
                            }
                            </script>";
    $EditList["Accapart"] = "<input type=\"button\" style=\"background-color:#3333FF;color:#FFFFFF\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:ChangeAcc(1)\" value=\"明細依單\"> ";
    $EditList["Accmerge"] = "<input type=\"button\" style=\"background-color:#3333FF;color:#FFFFFF\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:ChangeAcc(2)\" value=\"明細合併\"> ";
    $EditList["Delete"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Delete()\" value=\"刪除".$OtherStr[0]."\"> ";
    $EditList["Delete_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Delete(){
                                var obj = document.getElementById(\"basic\");
                                var del = 0;
                                var item = new Array();
                                var m=obj.rows.length;
                                if(m>=6)
                                {
                                    for(var i=5;i<m;i++){
                                        if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled ){
                                            item[item.length] = obj.rows[i].cells[0].firstChild.value;
                                            del++;
                                        }
                                    }
                                }
                                if(!del) alert(\"請選擇欲刪除的項目!!\");
                                else if(confirm(\"確定要刪除選擇的項目?? \")) window.location.href = \"".$Tp[0]."\" +\"?id=$_GET[id]&type=del&delid=\" +item;
                            }
                           </script>";
    $EditList["Check"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Check()\" value=\"確認".$OtherStr[0]."\"> ";
    $EditList["Check_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Check(){
                                var obj = document.getElementById('pro_list');
                                for(var i=ItemRowNum;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled ){
                                        for(var j=0;j<obj.rows[i].cells.length;j++){
                                            if(obj.rows[i].cells[j].firstChild.type == 'text') obj.rows[i].cells[j].firstChild.readOnly = true;
                                            else obj.rows[i].cells[j].firstChild.disabled = true;
                                        }
                                    }
                                }
                            }
                            </script>";
    $EditList["Printedit"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:document.getElementById('print').value=1;Sendsubmit(document.form1);\" value=\"列印".$OtherStr[0]."\"> ";
    $EditList["Print"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Print()\" value=\"列印".$OtherStr[0]."\"> ";
    $EditList["Apply"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Apply()\" value=\"完成".$OtherStr[0]."\"> ";
    $EditList["Apply_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Apply(){
                                if(confirm(\"確定單據內容無誤?? \")){
                                    var obj = document.form1;
                                    obj.apply.value = 1;
                                    Sendsubmit(obj);
                                }
                            }
                          </script>";
    $EditList["Voucok"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:Voucok()\" value=\"轉正式傳票\">";
    $EditList["Voucok_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Voucok(){
                                if(confirm(\"確定要轉正式傳票?? \")){
                                    var obj = document.form1;
                                    obj.apply.value = 1;
                                    Sendsubmit(obj);
                                }
                            }
                          </script>";
    ###權限
    if(strlen($GLOBALS["ThisAuth"][0])){
    
        for($i=0;$i<count($GLOBALS["ThisAuth"][1]);$i++){
            if(!substr($GLOBALS["ThisAuth"][0],$i,1)){
                if($GLOBALS["ThisAuth"][1][$i] == "del"){
                    $EditList["Delete"] = "";
                    $EditList["Clear"] = "";
                }else{
                     $EditList[ucfirst($GLOBALS["ThisAuth"][1][$i])] = "";
                     $EditList[ucfirst($GLOBALS["ThisAuth"][1][$i])."_val"] = "";
                }
            }
        }
    }
    
    $Out = "";
    for($i=0;$i<count($List);$i++){
        $NewList = explode("@",$List[$i]);
        if($EditList[$NewList[0]]!=''){
            $strlen = $_SESSION["font"]=="text"?14:18;
            $NewButtonName = "<input type=\"button\" ".($NewList[2]?"style=\"width:".($NewList[2]*$strlen)."px\"":"")." class=\"button_input\" onClick=\"javascript:".$NewList[0]."()\" value=\"".$NewList[1]."\"> ";
            $Out .=($NewList[1]?$NewButtonName:$EditList[$NewList[0]]).$EditList[$NewList[0]."_val"];
        }
    }
    return $Out;
}
#----------------2009.05.22-----------------------------
#說明：產生按鈕列
#輸入：按鈕陣列 , 是否傳問號參數值給另一頁 , 統一多加的預設值名稱(加於名稱哪個位置 , 看個案而定)@寬度
#輸出：按鈕列
#-------------------------------------------------------
function ScanAction($List,$Qs = "",$OtherStr=""){
    $Tmp = explode("_",$_SERVER["PHP_SELF"]);
    $Page = substr($_SERVER["PHP_SELF"],0,-strlen($Tmp[count($Tmp)-1]));
    $OtherStr = explode("@",$OtherStr);
    $OtherWidth = $OtherStr[1]?"style=\"width:".($OtherStr[1]+($_SESSION["font"]=='text'?0:20))."px\"":"";
    $EditList["Fabricate"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Fabricate()\" value=\"編輯".$OtherStr[0]."規格\"> ";
    $EditList["Fabricate_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Fabricate(){
                                var obj = document.getElementById(\"make_list\");
                                var bok = 0;
                                var qs = \"\";
                                var item = 0;
                                for(var i=ItemRowNum;i<=obj.rows.length-1;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled ){
                                        bok = bok+1;
                                        qs = qs?\"||\"+obj.rows[i].cells[pserial].firstChild.value+\" - \"+obj.rows[i].cells[title].firstChild.value+\"|-|\"+encodeURIComponent(obj.rows[i].cells[freight].firstChild.alt):obj.rows[i].cells[pserial].firstChild.value+\" - \"+obj.rows[i].cells[title].firstChild.value+\"|-|\"+encodeURIComponent(obj.rows[i].cells[freight].firstChild.alt);
                                        item = i;
                                    }
                                }
                                if(bok==0){
                                    alert(\"請選擇欲編輯規格的組裝商品!!\");
                                    return;
                                }
                                else if(bok>1){
                                    alert(\"一次只能編輯一個項目的規格!!\");
                                    return;
                                }
                                NewWindow('".$Page."make.php?id=' +qs+'&order_serial='+document.form1.serial.value+'&item='+item);
                            }
                           </script>";
    $EditList["Delete"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Delete()\" value=\"刪除".$OtherStr[0]."\"> ";
    $EditList["Delete_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Delete(){
                                var obj = document.getElementById(\"pro_list\");
                                var DelNum = obj.rows.length-1;
                                for(var i=DelNum;i>=ItemRowNum;i--){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled) obj.deleteRow(i);
                                }
                                if(document.getElementById('carry')) Carray_total();
                                if(document.getElementById('money')) Total();
                                if(document.getElementById('sumavg')) SubAvg();
                            }
                           </script>";
    $EditList["Deletemake"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Deletemake()\" value=\"刪除".$OtherStr[0]."\"> ";
    $EditList["Deletemake_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Deletemake(){
                                var obj = document.getElementById(\"make_list\");
                                var DelNum = obj.rows.length-1;
                                for(var i=DelNum;i>=ItemRowNum;i--){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled) obj.deleteRow(i);
                                }
                                if(document.getElementById('carry')) Carray_total();
                                if(document.getElementById('money')) Total();
                                if(document.getElementById('sumavg')) SubAvg();
                            }
                           </script>";
    
    $EditList["Check"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Check()\" value=\"確認".$OtherStr[0]."\"> ";
    $EditList["Check_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Check(){
                                var obj = document.getElementById('pro_list');
                                for(var i=ItemRowNum;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled){
                                        for(var j=0;j<obj.rows[i].cells.length;j++){
                                            if(obj.rows[i].cells[j].firstChild.type == 'text') obj.rows[i].cells[j].firstChild.readOnly = true;
                                            else obj.rows[i].cells[j].firstChild.disabled = true;
                                        }
                                    }
                                }
                            }
                            </script>";
    $EditList["Chose"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Chose()\" value=\"選擇".$OtherStr[0]."產品\"> ";
    $EditList["Chose_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Chose(){
                                if(document.form1.cus_cate && !document.form1.cus_cate.value ) alert(\"請先選擇客戶分類!!\");
                                else if(document.form1.cus_serial && !document.form1.cus_serial.value) alert(\"請先選擇客戶!!\");
                                else if(document.form1.sup_serial && !document.form1.sup_serial.value) alert(\"請先選擇廠商!!\");
                                else if(document.form1.sto_id && !document.form1.sto_id.value) alert(\"請先選擇倉庫!!\");
                                else if(document.form1.carry && !document.form1.carry.value) alert(\"請先選擇貨運方式!!\");
                                else{
                                    var Sto = '';
                                    var Sup = '';
                                    var Vid = '';
                                    if(document.form1.sto_id) Sto = document.form1.sto_id.value.split('||');
                                    if(document.form1.sup_serial) Sup = document.form1.sup_serial.value.split('||');
                                    if(document.form1.valuta) Vid = document.form1.valuta.value.split('||');
                                    if(document.form1.carry) Carry = encodeURIComponent(document.form1.carry.value).split('||');
                                    var qs = ".($Qs?"'?carry=' +(document.form1.carry?Carry[0]:'') +'&cusclass=' +(document.form1.cusclass?document.form1.cusclass.value:'') +'&discount=' +(document.form1.discount?document.form1.discount.value:'') +'&rate=' +(document.form1.rate?document.form1.rate.value:'') +'&valuta=' +Vid[0] +'&sup=' +(Sup[0]?Sup[0]:'') +'&sto=' +Sto[0];":"''")."
                                    NewWindow('".$Page."pop.php' +qs);
                                }
                            }
                            </script>";
    $EditList["ChoseCarry"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:ChoseCarry()\" value=\"".$OtherStr[0]."貨運方式\"> ";
    $EditList["ChoseCarry_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function ChoseCarry(){
                                if(document.form1.cus_cate && !document.form1.cus_cate.value ) alert(\"請先選擇客戶分類!!\");
                                else if(document.form1.cus_serial && !document.form1.cus_serial.value) alert(\"請先選擇客戶!!\");
                                else if(document.form1.sup_serial && !document.form1.sup_serial.value) alert(\"請先選擇廠商!!\");
                                else if(document.form1.sto_id && !document.form1.sto_id.value) alert(\"請先選擇倉庫!!\");
                                else if(document.form1.carry && !document.form1.carry.value) alert(\"請先選擇貨運方式!!\");
                                else{
                                    var Sto = '';
                                    var Sup = '';
                                    var Vid = '';
                                    if(document.form1.sto_id) Sto = document.form1.sto_id.value.split('||');
                                    if(document.form1.sup_serial) Sup = document.form1.sup_serial.value.split('||');
                                    if(document.form1.valuta) Vid = document.form1.valuta.value.split('||');
                                    if(document.form1.carry) Carry = encodeURIComponent(document.form1.carry.value).split('||');
                                    var qs = ".($Qs?"'?carry=' +(document.form1.carry?Carry[0]:'') +'&cusclass=' +(document.form1.cusclass?document.form1.cusclass.value:'') +'&discount=' +(document.form1.discount?document.form1.discount.value:'') +'&rate=' +(document.form1.rate?document.form1.rate.value:'') +'&valuta=' +Vid[0] +'&sup=' +(Sup[0]?Sup[0]:'') +'&sto=' +Sto[0];":"''")."
                                    NewWindow('".$Page."pop.php' +qs);
                                }
                            }
                            </script>";
    $EditList["Chosemake"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Chosemake()\" value=\"選擇".$OtherStr[0]."產品\"> ";
    $EditList["Chosemake_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Chosemake(){
                                if(document.form1.cus_cate && !document.form1.cus_cate.value ) alert(\"請先選擇客戶分類!!\");
                                else if(document.form1.cus_serial && !document.form1.cus_serial.value) alert(\"請先選擇客戶!!\");
                                else if(document.form1.sup_serial && !document.form1.sup_serial.value) alert(\"請先選擇廠商!!\");
                                else if(document.form1.sto_id && !document.form1.sto_id.value) alert(\"請先選擇倉庫!!\");
                                else if(document.form1.carry && !document.form1.carry.value) alert(\"請先選擇貨運方式!!\");
                                else{
                                    var Sto = '';
                                    var Sup = '';
                                    var Vid = '';
                                    var Carry = '';
                                    if(document.form1.sto_id) Sto = document.form1.sto_id.value.split('||');
                                    if(document.form1.sup_serial) Sup = document.form1.sup_serial.value.split('||');
                                    if(document.form1.valuta) Vid = document.form1.valuta.value.split('||');
                                    if(document.form1.carry) Carry = encodeURIComponent(document.form1.carry.value).split('||');
                                    var qs = ".($Qs?"'?carry=' +(document.form1.carry?Carry[0]:'') +'&cusclass=' +(document.form1.cusclass?document.form1.cusclass.value:'') +'&discount=' +(document.form1.discount?document.form1.discount.value:'') +'&rate=' +(document.form1.rate?document.form1.rate.value:'') +'&valuta=' +Vid[0] +'&sup=' +(Sup[0]?Sup[0]:'') +'&sto=' +Sto[0];":"''")."
                                    //var qs = ".($Qs?"'?cusclass=' +(document.form1.cusclass?document.form1.cusclass.value:'') +'&discount=' +(document.form1.discount?document.form1.discount.value:'') +'&rate=' +(document.form1.rate?document.form1.rate.value:'') +'&valuta=' +Vid[0] +'&sup=' +(Sup[0]?Sup[0]:'') +'&sto=' +Sto[0];":"''")."
                                    NewWindow('".$Page."pop_make.php' +qs);
                                }
                            }
                            </script>";
    $EditList["Chomakedetail"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Chomakedetail()\" value=\"選擇".$OtherStr[0]."產品\"> ";
    $EditList["Chomakedetail_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                                    function Chomakedetail(){
                                        NewWindow('".$Page."pop.php' +qs);
                                    }
                                    </script>";
    $EditList["Load"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Load()\" value=\"載入".$OtherStr[0]."明細\"> ";
    $EditList["Load_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Load(){
                                var Per = document.form1.person.value.split('||');
                                var qs = '?per=' +Per[0];
                                if(document.form1.cus_serial){
                                    if(!document.form1.cus_serial.value){
                                        alert(\"請先選擇客戶!!\");
                                        return;
                                    }else{
                                        var Cus = document.form1.cus_serial.value.split('||');
                                        qs += '&cus=' +Cus[0];
                                    }
                                }
                                if(document.form1.sup_serial){
                                    if(!document.form1.sup_serial.value){
                                        alert(\"請先選擇廠商!!\");
                                        return;
                                    }else{
                                        var Sup = document.form1.sup_serial.value.split('||');
                                        qs += '&sup=' +Sup[0];
                                    }
                                }
                                if(document.form1.sto_id){
                                    if(!document.form1.sto_id.value){
                                        alert(\"請先選擇倉庫!!\");
                                        return;
                                    }else{
                                        var Sto = document.form1.sto_id.value.split('||');
                                        qs += '&sto=' +Sto[0];
                                    }
                                }
                                NewWindow('".$Page."chose.php' +qs);
                            }
                            </script>";
    $EditList["AddVouc"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:AddVouc()\" value=\"新增".$OtherStr[0]."\"> ";
    $EditList["DelVouc"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:DelVouc()\" value=\"刪除".$OtherStr[0]."\"> ";
    $EditList["Credit"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Credit()\" value=\"刷卡\"> ";
    $EditList["Credit_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Credit(){
                                if(document.form1.cus_cate && !document.form1.cus_cate.value ) alert(\"請先選擇客戶分類!!\");
                                else if(document.form1.cus_serial && !document.form1.cus_serial.value) alert(\"請先選擇客戶!!\");
                                else if(document.form1.sup_serial && !document.form1.sup_serial.value) alert(\"請先選擇廠商!!\");
                                else if(document.form1.sto_id && !document.form1.sto_id.value) alert(\"請先選擇倉庫!!\");
                                else if(document.form1.carry && !document.form1.carry.value) alert(\"請先選擇貨運方式!!\");
                                var obj = document.getElementById(\"money\");
                                document.form1.notax.value = 'yes';
                                Total();
                            }
                            </script>";
    $Out = "";
    for($i=0;$i<count($List);$i++){
        $NewList = explode("@",$List[$i]);
        if($EditList[$NewList[0]]!=''){
            $strlen = $_SESSION["font"]=="text"?14:18;
            $NewButtonName = "<input type=\"button\" ".($NewList[2]?"style=\"width:".($NewList[2]*$strlen)."px\"":"")." class=\"button_input\" onClick=\"javascript:".$NewList[0]."()\" value=\"".$NewList[1]."\"> ";
            $Out .=($NewList[1]?$NewButtonName:$EditList[$NewList[0]]).$EditList[$NewList[0]."_val"];
        }
    }
    return $Out;
}
#----------------2009.05.22-----------------------------
#說明：產生按鈕列
#輸入：按鈕陣列 , 統一多加的預設值名稱(加於名稱哪個位置 , 看個案而定)@寬度
#輸出：按鈕列
#-------------------------------------------------------
function PopAction($List,$OtherStr=""){
    $OtherStr = explode("@",$OtherStr);
    $OtherWidth = $OtherStr[1]?"style=\"width:".($OtherStr[1]+($_SESSION["font"]=='text'?0:20))."px\"":"";
    $EditList["Chose"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:Chose()\" value=\"加入".$OtherStr[0]."\"> ";
    $EditList["Chose_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function Chose(){
                                var obj = document.getElementById(\"MyList\");
                                var MyObj = window.opener.document.getElementById(\"pro_list\");
                                for(var i=0;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled){
                                        var tmpfld = JoinFld.split(',');
                                        for(var o=0;o<tmpfld.length;o++){
                                            window.opener.document.getElementById(tmpfld[o]).value = obj.rows[i].cells[parseInt(o)+2].firstChild.value;
                                        }
                                        for(var j=window.opener.ItemRowNum;j<obj.rows[0].cells.length;j++){
                                        
                                            switch(MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.type){
                                                case 'text':
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.value = obj.rows[i].cells[j].firstChild.value;
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.alt = obj.rows[i].cells[j].firstChild.alt;
                                                    break;
                                                case 'select-one':
                                                    for(var k=0;k<obj.rows[i].cells[j].firstChild.options.length;k++){
                                                        var x = document.createElement('option');
                                                        x.text = obj.rows[i].cells[j].firstChild.options[k].text;
                                                        x.value = obj.rows[i].cells[j].firstChild.options[k].value;
                                                        x.selected = obj.rows[i].cells[j].firstChild.options[k].selected;
                                                        if(navigator.appName != \"Netscape\") window.opener.SelAdd((window.opener.ItemRowNum-1),(j-2),x);
                                                        else{
                                                            try{
                                                                MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.add(x,null);
                                                            }catch(ex){
                                                                 MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.add(x);
                                                            }
                                                        }
                                                    }
                                                    
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.options[obj.rows[i].cells[j].firstChild.selectedIndex].selected = true;
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.disabled = obj.rows[i].cells[j].firstChild.disabled;
                                                    break;
                                                default:break;
                                            }
                                        }
                                        window.opener.Add();
                                        document.body.focus();
                                        obj.rows[i].cells[0].firstChild.disabled = true;
                                    }
                                }
                            }
                          </script>";
    $EditList["ChosePay"] = "<input type=\"button\" class=\"button_input\" ".$OtherWidth." onClick=\"javascript:ChosePay()\" value=\"加入".$OtherStr[0]."\"> ";
    $EditList["ChosePay_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                          	function ChosePay(){
                          	
                          	    var obj = document.getElementById(\"MyList\");
                          	    var MyObj = window.opener.document.getElementById(\"pro_list\");
                          	    for(var i=0;i<obj.rows.length;i++){
                          	        if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled){
                          	        	window.opener.document.form1.member_id.value = obj.rows[i].cells[2].firstChild.value;
                          	        	
                          	            for(var j=window.opener.ItemRowNum-1;j<obj.rows[0].cells.length;j++){
                                            switch(MyObj.rows[window.opener.ItemRowNum-1].cells[j-1].firstChild.type){
                                            
                                                case 'text':
                                                
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-1].firstChild.value = obj.rows[i].cells[j].firstChild.value;
                                                    break;
                                                case 'select-one':
                                                
                                                    for(var k=0;k<obj.rows[i].cells[j].firstChild.options.length;k++){
                                                        var x = document.createElement('option');
                                                        x.text = obj.rows[i].cells[j].firstChild.options[k].text;
                                                        x.value = obj.rows[i].cells[j].firstChild.options[k].value;
                                                        x.selected = obj.rows[i].cells[j].firstChild.options[k].selected;
                                                        if(navigator.appName != \"Netscape\") window.opener.SelAdd((window.opener.ItemRowNum-1),(j-1),x);
                                                        else{
                                                            try{
                                                                MyObj.rows[window.opener.ItemRowNum-1].cells[j-1].firstChild.add(x,null);
                                                            }catch(ex){
                                                                 MyObj.rows[window.opener.ItemRowNum-1].cells[j-1].firstChild.add(x);
                                                            }
                                                        }
                                                    }
                                                    
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-1].firstChild.options[obj.rows[i].cells[j].firstChild.selectedIndex].selected = true;
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-1].firstChild.disabled = obj.rows[i].cells[j].firstChild.disabled;
                                                    
                                                    break;
                                                default:break;
                                            }
                          	            }
                          	            window.opener.AddPay();
                          	            document.body.focus();
                          	            obj.rows[i].cells[0].firstChild.disabled = true;
                          	        }
                          	    }
                            }
                          </script>";
    $EditList["ChoseMake"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:ChoseMake()\" value=\"加入".$OtherStr[0]."\"> ";
    $EditList["ChoseMake_val"] = "<script language=\"JavaScript\" type=\"text/JavaScript\">
                            function ChoseMake(){
                                var obj = document.getElementById(\"MyList\");
                                var MyObj = window.opener.document.getElementById(\"make_list\");
                                for(var i=0;i<obj.rows.length;i++){
                                    if(obj.rows[i].cells[0].firstChild.checked && !obj.rows[i].cells[0].firstChild.disabled){
                                        var tmpfld = JoinFld.split(',');
                                        for(var o=0;o<tmpfld.length;o++){
                                            window.opener.document.getElementById(tmpfld[o]+'_make').value = obj.rows[i].cells[parseInt(o)+2].firstChild.value;
                                        }
                                        for(var j=window.opener.ItemRowNum;j<obj.rows[0].cells.length;j++){
                                        
                                            switch(MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.type){
                                                case 'text':
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.value = obj.rows[i].cells[j].firstChild.value;
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.alt = obj.rows[i].cells[j].firstChild.alt;
                                                    break;
                                                case 'select-one':
                                                    for(var k=0;k<obj.rows[i].cells[j].firstChild.options.length;k++){
                                                        var x = document.createElement('option');
                                                        x.text = obj.rows[i].cells[j].firstChild.options[k].text;
                                                        x.value = obj.rows[i].cells[j].firstChild.options[k].value;
                                                        x.selected = obj.rows[i].cells[j].firstChild.options[k].selected;
                                                        if(navigator.appName != \"Netscape\") window.opener.SelAddMake((window.opener.ItemRowNum-1),(j-2),x);
                                                        else{
                                                            try{
                                                                MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.add(x,null);
                                                            }catch(ex){
                                                                 MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.add(x);
                                                            }
                                                        }
                                                    }
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.options[obj.rows[i].cells[j].firstChild.selectedIndex].selected = true;
                                                    MyObj.rows[window.opener.ItemRowNum-1].cells[j-2].firstChild.disabled = obj.rows[i].cells[j].firstChild.disabled;
                                                    break;
                                                default:break;
                                            }
                                        }
                                        window.opener.AddMake();
                                        document.body.focus();
                                        obj.rows[i].cells[0].firstChild.disabled = true;
                                    }
                                }
                            }
                          </script>";
    $EditList["Close"] = "<input type=\"button\" ".$OtherWidth." class=\"button_input\" onClick=\"javascript:str=true;window.close()\" value=\"關閉".$OtherStr[0]."視窗\"> ";
    $Out = "";
    for($i=0;$i<count($List);$i++){
        $NewList = explode("@",$List[$i]);
        if($EditList[$NewList[0]]!=''){
            $strlen = $_SESSION["font"]=="text"?14:18;
            $NewButtonName = "<input type=\"button\" ".($NewList[2]?"style=\"width:".($NewList[2]*$strlen)."px\"":"")." class=\"button_input\" onClick=\"javascript:".$NewList[0]."()\" value=\"".$NewList[1]."\"> ";
            $Out .=($NewList[1]?$NewButtonName:$EditList[$NewList[0]]).$EditList[$NewList[0]."_val"];
        }
    }
    return $Out."　&nbsp;";
}
#多公司
function Company(){
    return $GLOBALS["COMPANY"]["Exist"]?($_SESSION['admin']['company']?$_SESSION['admin']['company']:$GLOBALS["COMPANY"]["Serial"][0]):$GLOBALS["COMPANY"]["Serial"][0];
}

#產品搜尋欄位serial
function SearchFld($key,$DB = ""){
    $Fld = array_merge($GLOBALS["SEARCH_FILED"],$GLOBALS["BASIC_FILED"]["Fld"]);
    for($i=0;$i<count($Fld);$i++){
        $Query = ($DB?($DB."."):"").$Fld[$i]." LIKE '%$key%'";
        $Str?($Str .= " OR ".$Query):$Str = $Query;
    }
    return $Str;
}

#明細搜尋欄位pserial
function PSearchFld($key,$DB = ""){
    $Fld = array_merge($GLOBALS["JOIN_FILED"],$GLOBALS["BASIC_FILED"]["Fld"]);
    for($i=0;$i<count($Fld);$i++){
        $Query = ($DB?($DB."."):"").$Fld[$i]." LIKE '%$key%'";
        $Str?($Str .= " OR ".$Query):$Str = $Query;
    }
    return $Str;
}
#刪除關聯資料
function DelList($DB1,$DB2,$Id){
    $Del = ArrayListing($DB1,$Id);
    for($i=1;$i<count($Del);$i++){
        DB_DELETE($DB1,"id='".$Del[$i]['id']."'");
        DB_DELETE($DB2,"uid='".$Del[$i]['id']."'");
    }
    DB_DELETE($DB1,"id='$Id'");
    DB_DELETE($DB2,"uid='$Id'");
}

#搜尋欄位
function SFiled($Wid = ""){
    
    $Head = "";
    $Hidden = "";
    for($i=0;$i<count($GLOBALS["JOIN_FILED"]);$i++){
        $Head .= "<td ".($Wid?("width=\"".$Wid *$GLOBALS["JOIN_TITLE"]["Percent"][$i]."%\""):"").">".$GLOBALS["JOIN_TITLE"][$i]."</td>";
        $Hidden .= "<input type=\"hidden\" name=\"".$GLOBALS["JOIN_FILED"][$i]."\" id=\"".$GLOBALS["JOIN_FILED"][$i]."\" value=\"\">";
    }
    return Array(count($GLOBALS["JOIN_FILED"]),$Head,$Hidden);
}

#搜尋組裝商品欄位
function SFiled_Make($Wid = ""){
    
    $Head = "";
    $Hidden = "";
    for($i=0;$i<count($GLOBALS["JOIN_FILED"]);$i++){
        $Head .= "<td ".($Wid?("width=\"".$Wid *$GLOBALS["JOIN_TITLE"]["Percent"][$i]."%\""):"").">".$GLOBALS["JOIN_TITLE"][$i]."</td>";
        $Hidden .= "<input type=\"hidden\" name=\"".$GLOBALS["JOIN_FILED"][$i]."_make\" id=\"".$GLOBALS["JOIN_FILED"][$i]."_make\" value=\"\">";
    }
    return Array(count($GLOBALS["JOIN_FILED"]),$Head,$Hidden);
}

#產品欄位
function Filed($Wid = ""){
    $Head = "";
    $Filed = "";
    for($i=0;$i<count($GLOBALS["BASIC_FILED"]["Fld"]);$i++){
        $Head .= "<td ".($Wid?("width=\"".$Wid *$GLOBALS["BASIC_FILED"]["Percent"][$i]."%\""):"").">".$GLOBALS["BASIC_FILED"]["Title"][$i]."</td>";
        $Filed .= "<td ".($Wid?("width=\"".$Wid *$GLOBALS["BASIC_FILED"]["Percent"][$i]."%\""):"")."><input type=\"text\" class=\"page_main_right_input_01\" style=\"width:96%; display: none\" readonly></td>";
    }
    return Array($Head,$Filed);
}



#客戶列表-2
function Customer($Cate,$Cus,$Func1 = "",$Dis="",$Func2 = ""){
    #分類
    $Catalog = "<select name=\"cus_cate\" class=\"page_main_right_select_02\" onChange=\"javascript:CusList(this.value);".$Func1."\" $Dis><option value=\"\">-- 請選擇 --</option>";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_CUSCATE] ORDER BY sort ASC, serial");
    while($ROW=mysql_fetch_array($RES)){
        $Catalog .= "<option value=\"".$ROW["serial"]."\" ".($ROW["serial"]==$Cate?"selected":"").">".$ROW["serial"]." - ".$ROW["title"]."</option>";
    }
    $Catalog .= "</select>";
    #列表
    $Customer = "<select name=\"cus_serial\" class=\"page_main_right_select_02\" onChange=\"javascript:DisRate(this.value);".$Func2."\" $Dis>";
    $RES = DB_QUERY("SELECT DB1.* FROM $GLOBALS[DB_CUSTOMER] AS DB1
                     LEFT JOIN $GLOBALS[DB_CUSCOMP] AS DB2 ON DB1.serial=DB2.cid
                     WHERE DB1.uid='".$Cate."' AND DB2.company='".Company()."' ORDER BY DB1.serial");
    while($ROW=mysql_fetch_array($RES)){
        $Customer .= "<option value=\"".$ROW["serial"]."||".$ROW["twname"]."\" ".($ROW["serial"]==$Cus?"selected":"").">".$ROW["serial"]." - ".$ROW["twname"]."</option>";
    }

    if(!mysql_num_rows($RES)) $Customer .= "<option value=\"\">-- 請先選擇分類 --</option>";
    $Customer .= "</select>
                  <input type=\"text\" class=\"page_main_right_input_01\" onKeyUp=\"javascript:FindCus(event,this.value);".$Func2."\" ".($Dis?'readonly':'')."><span style=\"color:#CC0000\"> 請輸入客戶編號,再點選ENTER</span>";
    return $Catalog." ".$Customer;
}


#廠商列表
function Supply($Cate,$Sup,$Func = "",$Dis="",$Func2=""){
    #分類
    $Catalog = "<select name=\"sup_cate\" class=\"page_main_right_select_02\" onChange=\"javascript:SupList(this.value)\" $Dis><option value=\"\">-- 請選擇 --</option>";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_SUPCATE] ORDER BY sort DESC, serial");
    while($ROW=mysql_fetch_array($RES)){
        $Catalog .= "<option value=\"".$ROW["serial"]."\" ".($ROW["serial"]==$Cate?"selected":"").">".$ROW["serial"]." - ".$ROW["title"]."</option>";
    }
    $Catalog .= "</select>";
    #列表
    $Supply = "<select name=\"sup_serial\" class=\"page_main_right_select_02\" ".($Func?("onChange=\"javascript:".$Func."\""):"")." $Dis>";
    $RES = DB_QUERY("SELECT DB1.* FROM $GLOBALS[DB_SUPPLY] AS DB1
                     LEFT JOIN $GLOBALS[DB_SUPCOMP] AS DB2 ON DB1.serial=DB2.sid
                     WHERE DB1.uid='".$Cate."' AND DB2.company='".Company()."' ORDER BY DB1.serial");
    while($ROW=mysql_fetch_array($RES)){
        $Supply .= "<option value=\"".$ROW["serial"]."||".$ROW["twname"]."\" ".($ROW["serial"]==$Sup?"selected":"").">".$ROW["serial"]." - ".$ROW["twname"]."</option>";
    }
    if(!mysql_num_rows($RES)) $Supply .= "<option value=\"\">-- 請先選擇分類 --</option>";
    $Supply .= "</select>
                  <input type=\"text\" class=\"page_main_right_input_01\" onKeyUp=\"javascript:FindSupply(event,this.value);".$Func2."\" ".($Dis?'readonly':'')."><span style=\"color:#CC0000\"> 請輸入廠商編號,再點選ENTER</span>";
    return $Catalog." ".$Supply;
}

#----------------2009.07.15-----------------------------
#說明：部門列表
#輸入：部門編號 , 人員編號 , paper代碼 , 部門與人員兩個下拉式onchange函數 , 是否disabled (true/false)
#輸出：部門與人員下拉式
#-------------------------------------------------------
function Dept($Did,$Pid,$Paper = "",$Func = "",$Dis="false"){
    if($Dis=="false"){
        #人員基本資料
        $Dis = false;
        if(!$Did){
            $RES = DB_QUERY("SELECT DB2.* FROM $GLOBALS[DB_PERSON] AS DB1
                             LEFT JOIN $GLOBALS[DB_PER2JOB] AS DB2 ON DB2.pid=DB1.serial
                             WHERE DB1.loginid='".$_SESSION['admin']['user']."'");
            if(mysql_num_rows($RES)){
                $ROW = mysql_fetch_array($RES);
                $Did = trim($ROW["did"]);
                $Pid = $ROW["pid"];
                $Dis = true;
            }
        }else  $Dis = true;
    }
    else $Dis = true;

    #部門列表
    $Dept = "<select name=\"dept\" class=\"page_main_right_select_02\" onChange=\"javascript:PerList(this.value);".($Func?$Func:"")."\" ".($Dis?"disabled":"").">";
    if(!$Did) $Dept .= "<option value=\"\">-- 請選擇 --</option>";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_DEPT] ORDER BY serial");
    while($ROW=mysql_fetch_array($RES)){
        $Dept .= "<option value=\"".$ROW["serial"]."||".$ROW["title"]."\" ".($ROW["serial"]==$Did?"selected":"").">".$ROW["serial"]." - ".$ROW["title"]."</option>";
    }
    $Dept .= "</select>";
    #人員列表
    $Person = "<select name=\"person\" class=\"page_main_right_select_02\" ".($Func?("onChange=\"javascript:".$Func."\""):"").($Dis?" disabled":"").">";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_PER2JOB] WHERE did='".$Did."' ORDER BY jid, pid");
    while($ROW=mysql_fetch_array($RES)){
        $Person .= "<option value=\"".$ROW["pid"]."||".$ROW["person_name"]."\" ".($ROW["pid"]==$Pid?"selected":"").">".$ROW["pid"]." - ".$ROW["person_name"]."</option>";
    }
    if(!mysql_num_rows($RES)) $Person .= "<option value=\"\">-- 請先選擇部門 --</option>";
    $Person .= "</select>";
    
    return $Dept." ".$Person;
}

#倉庫列表
function Storage($Cate,$Sto,$Func = "",$Dis = "",$Where = ""){
    #分類
    $Catalog = "<select name=\"sto_cate\" class=\"page_main_right_select_02\" onChange=\"javascript:StoList(this.value);".($Func?$Func:"")."\" ".$Dis."><option value=\"\">-- 請選擇 --</option>";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_STOCATE] ORDER BY sort DESC, id");
    while($ROW=mysql_fetch_array($RES)){
        $Catalog .= "<option value=\"".$ROW["serial"]."\" ".($ROW["serial"]==$Cate?"selected":"").">".$ROW["serial"]." - ".$ROW["title"]."</option>";
    }
    $Catalog .= "</select>";
    #列表
    $Storage = "<select name=\"sto_id\" class=\"page_main_right_select_02\" ".($Func?("onChange=\"javascript:".$Func."\""):"")." ".$Dis.">";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_STORAGE] WHERE uid='".$Cate."' AND company='".Company()."' ".$Where." ORDER BY serial");
    while($ROW=mysql_fetch_array($RES)){
        $Storage .= "<option value=\"".$ROW["serial"]."||".$ROW["title"]."\" ".($ROW["serial"]==$Sto?"selected":"").">".$ROW["serial"]." - ".$ROW["title"]."</option>";
    }
    if(!mysql_num_rows($RES)) $Storage .= "<option value=\"\">-- 請先選擇分類 --</option>";
    $Storage .= "</select>";
    return $Catalog." ".$Storage;
}

/****************系統自動拋轉傳票
$type => 傳票來源對象||狀態(訂金 , 銷貨 , 採購)
$total => 傳票總金額
$debit => 借方科目+金額||摘要 
$credit => 貸方科目+金額||摘要
*/
function ToVouc($type,$total,$debit,$credit,$node=""){
    list($kind,$formal_type)=explode("||",$type);
    $Filed = Array("node","date","kind","serial","debit_total","credit_total","formal_type","company","createdate","creat");
    $NewSerial=Serial('vouc_list',substr($_POST["date"],0,10),$GLOBALS["DB_VOUCLIST"]);
    $Value = Array($node,date("Y-m-d"),$kind,$NewSerial,$total,$total,$formal_type,Company(),date("Y-m-d H:i:s"),"系統產生");
    DB_INSERT($GLOBALS["DB_VOUCLIST"],$Filed,$Value);
    DB_QUERY("UPDATE $GLOBALS[DB_PAPER] SET last_serial=last_serial+1, last_date='".date("Y-m-d")."' WHERE paper='vouc_list'");
    #明細
    $Filed = Array("uid","acc_serial","acc_title","content","debit_price","credit_price");
    foreach($debit as $key =>$val){//借方
        $RES=DB_QUERY("SELECT title FROM $GLOBALS[DB_ACCOUNT] WHERE serial='".$key."'");
        $ROW=mysql_fetch_array($RES);
        $content=explode("||",$val);
        if($content[0]>0){
            $tmp = Array($NewSerial , $key ,$ROW["title"] , $content[1],$content[0],0);
            DB_INSERT($GLOBALS["DB_VOUCITEM"],$Filed,$tmp);
        }
    }
    foreach($credit as $key =>$val){//貸方
        $RES=DB_QUERY("SELECT title FROM $GLOBALS[DB_ACCOUNT] WHERE serial='".$key."'");
        $ROW=mysql_fetch_array($RES);
        $content=explode("||",$val);
        if($content[0]>0){
            $tmp = Array($NewSerial , $key ,$ROW["title"] , $content[1],0,$content[0]);
            DB_INSERT($GLOBALS["DB_VOUCITEM"],$Filed,$tmp);
        }
    }
}

#結帳月份
function PayUpMonth($Month){
    $Month = $Month?$Month:date("m");
    $Out = "<select name=\"payup_month\" class=\"page_main_right_select_02\">";
    for($i=0;$i<count($GLOBALS["PAYUP_MONTH"]);$i++){
        $Out .= "<option value=\"".sprintf("%02d",$i+1)."\" ".(($Month == sprintf("%02d",$i+1))?"selected":"").">".$GLOBALS["PAYUP_MONTH"][$i]."</option>";
    }
    $Out .= "</select>";
    return $Out;
}
#結帳月份(含年份)
function PayUpMonths($time,$Dis=""){
    $time = $time?$time:date("Y-m");
    list($yy,$mm) = explode("-",$time);

    $Out = "<select name=\"payup_month\" class=\"page_main_right_select_02\" ".($Dis=="true"?'disabled':'').">";        
    for($i=$mm;$i<=12;$i++){  
        $temp = ($yy-1).'-'.sprintf("%02d",$i);
        $Out .='<option value="'.$temp.'" '.(($time==$temp)?'selected':'').'>'.$temp.'</option>';
    }             
    for($i=1;$i<=12;$i++){  
        $temp = $yy.'-'.sprintf("%02d",$i);
        $Out .='<option value="'.$temp.'" '.(($time==$temp)?'selected':'').'>'.$temp.'</option>';
    }        
    for($i=1;$i<=$mm;$i++){  
        $temp = ($yy+1).'-'.sprintf("%02d",$i);
        $Out .='<option value="'.$temp.'" '.(($time==$temp)?'selected':'').'>'.$temp.'</option>';
    }    
    $Out .= "</select>";
    return $Out;    
}
#幣別
function Valuta($Vid){
    $Valuta = "<select name=\"valuta\" class=\"page_main_right_select_02\" disabled>";
    if($GLOBALS["VALUTA"]["Exist"]){
        $RES = DB_QUERY("SELECT *, CONCAT(name, \" ( \", title, \" )\") AS valuta FROM $GLOBALS[DB_VALUTA] ORDER BY name");
        while($ROW=mysql_fetch_array($RES)){
            $Valuta .= "<option value=\"".$ROW["name"]."||".$ROW["title"]."\" ".($ROW["name"]==$Vid?"selected":"")." ".($ROW["valuta"]==$Vid?"selected":"").">".$ROW["valuta"]."</option>";
        }
    }
    else{
        $Valuta .= "<option value=\"".$GLOBALS["VALUTA_ID"]."||".$GLOBALS["VALUTA_TITLE"]."\" ".($GLOBALS["VALUTA_ID"]==$Vid?"selected":"")." ".($GLOBALS["VALUTA_TITLE"]==$Vid?"selected":"").">".($GLOBALS["VALUTA_ID"] ." ( ".$GLOBALS["VALUTA_TITLE"]." )")."</option>";    
    }
    $Valuta .= "</select>";
    return $Valuta;
}

#稅率
function TaxRate($Tax){
    if(!$Tax){
        $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_OTHER]");
        $ROW = mysql_fetch_array($RES);
        $Tax = $ROW["tax"];
    }
    return $Tax;
}

#匯率
function Rate($Valuta,$Date = ""){
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_EXCHANGE] AS DB1
                     LEFT JOIN $GLOBALS[DB_EXCHANGE_VALUTA] AS DB2 ON DB2.uid=DB1.id
                     WHERE DB1.date<='".($Date?$Date:date("Y-m-d"))."' AND DB2.vid='".$Valuta."'");
    $ROW = mysql_fetch_array($RES);
    return $ROW["rate"]?$ROW["rate"]:1;
}

#費用列表
function Fee($Fid,$Name = ""){
    $Fee = "<select name=\"fee".$Name."_title\" class=\"page_main_right_select_02\"><option value=\"\">-- 請選擇 --</option>";
    for($i=0;$i<count($GLOBALS["STR_FEE"]);$i++){
        $Fee .= "<option value=\"".$GLOBALS["STR_FEE"][$i]."\" ".($GLOBALS["STR_FEE"][$i]==$Fid?"selected":"").">".$GLOBALS["STR_FEE"][$i]."</option>";
    }
    $Fee .= "</select>";
    return $Fee;
}

#退貨原因
function Reson($Rid){
    $Reson = "<select name=\"reson\" class=\"page_main_right_select_02\"><option value=\"\">-- 請選擇 --</option>";
    for($i=0;$i<count($GLOBALS["STR_RESON"]);$i++){
        $Reson .= "<option value=\"".$GLOBALS["STR_RESON"][$i]."\" ".($GLOBALS["STR_RESON"][$i]==$Rid?"selected":"").">".$GLOBALS["STR_RESON"][$i]."</option>";
    }
    $Reson .= "</select>";
    return $Reson;
}

#檢視資料
function ViewData($Str){
    return join(" - ",$Str);
}
/*日期查詢
$date1=>起日期的值
$date2=>迄日期的值
$Url=>日期小圖示的路徑
*/
function SelectDate($date1,$date2,$Url="../../",$TimeDis="false"){
    $Str = "<span class=\"page_main_str_date\">日期查詢:</span>
                        <input name=\"date1\" type=\"text\" class=\"page_main_right_input_01\" value=\"".$date1."\" id=\"date1\" >
                        <img align=\"middle\" border=\"0\" src=\"".$Url."_plugin/jscalendar/img.gif\" style=\"CURSOR:pointer\" onClick=\"ShowCalendar('date1',$TimeDis)\">
                        <span class=\"page_main_str_date\">~</span>
                        <input name=\"date2\" type=\"text\" class=\"page_main_right_input_01\" value=\"".$date2."\" id=\"date2\" >
                        <img align=\"middle\" border=\"0\" src=\"".$Url."_plugin/jscalendar/img.gif\" style=\"CURSOR:pointer\" onClick=\"ShowCalendar('date2',$TimeDis)\">";
    return $Str;
}

##採購計劃產品對應的廠商 
/*
$pserial ->產品編號
$i -> 欄位值
*/
function ApplySup($pserial,$i=""){
    $RES=DB_QUERY("SELECT * FROM $GLOBALS[DB_SUPPLY_ITEM] WHERE ".$GLOBALS["JOIN_FILED"][0]."='$pserial'");
    $supply = "<select name=\"Supply".$i."\" id=\"Supply".$i."\" class=\"page_main_right_select_02\" >";
    while($ROW=mysql_fetch_array($RES)){
        $supply .= "<option value=\"".$ROW["uid"]."\" ".($ROW["def"]?"selected":"").">".$ROW["uid"]." - ".$ROW["sup_title"]."</option>";
    }
    if(!mysql_num_rows($RES)) $supply .= "<option value='' >產品尚無建立廠商</option>";
    $supply .= "</select>";
    return $supply;
}
#求出目前最新匯率
/*
$valuta-> 幣別代碼
*/
function NewRate($valuta){
    if($valuta == $GLOBALS["VALUTA_ID"]) $rate = 1;
    else{
        $res_rate=DB_QUERY("SELECT B.rate FROM $GLOBALS[DB_EXCHANGE] AS A
            LEFT OUTER JOIN $GLOBALS[DB_EXCHANGE_VALUTA] AS B ON B.uid=A.id 
            WHERE A.date=(SELECT date FROM $GLOBALS[DB_EXCHANGE] ORDER BY date desc limit 0,1) AND B.vid='$valuta' ORDER BY A.date");
        $row_rate=mysql_fetch_array($res_rate);
        $rate=$row_rate["rate"];
    }
    return $rate;
    
}
#----------------2009.05.11-----------------------------
#說明：更新訂單狀態
#輸入：訂單編號
#-------------------------------------------------------
function ModifyOrderType($Serial){
    $RES = DB_QUERY("SELECT A.*,B.item FROM $GLOBALS[DB_ORDERLIST] AS A 
                        LEFT OUTER JOIN $GLOBALS[DB_ORDERTYPE] AS B ON B.order_serial=A.serial
                        WHERE A.serial='".$Serial."'");
    $ROW = mysql_fetch_array($RES);
    if($ROW["paybill"]=="現金制"){
        if($ROW["type"]==2){
            if(($ROW["item"]-1)==0){
                DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=wait+1 WHERE paper='ordernot_list'");
                DB_QUERY("UPDATE $GLOBALS[DB_ORDERLIST] SET type=4 WHERE serial='".$Serial."'");
            }
        }
    }else{
        if($ROW["type"]==2){
            if(($ROW["item"]-1)==0){
                DB_QUERY("UPDATE $GLOBALS[DB_ORDERLIST] SET type=3 WHERE serial='".$Serial."'");
            }
        }
    }
    DB_QUERY("UPDATE $GLOBALS[DB_ORDERTYPE] SET item=item-1 WHERE order_serial='".$Serial."'");
}

#貨運方式
function PayCarry($PValue,$PFunc="",$CValue,$CFunc="",$Dis=""){
    $Str = "<select id=\"pay\" name=\"pay\" class=\"page_main_right_select_02\" onChange=\"javascript:ChosePayCarry(this.value);".$PFunc."\" $Dis>
            <option value=\"\">-- 請選擇 --</option>";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_PAYMETHLIST] ORDER BY sort DESC,id DESC ");
    while($ROW=mysql_fetch_array($RES)){
        $Str .="<option value=\"".($ROW["title"]."||".$ROW["id"])."\" ".($ROW["title"]==$PValue?'selected':'').">".$ROW["title"]."</option>";
        if($PValue==$ROW["title"]) $uid = $ROW["id"];
    }
    $Str .="</select> ";
    
    
    $StrC.= "<select id=\"carry\" name=\"carry\" class=\"page_main_right_select_02\" ".($CFunc?"onChange=\"".$CFunc."\"":"")." $Dis>
            <option value=\"\">-- 請選擇 --</option>";
    $RES = DB_QUERY("SELECT A.*,B.class1,B.class2,B.class3,B.class4,B.island FROM $GLOBALS[DB_PAYMETHITEM] AS A LEFT OUTER JOIN $GLOBALS[DB_FREIGHT] AS B ON B.title = A.title WHERE A.uid='".$uid."'");
    while($ROW=mysql_fetch_array($RES)){
        $StrC .="<option value=\"".($ROW["title"]."||".$ROW["paymethod"]."||".$ROW["island"]."||".$ROW["class1"]."||".$ROW["class2"]."||".$ROW["class3"]."||".$ROW["class4"])."\" ".($ROW["title"]==$CValue?'selected':'').">".$ROW["title"]."</option>";
    }
    $StrC.="</select>";
    return $Str." ".$StrC;
}
#結帳方式
/*
    $objName -> 物件名稱
    $type->方式
*/
function payMethod($objName,$type,$Func=""){
    $Str = "<select name=\"".$objName."\" class=\"page_main_right_select_02\" ".($Func?"onChange=\"".$Func."\"":"")." >
            <option value=\"\">-- 請選擇 --</option>";
    for($i=0;$i<count($GLOBALS["PAYMETHOD"]);$i++){
          $Str .="<option value=\"".$GLOBALS["PAYMETHOD"][$i]."\" ".($GLOBALS["PAYMETHOD"][$i]==$type?'selected':'').">".$GLOBALS["PAYMETHOD"][$i]."</option>";
    }

    $Str .="</select>";
    return $Str;
}
##更新人工待採購數量
function UpdateApplyNum($pseiral,$apply_serial){
    $res = DB_QUERY("SELECT sum(num) as not_num FROM $GLOBALS[DB_BUYITEM] AS A 
                        LEFT OUTER JOIN $GLOBALS[DB_BUYLIST] AS B ON B.serial=A.uid 
                        WHERE A.pserial='".$pseiral."' and A.apply_serial='".$apply_serial."' and B.company='".Company()."' GROUP BY CONCAT(A.pserial,A.apply_serial)");
    $notNum=mysql_fetch_array($res);
    DB_QUERY("UPDATE $GLOBALS[DB_APPLYPRE] SET buy_num=".($notNum["not_num"]?$notNum["not_num"]:0)." WHERE pserial='".$pseiral."' and apply_serial='".$apply_serial."' AND company='".Company()."'");
    DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=(SELECT count(id) FROM $GLOBALS[DB_APPLYPRE] WHERE company='".Company()."' AND num-buy_num>0) WHERE paper='apply_detail' ");
}

##組成採購單
/*
$supply --> 陣列
*/
function AddBuyPaper($supply,$List_Field,$Item_Field,$item_array,$date=""){
    if($date=='') $date = date("Y-m-d");
    foreach($supply as $val=>$key ){
        $buy_serial=Serial("buy_list",$date,$GLOBALS["DB_BUYLIST"]);//採購單號
        $res=DB_QUERY("SELECT * FROM $GLOBALS[DB_SUPPLY] WHERE serial='$val'");
        $row=mysql_fetch_array($res);
        $rate = NewRate($row["valuta"]);#目前最新指定幣別的匯率
        //新增採購主檔
        DB_INSERT($GLOBALS["DB_BUYLIST"],$List_Field,Array($date,Company(),$buy_serial,$row["uid"],$val,$row["twname"],$row["discount"],$row["valuta"],$row["valuta_title"],$rate));
        $price=0;
        $distotal = 0;
        foreach($key as $value){
            $List_Value=array();
            for($k=0;$k<count($item_array);$k++){
                $List_Value[]=$value[$item_array[$k]];
            }
            $res = DB_QUERY("SELECT price FROM $GLOBALS[DB_SUPPLY_ITEM] WHERE pserial='".$value["pserial"]."'");
            $rowPrice=mysql_fetch_array($res);
            $res = DB_QUERY("SELECT * FROM $GLOBALS[DB_PRO2UNIT] WHERE pserial='".$value["pserial"]."' and unit='".$value["unit"]."'");
            $rowUnit=mysql_fetch_array($res);
            $rowNator =(mysql_num_rows($res))? ($rowUnit["denominator"]/$rowUnit["numerator"]):1;
            $subprice = ($rowNator*$value["num"])*$rowPrice["price"];
            $subdistol = ($rowNator*$value["num"])*$rowPrice["price"]*(1-($row["discount"]/100));
            //新增採購名細
            DB_INSERT($GLOBALS["DB_BUYITEM"],$Item_Field,array_merge(Array($buy_serial),$List_Value,Array($rowNator*$rowPrice["price"],$rowPrice["price"],$row["discount"],$subprice-$subdistol)));
            UpPseBuynum($value["pserial"]);
            CreaAppSafe($value["pserial"]);
            UpdateApplyNum($value["pserial"],$value["apply_serial"]);
            $price+=$subprice;//小計
            $distotal += $subdistol;//折扣金額
        }
        $price = NumFormat_del($price);
        $distotal = NumFormat_del($distotal);
        $total=$price-$distotal;//本單總計金額
        $nt_subtotal=NumFormat_del($price*$rate);//基本幣別小計
        $nt_distotal=NumFormat_del($distotal*$rate);//折扣
        $nt_total=$nt_subtotal-$nt_distotal;//台幣總計
        //更新採購主檔小計資料
        DB_QUERY("UPDATE $GLOBALS[DB_BUYLIST] SET subtotal=".NumFormat_del($price).",distotal=".NumFormat_del($distotal).",total=".NumFormat_del($total).",nt_subtotal=".NumFormat_del($nt_subtotal).",nt_distotal=".NumFormat_del($nt_distotal).",nt_total=".NumFormat_del($nt_total)." WHERE serial='$buy_serial'");
        //更新paper
        DB_QUERY("UPDATE $GLOBALS[DB_PAPER] SET last_serial=last_serial+1,last_date='".$date."' WHERE paper='buy_list'");
    }
}

##更新待到貨數量
function UpdateBuyNum($pserial,$buy_serial){
    $res = DB_QUERY("SELECT sum(num) as not_num FROM $GLOBALS[DB_STOCKITEM] AS A 
                        LEFT OUTER JOIN $GLOBALS[DB_STOCKLIST] AS B ON B.serial=A.uid 
                        WHERE  A.pserial='".$pserial."' and A.buy_serial='".$buy_serial."' and B.company='".Company()."' GROUP BY CONCAT(A.pserial,A.buy_serial)");
    $notNum=mysql_fetch_array($res);
    DB_QUERY("UPDATE $GLOBALS[DB_BUYDETAIL] SET stock_num=".($notNum["not_num"]?$notNum["not_num"]:0)." WHERE pserial='".$pserial."' and buy_serial='".$buy_serial."' AND company='".Company()."'");
    DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=(SELECT count(id) FROM $GLOBALS[DB_BUYDETAIL] WHERE company='".Company()."' AND (num-stock_num)>0) WHERE paper='buy_detail' ");
    
    
}

#組合商品提示訊息
function CreaMakeSafe($pserial,$fld="",$val="",$dis="+"){
    $bok = 1;
    if($fld){
        $RES=DB_QUERY("SELECT * FROM $GLOBALS[DB_PROMASAFE] WHERE pserial='".$pserial."' and company='".Company()."'");
        if(!mysql_num_rows($RES)) DB_INSERT($GLOBALS["DB_PROMASAFE"],Array("pserial",$fld,"company"),Array($pserial,$val,Company()));
        else{
            $value = $fld.$dis.$val;
            if($fld=="safe_num") $value = $val;
            DB_QUERY("UPDATE $GLOBALS[DB_PROMASAFE] SET ".$fld."=".$value." WHERE pserial='".$pserial."' and company='".Company()."'");
        }
    }
    if($bok){
        $RES = DB_QUERY("SELECT A.*,B.title,B.specif,B.unit,B.barcode,B.stop FROM $GLOBALS[DB_PROMASAFE] AS A 
	                           LEFT OUTER JOIN $GLOBALS[DB_PRODUCT] as B ON B.serial=A.pserial WHERE A.pserial='".$pserial."' and A.company='".Company()."'");
        $ROW = mysql_fetch_array($RES);
        $bok2 = 1;
        $bok3 = 0;
        if($ROW["stop"]) $bok2=0;
        $appres = DB_QUERY("SELECT * FROM $GLOBALS[DB_AUTOMESG] WHERE pserial='".$pserial."' and company='".Company()."'");
        $Now_num = $ROW["store_num"]+$ROW["buy_num"]+$ROW["stock_num"];
        if($bok2){
            if( intval($Now_num) < intval($ROW["safe_num"]) ){//若低於安全存量才執行
                $NewNum = $ROW["safe_num"]-$Now_num;
                $Field = array("pserial","barcode","title","specif","unit","num","company","base_unit","numerator","denominator");
                $Value = array($pserial,$ROW["barcode"],$ROW["title"],$ROW["specif"],$ROW["unit"],$NewNum,Company(),$ROW["unit"],1,1);
                if(mysql_num_rows($appres)) DB_UPDATE($GLOBALS["DB_AUTOMESG"],$Field,$Value,"pserial='".$pserial."' and company='".Company()."'"); 
                else{
                    $Field = array_merge($Field,array("date"));
                    $Value = array_merge($Value,array(date("Y-m-d")));
                    DB_INSERT($GLOBALS["DB_AUTOMESG"],$Field,$Value);
                    AddWait("automakemag_list");
                }
            }else $bok3 = 1;
        }else $bok3 = 1;
 
        if($bok3){
            if(mysql_num_rows($appres)){
                DB_DELETE($GLOBALS["DB_AUTOMESG"],"pserial='".$pserial."' and company='".Company()."'");
                DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=wait-1 WHERE paper='automakemag_list'");
            }
        }
    }
}
//統計指定組合產品的庫存量
function UpPsMaStonum($pserial){
    DB_QUERY("UPDATE $GLOBALS[DB_PROMASAFE] SET store_num=(SELECT sum(total_num) FROM $GLOBALS[DB_PRO2FAM] WHERE pserial='".$pserial."' and beta GROUP BY pserial) WHERE pserial='".$pserial."' AND company='".Company()."'");
}

#一般商品自動請購
/*
$pserial -> 產品編號
$fld -> 欄位
$val -> 值
*/
function CreaAppSafe($pserial,$fld="",$val="",$dis="+"){
    $bok = 1;
    if($fld){
        $RES=DB_QUERY("SELECT * FROM $GLOBALS[DB_PROAPSAFE] WHERE pserial='".$pserial."' and company='".Company()."'");
        if(!mysql_num_rows($RES)) DB_INSERT($GLOBALS["DB_PROAPSAFE"],Array("pserial",$fld,"company"),Array($pserial,$val,Company()));
        else{
            $value = $fld.$dis.$val;
            if($fld=="safe_num") $value = $val;
            DB_QUERY("UPDATE $GLOBALS[DB_PROAPSAFE] SET ".$fld."=".$value." WHERE pserial='".$pserial."' and company='".Company()."'");
        }
    }
    if($bok){
        $RES = DB_QUERY("SELECT A.*,B.title,B.specif,B.unit,B.barcode,B.stop FROM $GLOBALS[DB_PROAPSAFE] AS A 
	                           LEFT OUTER JOIN $GLOBALS[DB_PRODUCT] as B ON B.serial=A.pserial 
                               WHERE A.pserial='".$pserial."' and A.company='".Company()."'");
        $ROW = mysql_fetch_array($RES);
        $bok2 = 1;
        $bok3 = 0;
        if($ROW["stop"]) $bok2=0;
        $appres = DB_QUERY("SELECT * FROM $GLOBALS[DB_AUTOAPPLIST] WHERE pserial='".$pserial."' and company='".Company()."'");
        $Now_num = $ROW["store_num"]+$ROW["buy_num"]+$ROW["stock_num"];
        if($bok2){
            if(intval($Now_num) < intval($ROW["safe_num"]) ){//若低於安全存量才執行
                $NewNum = $ROW["safe_num"]-$Now_num;
                $Field = array("pserial","barcode","title","specif","unit","num","company","base_unit","numerator","denominator");
                $Value = array($pserial,$ROW["barcode"],$ROW["title"],$ROW["specif"],$ROW["unit"],$NewNum,Company(),$ROW["unit"],1,1);
                if(mysql_num_rows($appres)) DB_UPDATE($GLOBALS["DB_AUTOAPPLIST"],$Field,$Value,"pserial='".$pserial."' and company='".Company()."'"); 
                else{
                    $Field = array_merge($Field,array("date"));
                    $Value = array_merge($Value,array(date("Y-m-d")));
                    DB_INSERT($GLOBALS["DB_AUTOAPPLIST"],$Field,$Value);
                    AddWait("autoapply_list");
                }
            }else $bok3 = 1;
        }else $bok3 = 1;
 
        if($bok3){
            if(mysql_num_rows($appres)){
                DB_DELETE($GLOBALS["DB_AUTOAPPLIST"],"pserial='".$pserial."' and company='".Company()."'");
                DB_QUERY("UPDATE $GLOBALS[DB_WAIT] SET wait=wait-1 WHERE paper='autoapply_list'");
            }
        }
        
    }
}
//統計指定一般產品的庫存量
function UpPserialStonum($pserial){

    DB_QUERY("UPDATE $GLOBALS[DB_PROAPSAFE] SET store_num=(SELECT sum(total_num) FROM $GLOBALS[DB_PRO2FAM] WHERE pserial='".$pserial."' and beta GROUP BY pserial) WHERE pserial='".$pserial."' AND company='".Company()."'");
}
//統計組成採購單未完成數量
function UpPseBuynum($pserial){
    DB_QUERY("UPDATE $GLOBALS[DB_PROAPSAFE] SET buy_num=(SELECT sum(if(base_unit=unit,num,num*denominator) ) as tol FROM $GLOBALS[DB_BUYITEM] as A 
                                                        	LEFT OUTER JOIN $GLOBALS[DB_BUYLIST] as B ON B.serial=A.uid 
                                                        	WHERE !B.type AND A.pserial= '".$pserial."' AND B.company='".Company()."' Group by A.pserial) 
                                                            WHERE pserial='".$pserial."' AND company='".Company()."'");
}
//統計預定到貨數
function UpPseStonum($pserial){
    DB_QUERY("UPDATE $GLOBALS[DB_PROAPSAFE] SET stock_num=IFNULL((SELECT sum(if(base_unit=unit,if((num-stock_num)>=0,(num-stock_num),0),if((num-stock_num)>=0,(num-stock_num),0)*denominator) ) as tol FROM $GLOBALS[DB_BUYDETAIL] WHERE pserial= '".$pserial."' AND company='".Company()."'  Group by pserial),0)+
                                                            IFNULL((SELECT sum(if(base_unit=unit,num,num*denominator) ) as tol FROM $GLOBALS[DB_STOCKITEM] as A 
                                                        		LEFT OUTER JOIN $GLOBALS[DB_STOCKLIST] as B ON B.serial=A.uid 
                                                        		WHERE !B.type AND A.pserial= '".$pserial."' AND B.company='".Company()."' Group by A.pserial),0) 
                                                            WHERE pserial='".$pserial."' AND company='".Company()."'");
}

//更新產品的平均進貨單價
function UpProAverage($pserial,$price,$tol){
    $tol = intval($tol);
    $A = $price* intval($tol);
    #總庫存量
    $RES = DB_QUERY("SELECT store_num FROM $GLOBALS[DB_PROAPSAFE] WHERE pserial='".$pserial."'");
    $storenum = mysql_fetch_array($RES);
    #上次平均單價
    $RES = DB_QUERY("SELECT avgbuyprice FROM $GLOBALS[DB_PRODUCT] WHERE serial='".$pserial."'");
    $backprice = mysql_fetch_array($RES);
    $B = intval($storenum["store_num"])*intval($backprice["avgbuyprice"]);
    #最新的庫存成本價
    $C = intval($A)+intval($B);
    $D = $storenum["store_num"]+intval($tol);
    $E = ceil($C/$D);
    DB_UPDATE($GLOBALS["DB_PRODUCT"],Array("newbuyprice","avgbuyprice"),Array($price,$E),"serial='".$pserial."'");
    DB_UPDATE($GLOBALS["DB_PRODETAIL"],Array("base_price"),Array($E),"pserial='".$pserial."'");
    SumAverage($pserial);
}
function SumAverage($pserial){
    $RES = DB_QUERY("SELECT uid FROM $GLOBALS[DB_PRODETAIL] WHERE pserial='".$pserial."'");
    while($ROW = mysql_fetch_array($RES)){
        DB_QUERY("UPDATE $GLOBALS[DB_PRODUCT] SET base_price = (SELECT sum(base_price*num) FROM $GLOBALS[DB_PRODETAIL] WHERE uid='".$ROW["uid"]."' GROUP BY uid)");
    }
}
#此金額是否有超過信用額度
function Credityes($Cus,$PayMonth,$Total){
    $bok = 0;
    $date = explode("-",$PayMonth);
    $res = DB_QUERY("SELECT A.id FROM $GLOBALS[DB_CUSTOMER] AS A 
                    LEFT OUTER JOIN $GLOBALS[DB_RECEIVTAB] AS B ON B.cid=A.serial AND B.year='".$date[0]."' AND month='".$date[1]."'
                    Where IFNULL(B.credit,A.credit) < $Total AND A.serial='".$Cus."' AND A.credit!=0");
    if(mysql_num_rows($res)) $bok = 1;
    return $bok ;
}

#此單據的倉庫是否於盤點中
function chckStockyes($DBLLIST,$DBITEM,$ID,$SIDFLD="sto_id",$FIDFLD="fid"){
    $ThisDay = date("Y-m-d");
    $RES = DB_QUERY("SELECT A.".$SIDFLD.",B.".$FIDFLD." FROM $DBLLIST as A
                        LEFT OUTER JOIN $DBITEM as B ON B.uid = A.serial
                        WHERE A.serial='".$ID."' GROUP BY CONCAT(A.".$SIDFLD.",B.".$FIDFLD.")");
    $bok = 0;
    while($ROW = mysql_fetch_array($RES)){
        $RES = DB_QUERY("SELECT check_serial FROM $GLOBALS[DB_CHECKPRO] WHERE (date_start<='".$ThisDay."' AND date_end>='".$ThisDay."') AND sto_id='".$ROW[$SIDFLD]."' AND fid='".$ROW[$FIDFLD]."'");
        if(mysql_num_rows($RES)>0){
            $bok = 1;
            break;
        } 
    }
    return $bok;
}

#----------------2009.05.13-----------------------------
#說明：更新出貨倉庫存數
#輸入：產品編號　/ 數量　/ 加或減
#-------------------------------------------------------
function UpStorageSale($pserial,$num,$dis="+"){
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_PRO2FAM] WHERE pserial='".$pserial."' and sid='".$GLOBALS["STORAGE_SALE"]."' and fid='".$GLOBALS["STORAGE_SALEFRAME"]."' and beta=1");
    if(mysql_num_rows($RES)) DB_QUERY("UPDATE $GLOBALS[DB_PRO2FAM] SET total_num = total_num".$dis.$num." WHERE sid='".$GLOBALS["STORAGE_SALE"]."' and fid='".$GLOBALS["STORAGE_SALEFRAME"]."' AND pserial='".$pserial."' and beta=1");
    else{
        $res = DB_QUERY("SELECT * FROM $GLOBALS[DB_PRODUCT] WHERE serial='".$pserial."'");
        $row = mysql_fetch_array($res);
        $FLD = array("barcode","pserial","title","specif","unit","sid","fid","first_num","total_num","beta","make");
        $VAL = array($row["barcode"],$row["serial"],$row["title"],$row["specif"],$row["unit"],$GLOBALS["STORAGE_SALE"],$GLOBALS["STORAGE_SALEFRAME"],$num,$num,1,$row["make"]);
        DB_INSERT($GLOBALS["DB_PRO2FAM"],$FLD,$VAL);
    }
}


#提示盤點中的訊息視窗
function CheckAlert($check_paper,$pageTitle){
    $msg = "";
    if(count($check_paper)>0){
        $msg = "\\n注意 : 以下".$pageTitle."單號倉庫目前於盤點中 , 不得異動指定倉庫的庫存數 \\n\\n";
        for($k=0;$k<count($check_paper);$k++){
            $msg .=$check_paper[$k]."\\n";
        }
        $msg .="\\n以上單號無法".$pageTitle."完成 !!";
    }
    return $msg;
}

#提示庫存不足
function StoreAlert($ErrorPaper,$pageTitle){
    $msg = "";
    if(count($ErrorPaper)>0){
        $msg = "\\n注意 : 以下".$pageTitle."單號庫存數不足 \\n\\n";
        for($k=0;$k<count($ErrorPaper);$k++){
            $msg .=$ErrorPaper[$k]."\\n";
        }
        $msg .="\\n以上單號無法".$pageTitle."完成 !!";
    }
    return $msg;
}

#提示信用額度不足
function CreditAlert($ErrorPaper,$pageTitle){
    $msg = "";
    if(count($ErrorPaper)>0){
        $msg = "\\n注意 : 以下".$pageTitle."單號信用額度不足 \\n\\n";
        for($k=0;$k<count($ErrorPaper);$k++){
            $msg .=$ErrorPaper[$k]."\\n";
        }
        $msg .="\\n以上單號無法".$pageTitle."完成 !!";
    }
    return $msg;
}

#刪除目錄
function DelFiles($Path){
  	if(is_dir($Path) || is_file($Path)){
		if(!is_dir($Path)){
			unlink($Path);
			return $Path;
		}
		$FileList=opendir($Path);
		$Str = array();
		while($Files=readdir($FileList)){
			if($Files!=".." && $Files!="."){
         DelFDir("$Path/$Files");
      }
		}
		closedir ($FileList);
		rmdir($Path);
	}
}

#----------------2009.04.24-----------------------------
#說明：加入會員通知信
#輸入：會員信箱,會員帳號,會員密碼,會員/公司名稱
#輸出：頁數列
#-------------------------------------------------------
function AddmemberNotice($mail,$loginid,$loginipw,$title)
{
    $res=DB_QUERY("select * from ".$GLOBALS["DB_COMP"]." where company='yes3c'");
    $row=mysql_fetch_assoc($res);
    $text = '<div style="font-size:12px;">'.$title.' 您好!! <br><br><p>歡迎您加入 '.$row['comp_name_tw'].' 的大家庭</p>
            <p>您的帳號：'.$loginid.'</p>
            <p>您的密碼：'.$loginipw.'</p>
            有任何問題亦歡迎與我們連絡<br />
            我們將有專人為您服務<br />
            服務專線：'.$row['phone'].' 傳真：'.$row['fax'].'<br />
            本站網址：<a href="'.myUrl(1).'" target="_blank">'.myUrl(1).'</a><br>
            地址:'.$row['comp_addr_tw'].'<br>
            '.$row['comp_name_tw'].' 敬上</div>';
    $text2='管理者您好!!<br><br>
            您有新會員加入!!<br><br>
            會員名稱為:'.$title;
    
    $cksend=SendMail2($mail,$row["comp_name_tw"]." - 會員加入通知信",$text,$row['email'],$row["comp_name_tw"]);
    SendMail2($row['email'],$row["comp_name_tw"]." - 新會員加入通知信",$text2,$row['email'],$row["comp_name_tw"]);
    return $cksend;
}

#----------------2009.04.24-----------------------------
#說明：忘記密碼通知信
#輸入：會員信箱,會員密碼,會員/公司名稱
#輸出：頁數列
#-------------------------------------------------------
function ForgetpwNotice($mail,$loginipw,$title)
{
    $res=DB_QUERY("select * from ".$GLOBALS["DB_COMP"]." where company='yes3c'");
    $row=mysql_fetch_assoc($res);
    $text = '<div style="font-size:12px;">'.$title.' 您好!! <br><br>
            <p>您的密碼：'.$loginipw.'</p>
            有任何問題亦歡迎與我們連絡<br />
            我們將有專人為您服務<br />
            服務專線：'.$row['phone'].' 傳真：'.$row['fax'].'<br />
            本站網址：<a href="'.myUrl(1).'" target="_blank">'.myUrl(1).'</a><br>
            地址:'.$row['comp_addr_tw'].'<br>
            '.$row['comp_name_tw'].' 敬上</div>';
    $cksend=SendMail2($mail,$row["comp_name_tw"]." - 忘記密碼通知信",$text,$row['email'],$row["comp_name_tw"]);
    return $cksend;
}

#----------------2009.05.10-----------------------------
#說明：計算運費
#輸入：包裝等級,特定運費,會員等級,總額(折扣後小計),貨運名稱
#輸出：運費
#-------------------------------------------------------
function Freight($pack,$specia,$level,$total,$carry)
{
    $res=DB_QUERY("select * from ".$GLOBALS["DB_FREIGHT"]." where title='$carry'");
    $row=mysql_fetch_assoc($res);
    $fre=array();
    $pay=0;
    $ckstores=strpos("t".$carry,'超商');    
    if($ckstores>0){//超商取貨
        if($total<=2000) $pay=$row['class1'];
        else $pay=$row['class2'];
    }else{
        if($specia>0){
            $pack=$pack-$specia;
            if($pack>0) $pack +=$specia;
            else $pack=$specia;
        }
        $ckcarry=strpos("t".$carry,'離島');
        //if($ckcarry>=0 && $total>=15000 && $level!='001') $pack=$pack-3;//優惠運費
        if($ckcarry=='' && $total>=15000 && $level=='001') $pack=$pack-3;//優惠運費 
        if($pack>4)
        {
            do{
                $fre[]=4;
                $pack -=4;        
            }while($pack>4);
        }
        $fre[]=$pack;
        $m=count($fre);
        for($i=0;$i<$m;$i++)
        {
            $num=ceil($fre[$i]);
            $pay +=$row["class$num"];
        }
    }
    
    return $pay;
}

#----------------2009.05.11-----------------------------
#說明：訂單table
#輸入：table樣試
#輸出：產品資料table
#-------------------------------------------------------
function OrderTable($type,$data='')
{
  $str='';
  switch ($type)
  {
    case 'table':
      $str='<table  width="80%" border="0" cellspadding="1" cellspacing="1" style="background-color:#ccc;border:#333 solid 1px;font-size:12px;">';
    break;
    case 'head'://表頭資料
      $str='
      <table  width="80%" border="0" cellspadding="1" cellspacing="1" style="background-color:#ccc;border:#333 solid 1px;font-size:12px;">
        <tr style="background-color:#0547C2;" align="center">
          <td >'.$data[0].'</td>
          <td width="10%">'.$data[1].'</td>
          <td width="20%">'.$data[2].'</td>
          <td width="25%">'.$data[3].'</td>
        </tr>
          ';
    break;
    case 'cart'://產品資料
       $str='
        <tr style="background-color:#fff;">
          <td align="center">'.$data[0].'</td>
          <td align="center">'.$data[1].'</td>
          <td align="center">'.$data[2].'</td>
          <td align="right">'.$data[3].'</td>
        </tr>'; 
    break;
    case 'amount'://合計資料
       $str='<tr style="background-color:#fff;">
              <td></td>
              <td></td>
              <td align="center">'.$data[0].'</td>
              <td align="right">'.$data[1].'</td>
            </tr>';
    break;
    case 'cut'://合計資料(減)
       $str='<tr style="background-color:#fff;">
              <td></td>
              <td></td>
              <td align="center">'.$data[0].'</td>
              <td align="right" style="color:#f00;">-'.$data[1].'</td>
            </tr>';
    break;
    case 'foot'://總額資料
      $str='<tr style="background-color:#fff;">
        <td></td>
        <td></td>
        <td align="center">'.$data[0].'</td>
        <td align="right" style="background-color:#E9F2F9;">'.$data[1].'</td>
      </tr>      
      </table>';
    break;
    default:
  }
  return $str;
}
#----------------2009.06.09-----------------------------
#說明：通知客戶出貨
function NoticeCusSale($serial){
    $RES = DB_QUERY("SELECT A.*,B.email FROM $GLOBALS[DB_SALELIST] AS A  
                            LEFT OUTER JOIN $GLOBALS[DB_CUSTOMER] as B ON B.serial=A.cus_serial WHERE A.serial='".$serial."'");
    $ROW = mysql_fetch_array($RES);
    if($ROW["cus_serial"]=='N002' || $ROW["cus_serial"]=='N003'){
        $Name = $ROW["send_contact"];
        $Mail = $ROW["send_mail"];
    }
    else{
        $Name = $ROW["cus_title"];
        $Mail = $ROW["email"];
    }
    if($Mail){
        $Str = "<div style=\"font-size:12px;\">
        親愛的 ".$Name." 您好!!<br />
        感謝您對本公司的支持~<br />
        本公司產品出貨通知如下：<br /><br />
        <table  width=\"80%\" border=\"0\" cellspadding=\"1\" cellspacing=\"1\" style=\"background-color:#ccc;border:#333 solid 1px;font-size:12px;\">
            <tr style=\"background-color:#0547C2;color:#FFFFFF\" align=\"center\">
                <td colspan=\"3\">YES'3C 出 貨 通 知</td>
            </tr>
            <tr style=\"background-color:#BBD9F1;color:#274E91\" align=\"center\">
                <td>產品名稱</td>
                <td>數量</td>
                <td>單位</td>
            </tr>";
        $res = DB_QUERY("SELECT * FROM $GLOBALS[DB_SALEITEM] WHERE uid='".$serial."'");
        while($row = mysql_fetch_array($res)){
            $Str .="<tr style=\"background-color:#E9F2F9;color:#333333\" align=\"center\">
                    <td>".$row["title"]."</td>
                    <td>".$row["num"]."</td>
                    <td>".$row["unit"]."</td>
                    </tr>
                    ";
        }
        $res=DB_QUERY("select * from ".$GLOBALS["DB_COMP"]." where company='".Company()."'");
        $row=mysql_fetch_assoc($res);
        $Str .="</table><br /><br />以上產品已出貨<br /><br />有任何問題亦歡迎與我們連絡<br />
        我們將有專人為您服務<br />
        服務專線：".$row['phone']." 傳真：".$row['fax']."<br />
        網址：<a href=\"".myUrl(1)."\" target=\"_blank\">".myUrl(1)."</a><br />
        地址：".$row['comp_addr_tw']."<br />
        ".$row['comp_name_tw']."  敬上";
        SendMail($Mail,$row["comp_name_tw"]." - 出貨通知信",$Str,$row['email'],$row["comp_name_tw"]);
    }
    
}

#----------------2009.05.11-----------------------------
#說明：訂單通知信
#輸入：會員mail,會員名稱,訂單資料array(訂單號碼,付款方式,貨運方式),
#      訂購人資料array(姓名,聯絡電話,行動電話,地址),
#      收件人資料array(姓名,聯絡電話,行動電話,地址),
#      產品資料
#輸出：發出通知信(會員與管理者)
#-------------------------------------------------------
function OrderNotice($mail,$member,$ohead,$buy,$send,$table){
    $res=DB_QUERY("select * from ".$GLOBALS["DB_COMP"]." where company='".Company()."'");
    $row=mysql_fetch_assoc($res);
    
    $text='<div style="font-size:12px;">
    親愛的 '.$member.' 您好!!<br /><br />
    感謝您對本公司的支持~<br />
    您的訂單資料如下：<br /><br />
    
    訂單資料<br />
    &nbsp;&nbsp;訂單編號：'.$ohead[0].'<br />
    '.(($ohead[3]=='')?'':'&nbsp;&nbsp;虛擬帳號：'.$ohead[3].'<br />').'
    &nbsp;&nbsp;付款方式：'.$ohead[1].'<br />
    &nbsp;&nbsp;貨運方式：'.$ohead[2].'<br /><br />
    
    訂購人資料<br />
    &nbsp;&nbsp;姓名：'.$buy[0].'<br />
    &nbsp;&nbsp;聯絡電話：'.$buy[1].'<br />
    &nbsp;&nbsp;行動電話：'.$buy[2].'<br />
    &nbsp;&nbsp;地址：'.$buy[3].'<br /><br />
    
    收件人資料<br />
    &nbsp;&nbsp;姓名：'.$send[0].'<br />
    &nbsp;&nbsp;聯絡電話：'.$send[1].'<br />
    &nbsp;&nbsp;行動電話：'.$send[2].'<br />
    &nbsp;&nbsp;地址：'.$send[3].'<br /><br />
        訂單明細<br />
    '.$table.'<br />
    <p></p>


    請於付款後通知本公司<br />
    <br />
    本公司將儘速為您處理<br />
    訂單進度查詢：<br />
    <a href="'.myUrl(0).'/order_list.php" target="_blank">'.myUrl(0).'/inquiry.php</a><p></p>
    
    有任何問題亦歡迎與我們連絡<br />
    我們將有專人為您服務<br />
    服務專線：'.$row['phone'].' 傳真：'.$row['fax'].'<br />
    網址：<a href="'.myUrl(1).'" target="_blank">'.myUrl(1).'</a><br />
    地址：'.$row['comp_addr_tw'].'<br />
    '.$row['comp_name_tw'].'  敬上</div>';
    
    $other='管理者您好!!<br><br>
    &nbsp;&nbsp;新訂單資料如下：';
    
    $cksend=SendMail2($mail,$row["comp_name_tw"]." - 訂單通知信",$text,$row['email'],$row["comp_name_tw"]);
    SendMail2($row['email'],$row["comp_name_tw"]." - 新訂單通知信",$other.$text,$row['email'],$row["comp_name_tw"]);
    
    return $cksend;
    //SendMail2($mail,$row["comp_name_tw"]." - 忘記密碼通知信",$text,$row['email'],$row["comp_name_tw"]);
}

#----------------2009.05.11-----------------------------
#說明：匯款通知信
#輸入：會員信箱,訂單資料array(訂單編號,付款日期,付款方式,付款帳號後為5碼,訂購帳號,訂購人)
#輸出：發出通知信(會員與管理者)
#-------------------------------------------------------
function RemitNotice($mail,$order='')
{
    $res=DB_QUERY("select * from ".$GLOBALS["DB_COMP"]." where company='yes3c'");
    $row=mysql_fetch_assoc($res);
    $text='<table border="0" width="100%" cellspacing="0" cellpadding="0" >
              <tr>
                <td align="left">
                  <div style="font-size:12px;">
                    <span style="font-size:14px;color:#039;">匯款資料如下：</span><br /><br />
                    訂單編號：'.$order[0].' <br /><br />
                    付款日期：'.$order[1].' <br /><br />
                    付款方式：'.$order[2].' <br /><br />
                    付款帳號後5碼：'.$order[3].' <br /><br />
                    訂購帳號：'.$order[4].' <br /><br />
                    訂購人：'.$order[5].' <br />
                  </div>
                </td>
              </tr>
         </table>';
    SendMail2($row['email'],$row["comp_name_tw"]." - 新匯款通知信",$text,$row['email'],$row["comp_name_tw"]);
    
    $text='<table border="0" width="100%" cellspacing="0" cellpadding="0" >
              <tr>
                <td align="left">
                  <div style="font-size:12px;">
                    親愛的會員您好：<br />
                    感謝您對本公司的支持~<br /><br />
                    本公司已經收到您的付款資料<br />
                    我們將會把此訂單列為最優先處理事項<br />
                    預計將於1-2個工作天送達商品至您指定的地點　　謝謝<br /><br>
                    有任何問題亦歡迎與我們連絡<br />
                    我們將有專人為您服務<br />
                    服務專線：'.$row['phone'].' 傳真：'.$row['fax'].'<br />
                    網址：'.myUrl(1).'<br>
                    地址：'.$row['comp_addr_tw'].'<br>
                    '.$row["comp_name_tw"].'　　敬上
                  </div>
                </td>
              </tr>
         </table>';
    $cksend=SendMail2($mail,$row["comp_name_tw"]." - 匯款通知信",$text,$row['email'],$row["comp_name_tw"]);
    return $cksend;
}
#取出無限層關聯資料
function ArrayListing($DB, $Uid = 0, $Blank = 0, $Getrow = Array(), $Array = Array()){

    $Array[] = $Getrow;
    $RES = DB_QUERY("SELECT * FROM $DB WHERE uid = '$Uid' ORDER BY sort");
    $Blank++;
    while($ROW = mysql_fetch_assoc($RES)){
        $res = DB_QUERY("SELECT * FROM $DB WHERE uid='".$ROW["id"]."'");
        list($ROW["blank"],$ROW["child"]) = Array($Blank,!$ROW["type"]);
        $Array = ArrayListing($DB, $ROW["id"], $Blank, $ROW, $Array);
    }
    return $Array;
}
#科目類型
function Account($Type){
    $Out = Array();
    for($i=0;$i<count($GLOBALS["ACCOUNT"]);$i++){
        $Out[] = "<input type=\"radio\" name=\"type\" value=\"$i\" onClick=\"ChkType()\" ".($Type==$i?"checked":"").">".$GLOBALS["ACCOUNT"][$i];
    }
    return join(" ",$Out);
}

#傳票類型
function Kind($Kind){
    $Out = "";
    for($i=0;$i<count($GLOBALS["VOUCHER"]);$i++){
        $Out .= "<input type=\"radio\" name=\"kind\" value=\"".$i."\" ".($Kind==$i?"checked":"").">".$GLOBALS["VOUCHER"][$i];
    }
    return $Out;
}


##單據列印表頭公司名稱
function CompName($Paper,$col1){
    #公司基本資料
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_COMP] where company='".Company()."'");
    $Comp = mysql_fetch_array($RES);
    return  "<table width=\"100%\">
                <tr style=\"font-size:15px;font-weight:bold;\">
                    <td align=\"center\" width=\"80%\" colspan='".$col1."'><b>".$Comp["comp_name_tw"]."</b></td>
                </tr>

                <tr>
                    <td align=\"center\" colspan='".($col1)."' >".$Comp["comp_addr_tw"]."</td>
                </tr>
                <tr>
                    <td align=\"center\" colspan='".($col1)."'>Tel : ".$Comp["phone"]."  　Fax : ".$Comp["fax"]."</td>
                </tr>
                <tr style=\"font-size:15px;font-weight:bold;\">
                    <td align=\"center\" valign=\"top\" colspan='".($col1)."'  class=\"style1\" style=\"font-size:25px;text-decoration: underline;\">$Paper</td>
                </tr>
            </table>";
}
#列印按鈕 $export 0 = 不顯示匯出按鈕
function PrintButton($export=1){
    $Button = "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                        <tr>
                          <td align=\"left\" valign=\"top\" height=\"10\"></td>
                        </tr>
                        <tr>
                          <td align=\"center\" valign=\"top\"><table width=\"60%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"noprint\">
                            <tr align=\"center\" valign=\"top\">";
    if($export==1){
        $Button.="            <td style=\"display:none;\">
                                <input type=\"button\" class=\"button_css\" value=\"匯出 Excel_\" onclick=\"document.execCommand('SaveAs', true, 'Royalty Statement.xls');\">
                              </td>
                              <td>
                                <input type=\"button\" class=\"button_css\" value=\"匯出 Excel\" onclick=\"javascript:ExportExcel();\" >
                              </td>
                              <td style=\"display:none;\">
                                <input type=\"button\" class=\"button_css\" value=\"匯出 Word\" onclick=\"document.execCommand('SaveAs', true, 'Royalty Statement.doc');\">
                              </td>
                              <td>
                                <input type=\"button\" class=\"button_css\" value=\"匯出 Word\" onclick=\"javascript:ExportDoc();\">
                              </td>";
    }
    $Button.="                <td>
                                <input type=\"button\" class=\"button_css\" value=\"列　　印\" onClick=\"factory.printing.Print(false);\">
                              </td>
                              <td>
                                <input type=\"button\" class=\"button_css\" value=\"預覽列印\" onClick=\"factory.printing.Preview()\">
                              </td>
                              <td>
                                <input type=\"button\" class=\"button_css\" value=\"設定印表機\" onClick=\"factory.printing.PageSetup()\">
                              </td>
                            </tr>
                          </table></td>
                        </tr>
                      </table>";
    return $Button;
}
#----------------2009.05.26-----------------------------
#說明：匯入產品順便新增庫存到備貨倉
#輸入：產品編號
#-------------------------------------------------------
function InsertStoreNum($pserial){
    $RES = DB_QUERY("SHOW COLUMNS FROM $GLOBALS[DB_PRO2FAM] WHERE Field!='id' AND Field!='sid' AND Field!='fid' AND Field!='first_num' AND Field!='total_num' AND Field!='beta' ");
    while($row=mysql_fetch_array($RES)) {
        if($row["Field"]=="serial"){
            $STORE[] = "pserial";
        }else $STORE[] = $row["Field"];
    }
    
    $res = DB_QUERY("SELECT * FROM $GLOBALS[DB_PRODUCT] WHERE serial='".$pserial."'");
    $row = mysql_fetch_array($res);
    $Fld = array();
    $Val = array();
    for($i=0;$i<count($STORE);$i++){
        $Fld = array_merge($Fld,array($STORE[$i]));
        if($STORE[$i]=="pserial") $Val = array_merge($Val,array($row["serial"]));
        else $Val = array_merge($Val,array($row[$STORE[$i]]));
    }
    $Fld = array_merge($Fld,array("sid","fid","beta"));
    $Val = array_merge($Val,array($GLOBALS["STORAGE_PICK"],"F1",1));
    DB_INSERT($GLOBALS["DB_PRO2FAM"],$Fld,$Val);
}

#----------------2009.06.11-----------------------------
#說明：現行稅率
#輸入：無
#輸出：稅率
#-------------------------------------------------------
function GetTax()
{
    $res=DB_QUERY("select tax from ".$GLOBALS["DB_OTHER"]);
    $row=mysql_fetch_assoc($res);
    if($row['tax']==0) return '0';
    else {
        $tax=($row['tax']/100);
        return $tax;
    }
}

/*權限群組
$DB -> 儲存群組的TABLE
*/
function limitCate(){
    
    $Str = "<span style=\"color:#2B7BC9\"><b>權限群組 :</b></span>
       <select name=\"limitCate\" id=\"limitCate\" class=\"page_main_right_select_02\" >
            <option value=\"\">-- 請選擇 --</option>";
    $RES = DB_QUERY("SELECT * FROM $GLOBALS[DB_LIMITCATE] where company='".Company()."' ORDER BY title");
    while($ROW=mysql_fetch_array($RES)){        

        $Str .= "<option value=\"".$ROW["id"]."\" >".$ROW["title"]."</option>";
    }
    $Str .="</select>
       <input type=\"button\" class=\"button_css\" value=\"套 用\" onClick=\"UselimitCate();\">";
    return $Str;
}
//匯出的字串編碼
function export_strCode($str){
    $new_str = function_exists("mb_convert_encoding")
                              ?mb_convert_encoding( $str, "BIG5", "UTF-8" )
                              :iconv( "utf-8", "big5", $str );
    return $new_str;
}


#----------------2008.12.15-----------------------------
#說明：設定頁數(與FPagebar一起使用)
#輸入：總頁數,目前頁數,列數
#輸出：開始頁，結束頁
#-------------------------------------------------------
function SetPage($show,$page,$num){

    $mypage=array();
	if(strval($show)<=strval($num)){
		$mypage['p']=1;
		$mypage['n']=$show;
	}
	else{
		if(($page-5)<=1){
	   	    $mypage['p']=1;
			$mypage['n']=$num;
		}
		else{
			if($show-($page-5)>=($num-1)){
				$mypage['p']=$page-5;
				$mypage['n']=$page-5+($num-1);
			}
			else{
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
function FPagebar($SQL,$NowPage,$Num,$STR="",$CSS="",$Barnum="10"){
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
    	$FirstLink = '<a href="'.$_SERVER["PHP_SELF"]."?ipage=0".$STR.'" ><span class="page_pn">&nbsp;最上頁</span></a> ';
        $BackLink = '<a href="'.$_SERVER["PHP_SELF"]."?ipage=".(($NowPage!=0)?$NowPage-1:0).$STR.'" ><span class="page_pn">&nbsp;上一頁</span></a> ';
        $NextLink = ' <a href="'.$_SERVER["PHP_SELF"]."?ipage=".(($NowPage!=($Total_page-1))?$NowPage+1:$NowPage).$STR.'" ><span class="page_pn">下一頁&nbsp;</span></a> ';
        $LastLink = '<a href="'.$_SERVER["PHP_SELF"]."?ipage=".($Total_page-1).$STR.'" ><span class="page_pn">最末頁&nbsp;</span></a> ';
        
    	$restr='';
    	for($p=$mypage['p'];$p<=$mypage['n'];$p++)
    	{
    	    $restr.=$restr!=""?"|&nbsp;":"&nbsp;";
    		if($p!=($NowPage+1)) $restr.='<a href="'.$_SERVER["PHP_SELF"].'?ipage='.($p-1).$STR.'"><span class="page_num">'.$p.'</span></a>&nbsp;';
    		else $restr.='<a href="'.$_SERVER["PHP_SELF"].'?ipage='.($p-1).$STR.'"><span class="page_nums">'.$p.'</span></a>&nbsp;';
    	}
    	$PageBar = '<table width="100%" border="0"><tr><td width="18%">'.$PageL.'</td><td align="center">'.$FirstLink."|".$BackLink."|".$restr."|".$NextLink."|".$LastLink.'</td><td width="15%" align="right">'.$PageR.'</td></table>';
    }
	return $PageBar;
}
?>