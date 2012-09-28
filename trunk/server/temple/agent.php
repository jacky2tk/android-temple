<?php
	header("Content-Type:text/html; charset=utf-8");
?>
<?php
include("_func/config.inc.php");

// GET 傳入值
$m_case = $_GET['case'] ; 	// 取得傳入值 case
$m_data = $_GET['data'] ;	// 取得使用者輸入資訊

$CONST_SPLITER = ",";		// 資料分隔符號

/*
mysql_query("SET NAMES 'utf8'"); 
mysql_query("SET CHARACTER_SET_CLIENT=utf8"); 
mysql_query("SET CHARACTER_SET_RESULTS=utf8");
*/

switch ($m_case) {
	// --------------------------------------------------------------
	// 書本清單
	// 回傳值: 逗號分隔的書本名稱
	case "temple_list":
		$m_query = DB_QUERY("SELECT * FROM $GLOBALS[DB_TEMPLE]");
		$m_rows = mysql_num_rows($m_query);	// 取得資料筆數
		
		// 將資料組成以逗號相隔的字串
		$m_result = "";
		for ($i = 0; $i < $m_rows; $i++) {
			$m_q = mysql_fetch_array($m_query);
			
			// 因 JSON 必須傳輸的資料必須是 UTF-8 編碼, 若資料有中文字會出現亂碼
			// 所以在 JSON 編碼前, 先經過 URL 編碼
			// http://phpwolf.blogspot.tw/2012/04/php-json.html
			$json[$i]['name'] = urlencode($m_q['m_name']);
			$json[$i]['god'] = urlencode($m_q['m_god']);
			$json[$i]['address'] = urlencode($m_q['m_address']);
			$json[$i]['location'] = array(
				'lat' => $m_q['m_lat'], 
				'lng' => $m_q['m_lng']);
		}
		
		// Data -> JSON Encode -> URL Decode
		$m_result = urldecode(json_encode($json));
		echo $m_result;
		break;
		
}

?>