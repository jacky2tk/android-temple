<?php
// ****************** 將網頁上的所有 o_ 開頭的物件之 [名稱] 和 [內容值] 存入到 _SESSION[] 中
function usession($la_post_vars){
	// 固定傳入 $HTTP_POST_VARS
	// 傳回 所建立的所有 _SESSION 之變數名稱之陣列
	foreach($la_post_vars as $m_key=>$m_value){
		$m_chr = substr(strtolower(trim($m_key)),0,2) ;
		if($m_chr=="o_"){
			$m_session_name = 's_'.$m_key;
			session_register("$m_session_name") ;
			$_SESSION["$m_session_name"] = $m_value ;
		}
	}
}



// 將 [網頁] 中的所有物件之值寫入到 session 陣列中(可指定session陣列名稱)
// 注意 : 如果網頁中有 [群組物件] (例:checkbox 、radio 等物件) 時, 
//		  1. 其 name 屬性需是 [陣列] (亦即在 名稱後面加上 [] )
//		  2. 其 id 需和 name 屬性之值相同, 然後再加上 [流水編號] 	
//        3. value 屬性值則需和 id 屬性的 [流水編號] 相同, 資料型態可為 [數值(建議)] 或 [字串] (該值是要存到 [資料庫] 中的值)
function uget_objvalue($la_post_vars, $m_session_name='allobj'){
	// $la_post_vars = 請固定傳入 $HTTP_POST_VARS
	// $m_session_name = SESSION 的變數名稱 (不傳入則預設為 allobj)

	unset($_SESSION["$m_session_name"]) ; // 需先釋放原來的session變數, 否則有時會和原來的值混合了, 而產生不可預測的錯誤
	$m_num = 0 ;
	foreach($la_post_vars as $m_key=>$m_value){
		$m_chr = substr(strtolower(trim($m_key)),0,2) ;
		if($m_chr=="o_"){
			if(gettype($m_value)=="array"){
				// 處理群組物件的值 (例:可複選的[核取方塊、下拉式物件] 或 多選一的[點選物件]等)
				foreach($m_value as $m_value2){ 
					$_SESSION["$m_session_name"][$m_num][0] = $m_key.$m_value2 ; // 陣列的第一欄儲存 物件的名稱
					$_SESSION["$m_session_name"][$m_num][1] = $m_value2  ; // 陣列的第二欄儲存 物件的值(不一定是 [邏輯值])
					$m_num = $m_num + 1 ;
				}
			}
			else{
				// 處理單一物件的值
				$_SESSION["$m_session_name"][$m_num][0] = $m_key ;	// 陣列的第一欄儲存 物件的名稱
				$_SESSION["$m_session_name"][$m_num][1] = $m_value ; // 陣列的第二欄儲存 物件的值
				$m_num = $m_num + 1 ;
			}
		}
	}
}


// 將 usession2() 所紀錄在 session 陣列中的值寫入到對應的物件中
function uset_objvalue($m_session_name='allobj'){
	// $m_session_name = SESSION 的變數名稱 (不傳入則預設為 allobj)
	if(!isset($_SESSION["$m_session_name"])){
		umsg("$"."_SESSION['".$m_session_name."'] 的變數尚未建立", "",
			 "執行本函數前, 需先有執行過 usession2( ), 且其第2參數的傳入值需要和本函數一樣") ;
	}
	$m_end = sizeof($_SESSION["$m_session_name"]) ;
	for($m_i=0 ; $m_i<$m_end ; $m_i++){
		$m_name2=$_SESSION["$m_session_name"][$m_i][0] ;
		$m_value2=$_SESSION["$m_session_name"][$m_i][1] ;
		echo "<script> 
				var m_name, m_value, m_obj, m_objtype ;
				m_name = '$m_name2' ;
				m_obj = document.getElementById(m_name) ;
				m_objtype = m_obj.type ;
				if(m_objtype=='text' || m_objtype=='select-one' || m_objtype=='textarea'){
					m_value = '$m_value2' ;
					m_obj.value = m_value ;
				}
				else{
					m_obj.checked = $m_value2 ;
				}</script>" ;
	}
}


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
}



// ****************** 標準用, 顯示一個 [確定] 之對話框 (不會顯示 [變數型態] 和 [長度])
function umsg(){
	//需至少傳入一個以上 250 個以下的參數, 可以傳入 [陣列] 或 [個別的變數或字串] 或 2 者混合
	$m_msg = func_get_args() ;
	$m_alen = count($m_msg) ;
	$m_value = "" ;
	$m_chr13="" ;
	for($m_x = 0 ; $m_x < $m_alen ; $m_x++){
		if(gettype($m_msg[$m_x])=="array"){	//傳入值是 [陣列] 時
			foreach($m_msg[$m_x] as $m_val){
				$m_value = $m_value.$m_chr13.$m_val ;
				$m_chr13 = ' \n' ;
			}
		}	
		else{	//傳入值是 [字串] 時
			$m_value = ltrim($m_value.$m_chr13.$m_msg[$m_x]) ;
			$m_chr13 = ' \n' ;
		}
	}
	echo '<script>alert("'.$m_value.'");</script>' ; 
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


// ****************** 取得 [網址列] 中的字串資料
function uget_url($ln_type){
	// 例如網址列有 : htt://www.qksort.com/tecno/test.html
	// $ln_type 0 = 整個網址傳回 ( 但不含 http:// )
	//			1 = 只取 [主機] 名稱 (例如 : www.qksort.com)
	//			2 = 只取 [主機]後面的字串 (例如 : /tecno/test.html)
	//			3 = 只取最後面的檔案名稱 (例如 : test.html) (檔名前面不會加上 '/')
	switch($ln_type){
		case 0 :
			$m_return = $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"] ;
			break ;
		case 1 :
			$m_return = $_SERVER['HTTP_HOST'] ;
			break ;
		case 2 :
			$m_return = $_SERVER["REQUEST_URI"] ;
			break ;
		case 3 :
			$m_chr = explode("/",$_SERVER["REQUEST_URI"]) ;
			$m_alen = count($m_chr) ;
			if($m_alen>0){
				$m_alen = $m_alen - 1 ;
			}
			$m_return = $m_chr[$m_alen] ;
			break ;
	}
	return $m_return ;
}

//在一個指定的 [來源字串] 中 搜尋某一個指定的 [字元] 位於該 [來源字串] 中的 +1 位置
function found($lc_b1,$lc_b2,$lc_b3,$lc_b4)  {
//b1	原始字串
//b2	字元符號(例:'+','-','*','/')
//b3	第幾次出現的b2
//b4	於 b3 位置找不到 b2 字元之處理   傳入1= 回傳 -1     2=回傳最後一次 b2 字元 +1 的位置  
    $m_bb = $lc_b1;
	$m_aa = 1;  // 記憶 每一次 搜尋道的 b2 字元的位置 並予以累加
		if($lc_b3 <= 0)	{
			if($lc_b4 == 2)	{
				$m_return = 1;
			}
			else	{
				$m_return = 0 ;
			}	
		}
    for($m_cc=1;$m_cc<=$lc_b3;$m_cc=$m_cc + 1) {
			$m_beginning = strpos($m_bb,$lc_b2);
			if($m_beginning == -1) {
				if($lc_b4 == 2)	{
					$m_return = $m_aa;
				}
				else{
     			$m_return3 = -1;
				}
				break;
      		}
	        $m_aa = $m_aa + $m_beginning;
			if($m_cc != 1) {
			  $m_aa = $m_aa+1;
			}
			$m_return = $m_aa; 
			$m_bb = substr($m_bb,$m_beginning+1);
		}
    return $m_return;
}


//****************** 取得亂數值 
function urand($ln_min,$ln_max){
//ln_min 要產生亂數的最小值
//ln_max 要產生亂數的最大值
	srand((double)microtime()*1000000);
	$m_return = rand($ln_min, $ln_max);
	return $m_return;
}



//****************** 取得連線 ID , 大部分是供 uset_var() 使用
function uget_varid($lc_ip){
// 請傳入 $REMOTE_ADDR 之環境變數值 (本值如果在本函數中取得, 則會取到 [空白], 所以才需要在函數外面傳入)
	$m_return = $lc_ip."_".strval(urand(10000000,99999999));
	return $m_return;
}

//****************** 將指定的變數及其內容寫入到 VAR_ 資料庫中
function uset_var($lc_varid){
// $lc_varid 	由 uget_varid($REMOTE_ADDR) 所取得的連線 ID
// 第 2 個變數以後即為要記憶的變數名稱和內容, 需配對
// 範例 : uset_var(uget_varid($REMOTE_ADDR), "m_var1", "aaa", "m_var2", "bbb", "m_var3", "ccc") ;
	$a_pro=func_get_args();
	$m_count=count($a_pro);
	if(!(bcmod($m_count-1,2)==0)){
		die("第二個參數以後, 其參數的數目需配對 ( uset_var() )") ;
	}
	$m_var = "" ;
	for($m_i=1;$m_i<$m_count; $m_i=$m_i+2){
		$m_value = $a_pro[$m_i+1] ;
		$m_type = gettype($m_value) ;
		// 當變數內容是邏輯型態時,需將其內容改成 "true" 或 "false"
		if($m_type =="boolean"){
			if($m_value){
				$m_value = "true";
			}
			else{
				$m_value = "false";
			}
		}
		//組合每一區塊之字串
		$m_var = $m_var . "(CURDATE()"	// 不能加上 ' ', 否則不能正確寫入日期資料
							 .",'".$lc_varid."'"
							 .",'".$a_pro[$m_i]."'"
							 .",'".$m_type."'"
							 .",'".$m_value."'".")"  ;
		if(!($m_i==$m_count- 2)) {
			$m_var = $m_var . ",";
		}
	}
	//將組合後之字串寫入資料庫中
	$m_link = uconnect("aigulic2_COR000") ;
	ucode();
	mysql_query("INSERT INTO VAR_ VALUES $m_var")
					or die("VAR_ 資料表格執行 SQL 時錯誤  ( uset_var() )") ;
	mysql_close($m_link) ;
}



//****************** 從指定的資料庫中取出指定變數的值
function uget_var($lc_varid, &$la_value){
//$lc_varid 傳入連線的id
//$la_value 需使用 [傳址] 的方式傳入接收 [指定變數的值] 之陣列名稱
//第三個參數以後,需傳入要取得值的變數名稱
// 範例 : 	uget_var(uget_varid($REMOTE_ADDR), $a_array, "var1", "var2") ; 其中的陣列不需事先給值
//			$m_var1 = $a_array[0] ;	 第一個變數 var1 的內容 = $a_array[0] , 第二個即為 $a_array[1] ..... 以此類推	
//			$m_var1 = $a_array[1] ;
	$a_array = func_get_args();
	$m_count=count($a_array);
	if($m_count<=2){
		die("需傳入三個以上的參數值 ( uget_var() )");
	}
	// 組合WHERE的條件字串
	$m_var="(";
	for($m_i=2;$m_i < $m_count;$m_i++){
		$m_var= $m_var."VARNAME='".$a_array[$m_i]."'";
		if(!($m_i==$m_count- 1)) {
			$m_var = $m_var . " or ";
		}
		else{
			$m_var = $m_var . ")";
		}
	}
	$m_var = "VARID='$lc_varid' and $m_var";
	$m_link = uconnect("aigulic2_COR000") ; 
	ucode();
	$m_query = mysql_query("SELECT * FROM VAR_ WHERE $m_var",$m_link) 
							or die("VAR_ 資料表格執行 SQL 時錯誤  ( uget_var() )") ;
	while($a_record=mysql_fetch_array($m_query)){
		if($a_record[VARTYPE]=="integer"){ 
			//變數內容是整數時之處理
			$m_return = intval($a_record[VARVALUE]);
		}
		else if($a_record[VARTYPE]=="double"){
			//變數內容是浮點數時之處理
			$m_return = doubleval($a_record[VARVALUE]);
		}
		else if($a_record[VARTYPE]=="boolean"){
			//變數內容是邏輯值時之處理
			if($a_record[VARVALUE]=="true"){
				$m_return=true;
			}
			else{
				$m_return=false;
			}
		}
		else{
			//變數內容是字串時之處理
			$m_return = $a_record[VARVALUE];
		}
		
		//處理接收陣列之值
		$m_l=0 ;
		for($m_k=2;$m_k<$m_count;$m_k++){
			if($a_array[$m_k] == $a_record[VARNAME]){
				$la_value[$m_l] = $m_return;
			}
			$m_l=$m_l+1;
		}
	}
	mysql_query("DELETE FROM VAR_ WHERE $m_var",$m_link) or die(mysql_error()) ;
	mysql_close($m_link);
	return true ;
}
//****************作為輪播圖片用
function uset_image($lc_direction = "up",$lc_fast = 2,$lc_width = 500,$lc_height = 130){
//第一個參數傳入方向  up down left right
//第二個參數傳入速度  數字越小越慢  數字越大越快
//第三個參數傳入整個輪播圖片的寬度
//第四個參數傳入整個輪播圖片的高度
	$m_link = uconnect("aigulic2_COR000") ;
	ucode();
	$query = mysql_query("SELECT A.NO_,A.NAME1 AS IMG_NAME, B.NAME1
							FROM A6200A_IMAGE A LEFT OUTER JOIN A6200A B 
							ON B.NO_=A.NO_
							WHERE A.TYPE_ = 2 AND B.ORDER1='1'",$m_link);
	$side = "";
	$m_num = 0;
	while($a_record = mysql_fetch_array($query)){

			$side[$m_num] = "<a href='../0435001188/heart_pc.php?m_par=$a_record[NO_]' target='_blank'><img src='../A6200A_IMAGE/$a_record[IMG_NAME]' width='153' height='129'></a>";
			$m_num = $m_num + 1;
		
	}
	for($m_j=0;$m_j<count($side);$m_j++){//count傳回陣列的元素個數
		if($lc_direction == "left"||$lc_direction == "right"){
			$slide=$slide.'　'.$side[$m_j];
		}
		else{
			$slide=$slide.'<br>'.$side[$m_j];
		}
	}
	if ("<script>document.all</script>"){
	echo "<marquee direction='".$lc_direction."' scrollAmount='".$lc_fast."' top='300' width='500' height='".$lc_height."' onMouseOut='this.start()' onMouseOver='this.stop()'>".$slide."</marquee>";
	}
}

function ureplace($la_array,$ln_second=2000,$ln_num1){
//$lc_query 為sql語言傳回的變數  本TABLE的結構需要有  1.IMG_NAME 2.NO_  3.NAME1(客戶名稱)
//$ln_second 幾秒呼叫test2()函數(不傳入,則預設為2秒)
//$ln_num1 為此網頁連結的總數
//之後的參數則傳入要替換圖片的物件名稱,沒限定傳入幾個
	$m_imgobj = func_get_args() ;
	$m_alen = count($m_imgobj) ;
	$m_alen_count=$m_alen-3;
	if(gettype($m_imgobj[1])!=integer){//判斷第二個參數是否為數值
		umsg("請傳入第二個參數,或第二個參數需為數值");
	}
	else{
		$m_num=sizeof($la_array);//求出資料筆數,以利得知陣列元素個數(陣列是從0為起始,所以總筆數要-1,才是陣列元素的總各數)
		echo "<script>
			a_array=new Array($m_num);
			for(m_i=0;m_i<$m_num;m_i++){
				a_array[m_i] = new Array(6) ;
			}
			</script>";//宣告一javascript陣列 --> 存圖片的名稱
		//將PHP傳入的陣列寫入到SCRIPT的陣列中
		
		for($m_i=0;$m_i<$m_num;$m_i++){
			
			$a_arr0[$m_i] = $la_array[$m_i][0] ;
			$a_arr1[$m_i] = $la_array[$m_i][1] ;
			$a_arr2[$m_i] = $la_array[$m_i][2] ;
			$a_arr3[$m_i] = $la_array[$m_i][3] ;
			$a_arr4[$m_i] = $la_array[$m_i][4] ;
			$a_arr5[$m_i] = $la_array[$m_i][5] ;
			
			echo "<script>
					a_array[$m_i][0] = $a_arr0[$m_i];
					a_array[$m_i][1] = \"$a_arr1[$m_i]\";
					a_array[$m_i][2] = \"$a_arr2[$m_i]\";
					a_array[$m_i][3] = $a_arr3[$m_i];
					a_array[$m_i][4] = \"$a_arr4[$m_i]\";
					a_array[$m_i][5] = \"$a_arr5[$m_i]\";
					
				  </script>";
		}
		echo "<script>
				
				a_imgobj=new Array($m_alen_count);
			 	for(m_k=0;m_k<$m_alen_count;m_k++){
					a_imgobj[m_k]=new Array(2);
				}
			  </script>";//宣告一javascript陣列 --> 存要替換的圖片物件名稱
		$m_m=0;
		for($m_j=3;$m_j<=($m_alen+1)/2;$m_j++){
			$m_z = $m_j+($m_alen_count/2);
			echo "<script>
					a_imgobj[$m_m][0]=\"$m_imgobj[$m_j]\";
					a_imgobj[$m_m][1]=\"$m_imgobj[$m_z]\";
				  </script>";//給定javascript陣列值 --> 給的參數要替換的圖片物件名稱
			$m_m=$m_m+1;
		}
		
		echo "<script>setInterval('jset_img(a_array,a_imgobj,$m_alen_count,$m_num,$ln_num1)',$ln_second);</script>";
		
		//↑呼叫javescript內建函數setInterval計時器(每$ln_second秒,呼叫test2()函數一次)
	}	
	/*	la_array 傳入公司名稱 圖片編號及 圖片名稱的陣列		la_array[X][0] 公司編號 la_array[X][1] 圖片名稱 la_array[X][2] 公司名稱
		la_imgobj 傳入使用者傳入的img物件名字		la_imgobj[x][0] 前四個物件的名字 la_imgobj[x][1] 後四個物件的名字
		ln_count 傳入使用者傳入幾個img物件名字
		ln_num 傳入資料的比數
		ln_num1 傳入網址的總數	*/
	echo "<script>
		var m_num ;
		function jset_img(la_array,la_imgobj,ln_count,ln_num,ln_num1){
			var m_obj_name, m_num2;
			m_l=jrand(0,ln_num-ln_count);
			m_num = ln_num1;
			m_num1 = m_num+12;
			m_num2 = 0;
			for(m_j=0;m_j<ln_num-2;m_j++){
				m_z=m_num2+4 ;
				m_td_id1 = 'td'+m_num2.toString() ;
				m_td_id2 = 'td'+m_z.toString() ;
				
				document.links[m_num].href='../0435001188/heart_pc.php?m_par='+la_array[m_l+m_j][0];
				document.getElementById(la_imgobj[m_num2][0]).src='../A6200A_IMAGE/'+la_array[m_l+m_j][1];
				document.getElementById(m_td_id1).innerHTML = la_array[m_l+m_j][2];
				
				document.links[m_num1].href='../0435001188/heart_pc.php?m_par='+la_array[m_l+m_j][3];
				document.getElementById(la_imgobj[m_num2][1]).src='../A6200A_IMAGE/'+la_array[m_l+m_j][4];
				document.getElementById(m_td_id2).innerHTML = la_array[m_l+m_j][5];
				
				m_num = m_num + 1;
				m_num1 = m_num1 + 1;
				m_num2 = m_num2 + 1;
				if(m_num2 == 4){
					m_num2 = 0;
				}
			}
		}
		</script>";

}

//****************************讓圖片能再輪流替換,於指定的時間內依次替換完畢
function ureplace2($lc_query,$ln_second=2000,$ln_num1,$ln_tdname){
//$lc_query 為sql語言傳回的變數  本TABLE的結構需要有  1.IMG_NAME 2.NO_  3.NAME1(客戶名稱)
//$ln_second 幾秒呼叫test2()函數(不傳入,則預設為2秒)
//$ln_num1 為此網頁連結的總數
//之後的參數則傳入要替換圖片的物件名稱,沒限定傳入幾個
	$m_img = func_get_args() ;
	$m_alen = count($m_img) ;
	$m_alen_count=$m_alen-4;
	if(gettype($m_img[2])!=integer){//判斷第二個參數是否為數值
		umsg("請傳入第二個參數,或第二個參數需為數值");
	}
	else{
		$m_num=mysql_num_rows($lc_query);//求出資料筆數,以利得知陣列元素個數(陣列是從0為起始,所以總筆數要-1,才是陣列元素的總各數)
		$m_i = 0;
		echo "<script>a_array=new Array();</script>";//宣告一javascript陣列 --> 存圖片的名稱
		echo "<script>a_par=new Array();</script>";//宣告一javascript陣列 --> 存客戶的編號
		echo "<script>a_name=new Array();</script>";
		while($a_record = mysql_fetch_array($lc_query)){
			echo "<script>
					a_array[$m_i]='$a_record[IMG_NAME]';
					a_par[$m_i]=$a_record[NO_];
				  	a_name[$m_i]='$a_record[NAME1]';
				  </script>";//給定javascript陣列值 --> 所有圖片的名稱
			$m_i=$m_i+1;
		}
		echo "<script>a_img=new Array();</script>";//宣告一javascript陣列 --> 存要替換的圖片物件名稱
		$m_m=0;
		for($m_j=4;$m_j<$m_alen;$m_j++){
			echo "<script>
					a_img[$m_m]='$m_img[$m_j]';
				  </script>";//給定javascript陣列值 --> 給的參數要替換的圖片物件名稱
			$m_m=$m_m+1;
		}
		echo "<script>setInterval('jset_img(a_array,a_par,a_name,a_img,$m_alen_count,$m_num,$ln_num1,$ln_tdname)',$ln_second);</script>";
		//↑呼叫javescript內建函數setInterval計時器(每$ln_second秒,呼叫test2()函數一次)
	}
	echo "<script>
		var m_num;
		function jset_img(lc_array,ln_par,lc_name,lc_img,ln_count,ln_num,ln_num1,ln_tdname){
			var m_obj_name, m_num2;
			m_l=jrand(0,ln_num-ln_count);
			m_num = ln_num1;
			for(m_j=0;m_j<ln_count;m_j++){
				m_num2 = m_j+ln_tdname;
				m_obj_name = 'td'+m_num2.toString() ;
				document.links[m_num].href='../0435001188/heart_pc.php?m_par='+ln_par[m_l+m_j];
				document.getElementById(lc_img[m_j]).src='../A6200A_IMAGE/'+lc_array[m_l+m_j];
				document.getElementById(m_obj_name).innerHTML = lc_name[m_l+m_j];
				m_num = m_num + 1;
			}
		}
		</script>";
}

//***************計數器
function ucounter(){
//不虛傳入參數     固定為八張圖八位數
	if(isset($_SESSION[s_count_])){
		$m_link = uconnect("aigulic2_COR000") ;
		$m_query = mysql_query("SELECT COUNTER_ FROM COUNTER_")
					or die("not".mysql_error());
		$m_record2 = mysql_fetch_array($m_query);
		for($m_i=0;$m_i<8;$m_i++){
			$m_sub = substr($m_record2[COUNTER_],$m_i,1);
			$m_i_ = $m_i + 1;
			echo "<script>document.getElementById('img".$m_i_."').src='counter_image/A".$m_sub.".jpg';</script>";
		}
	}
	else{
		session_register("s_count_");
		$_SESSION[s_count_]="";
		$m_link = uconnect("aigulic2_COR000") ;
		$m_result = mysql_query("SELECT COUNTER_ FROM COUNTER_")
					or die("not".mysql_error());
		$m_record = mysql_fetch_array($m_result);
		$m_count = strval(intval($m_record[COUNTER_])+1);
		$m_len = 8-strlen($m_count);
		$m_repeat = str_repeat("0",$m_len);
		$m_count2 = $m_repeat.$m_count;
		mysql_query("UPDATE COUNTER_ SET COUNTER_ = '$m_count2'")
					or die("not".mysql_error());
		$m_query = mysql_query("SELECT COUNTER_ FROM COUNTER_")
					or die("not".mysql_error());
		$m_record2 = mysql_fetch_array($m_query);
		for($m_i=0;$m_i<8;$m_i++){
			$m_sub = substr($m_record2[COUNTER_],$m_i,1);
			$m_i_ = $m_i + 1;
			echo "<script>document.getElementById('img".$m_i_."').src='counter_image/A".$m_sub.".jpg';</script>";
		}
	}
}
//將某數值$ln_frecno做前置$ln_zero位數的零
function ufrecno($ln_frecno,$ln_zero){
	
	$m_frecno=trim(strtr($ln_frecno,"-",""));//將負號去除,且去空白

	//假設傳進的值是null值時,預設值設為1
	if($ln_frecno==null){
		$m_frecno=1;
	}
	$m_zero=str_repeat("0",$ln_zero);//重複$ln_zero個0
	$m_number=intval("-".$ln_zero);//加上負號
	$m_frecno2=substr("$m_zero"."$m_frecno",$m_number);//從右向左取5位數
	return $m_frecno2;
}
//檢查看此字串是不是西元的日期
function ucheckdate($lc_date){
//$lc_date必須是西元的日期字串,例如'2007/07/25',或者是'2007-07-25',20070725

	$m_date=str_replace(" ","",$lc_date);//去空白
	$m_date=str_replace("/","",$m_date);//將$lc_date字串中的/符號轉成"",例如2007/05/25-->20070525
	$m_date=str_replace("-","",$m_date);//將$m_date字串中的-符號轉成"",例如2007-05-25-->20070525
	$m_date=str_replace(" ","",$m_date);//去空白
	if(strlen($m_date)<>8){
		$m_checkdate=false;
	}
	else{
		//例如日期為20060525
		$m_year = intval(substr($m_date,0,4));//擷取後-->2006
		$m_month = intval(substr($m_date,4,2));//擷取後-->05
		$m_day = intval(substr($m_date,6));//擷取後-->25
		$m_checkdate=false;
		if(checkdate($m_month,$m_day,$m_year) && $m_year>=1911){//檢驗是否為正確日期,且是西元年$m_year>=1911
			$m_checkdate=true;
		}
	}
return	$m_checkdate;
}
//連接web通道用
function uconnect_web($lc_name,$lc_str){
//$lc_name為通道名稱
//$ln_i為識別代號;另如 sql,mysql,odbc....等
	if($lc_str=="odbc"){//代表為odbc的通道
		$m_link=odbc_connect($lc_name,"","");//為回傳值
	}
	if($lc_str=="mysql"){//代表為odbc的通道
		$servername = "localhost";
		$username = "moofeec2" ;
		$password = "9957!!@@##" ;
		//連結資料庫
		$m_link = mysql_connect($servername,$username,$password)
			or die("無法連接資料庫".mysql_error()) ;
		//選用資料庫	
		$select = mysql_select_db($lc_name,$m_link)
			or die("無法選用資料庫".mysql_error()) ;
	}
	return $m_link;
}
function uconnect_web2($lc_name,$lc_str){
//$lc_name為通道名稱
//$ln_i為識別代號;另如 sql,mysql,odbc....等
	if($lc_str=="odbc"){//代表為odbc的通道
		$m_link=odbc_connect($lc_name,"","");//為回傳值
	}
	if($lc_str=="mysql"){//代表為odbc的通道
		$servername = "localhost";
		$username = "enter30" ;
		$password = "ACE7880903zor" ;
		//連結資料庫
		$m_link = mysql_connect($servername,$username,$password)
			or die("無法連接資料庫".mysql_error()) ;
		//選用資料庫	
		$select = mysql_select_db($lc_name,$m_link)
			or die("無法選用資料庫".mysql_error()) ;
	}
	return $m_link;
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

				writeWin =window.open('','aWin','top=50%,left=50%,width=1200,status=yes,toolbars=yes,scrollbars=yes,menubar=yes,directories=yes,resizable=yes'); 
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
//將各國的都市轉換成台灣台北的時間
function utime_tran($lc_country){
//lc_country 代表欲轉換時差的都市名  (目前只支援[洛杉磯]字串 "Los Angeles")
	$m_time = date("Y-m-j-H-i-s",mktime());//Y-m-j-H-i-s->年月日時分秒

	$m_array=explode("-","$m_time");//將$time(2007-01-01-05-5)以[-]符號隔開成為陣列
	$m_years=$m_array[0];//年
	$m_months=$m_array[1];//月
	$m_days=$m_array[2];//日
	$m_hour=$m_array[3];//時
	$m_minute=$m_array[4];//分
	$m_second=$m_array[5];//秒
	if($lc_country=="Los Angeles"){//Los Angeles代表洛杉磯
		$m_number=16;
		if($m_months>=4 & $m_months<=10){
			$m_number=15;
		}
	}
	else{
		umess("本函數目前只支援[洛杉磯]時差轉換");
	}
	return date("Y-m-j H:i:s",mktime($m_hour+$m_number,$m_minute,$m_second,$m_months,$m_days,$m_years));//回傳值:年-月-日-時-分-秒
//	return date("Y-m-j-H-i-s",mktime($m_hour+$m_number,$m_minute,$m_second,$m_months,$m_days,$m_years));//回傳值:年-月-日-時-分-秒
}
//將指定的圖片調整成指定的大小
//2009/07/28
//柯菁怡
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
?>