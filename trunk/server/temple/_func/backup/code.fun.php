<?php
// ****************** 除錯用, 顯示一個 [確定] 之對話框
function umess(){
	//需至少傳入一個以上 250 個以下的參數, 可以傳入 [陣列] 或 [個別的變數或字串] 或 2 者混合 (一併顯示 [變數型態] 和 [長度])
	$m_msg = func_get_args() ;
	$m_alen = count($m_msg) ;
	$m_value = "" ;
	$m_chr13 = "" ;
	for($m_x = 0 ; $m_x < $m_alen ; $m_x++){
		if(gettype($m_msg[$m_x])=="array"){	//傳入值是 [陣列] 時
			foreach($m_msg[$m_x] as $m_val){
				$m_len = strval(strlen($m_val)) ;		// 變數的內容之長度
				$m_type = "(".gettype($m_val).")"	;	// 變數的資料型態
				$m_value = ltrim($m_value.$m_chr13.$m_len.$m_type."__".$m_val) ;
				$m_chr13 = ' \n' ;
			}
		}
		else{	//傳入值是 [字串] 時		
			$m_len = strval(strlen($m_msg[$m_x])) ;		// 變數的內容之長度
			$m_type = "(".gettype($m_msg[$m_x]).")"	;	// 變數的資料型態
			$m_value = ltrim($m_value.$m_chr13.$m_len.$m_type."__".$m_msg[$m_x]) ;
			$m_chr13 = ' \n' ;
		}
	}
	echo '<script>alert("'.$m_value.'");</script>' ; 
	//return $m_value;
}

//瀏覽sql命令之後的結果
function ubrows($lc_sql){
//$lc_sql為mysql_query()所傳回的變數
	$m_field=mysql_num_fields($lc_sql);//求出查詢的sql命令中共有幾個欄位
	$m_table='<table  border="1" align="center" cellspacing="0" ><tr bgcolor="#CCFF66">';
	 for($m_l=0;$m_l<$m_field;$m_l++){
		$m_str=mysql_field_name($lc_sql,$m_l);
			$m_table=$m_table.'<td ><span style="font-size:18px">&nbsp;'.$m_str.'</span></td>';
	}
	$m_table=$m_table.'</tr>';
	$m_i=0;
	while($m_record=mysql_fetch_array($lc_sql)){
		$m_color='#DDFFCC';
		if(bcmod($m_i,2)==0){
			$m_color="FFFFFF";//資料第一筆,就使用白色,第二筆淺綠色,第三筆白色,以此類推
		}
		$m_table=$m_table.'<tr bgcolor="'.$m_color.'">';
		for($m_k=0;$m_k<$m_field;$m_k++){//$m_field為此sql命令的欄位總數
			$m_str=mysql_field_name($lc_sql,$m_k);
			$m_type=mysql_field_type($lc_sql,$m_k);//指定欄位的型態
			$m_len=mysql_field_len($lc_sql,$m_k);//指定欄位的長度
			$m_table=$m_table.'<td ><span style="font-size:13px">&nbsp;'.$m_record[$m_k].'</span></td>';
		}
		$m_table=$m_table."</tr>";
		$m_i=$m_i+1;
	}
	$m_table=$m_table."</table>";
	echo "<script>
			var writeWin = null;
			function writeLeft() { 

				writeWin =window.open('','aWin','top=50%,left=50%,width=700,status=yes,toolbars=yes,scrollbars=yes,menubar=yes,directories=yes,resizable=yes'); 
				var ePen ='".$m_table."';
				writeWin.document.open();
				writeWin.document.write(ePen);
				writeWin.document.close();
				
			}
		</script>";
	//window視窗物件
	//menubar-->功能表列 [例如] [檔案] [編輯] [檢視] [我的最愛] .. 等 這些按鈕,若沒有設定,則預設為隱藏
	//scrollbars-->上下拉式卷軸 若沒有設定則設為只顯示視窗大小的內容
	//resizable為是否允許使用者改變視窗大小,若不設定,預設為不允許
	echo "<script>writeLeft();</script>";
	
}
// ****************** 將網址轉到 [指定] 的網頁
function uset_url($ln_type){
	// $ln_type 	轉換網頁的方式
	// $[lc_msg]	轉換網頁前要顯示的訊息, 如果傳入"", 則不會顯示 [對話框]
	// $[lc_url]	要轉換的目標 [網頁] , 可含或不含網址 (ln_type = 2 時,本參數可傳入空白, 其他方式則要傳入), 不傳入則預設網址為 ""
	// $[lc_time]	如果 ln_type = 1, 則本參數為 [暫停的秒數] (ln_type = 2,3,4 時,本參數可傳入空白, 其他方式則要傳入), 不傳入則預設為 3 秒
	$m_args = func_get_args() ;
	$m_alen = count($m_args) ;
	$lc_url = "" ;
	$lc_time = 3 ;

	if($m_alen>=2){	// 有傳入第2個參數時
		if(!empty($m_args[1])){
			echo "<script> alert('".$m_args[1]."'); </script>" ;
			// 另一種方式, 但是本方式須於程式開頭先下 ob_start(), 結尾下 ob_end_flush()
//			Header("refresh:3;URL=http://www.aiguli.com/test/tenco/insert.php") ;
		}
	}
	if($m_alen>=3){	// 有傳入第3個參數時
		$lc_url = $m_args[2] ;
	}

	if($m_alen>=4){	// 有傳入第4個參數時
		$lc_time = $m_args[3] ;
	}	

	switch($ln_type){
		case 1 : 
			if($m_alen < 4){
				umess("第1種模式, 需傳入第3參數(目標網址) 和 第4參數(延遲時間)") ;
			}
			else{	//在網頁上顯示訊息後, 於指定的 [時間] 後返回上一頁 (利用 HTML 語法)
				echo $lc_msg.$lc_time."秒後會自動返回" ;
				echo "<meta http-equiv='refresh' content='".$lc_time.";url=".$lc_url."'>" ;
			}
			break ;
		case 2 :
			if($m_alen >= 3){
				umess("第2種模式, 不需傳入第3個參數(目標網址)") ;
			}
			else{	//顯示對話框, 並會自動 [返回] 到上一頁 (利用 JaveScript 語法)
				echo '<Script> 
						history.go(-1);
					  </Script>' ;
			}
			break ;
		case 3 :
			if($m_alen < 3){
				umess("第3種模式, 需傳入第3個參數(目標網址)") ;
			}
			else{	//顯示對話框, 並會轉到 [指定] 的網頁 (利用 JaveScript 語法)
				echo "<script>
						window.location.href='".$lc_url."';
					  </script>" ;
			}
			break ;
		case 4 :
			if($m_alen < 3){
				umess("第4種模式, 需傳入第3個參數(目標網址)") ;
			}
			else{
				// 使用本方法 (location.replace(URL)) 應該可將URL取代原本在瀏覽器執行的網頁，而無法使用「上一頁」的按鈕回到上一頁。
				echo '<Script> 
						window.location.replace("'.$lc_url.'") ;
					  </Script>' ;
			}
			break ;
		case 5 :
			if($m_alen < 3){
				umess("第5種模式, 需傳入第3個參數(目標網址)") ;
			}
			else{
			// 使用本方式須於程式開頭先下ob_start(),結尾下ob_end_flush(),但是下了 ob_start(),則在 Header()同一區塊中的umess() 將會失效
				Header("Location: $lc_url");
			}
			break ;
	}
}
?>
