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
	// http://localhost/temple/agent.php?case=temple_list&search=%E5%BB%9F&locat=24.2557,120.7205&dist=0.4
	// 上益電腦: 24.2557, 120.7205
	case "temple_list":
		$m_search = $_GET['search'] ;		// 取得搜尋條件
		$m_location = preg_split('/,/', $_GET['locat']) ;	// 取得目前座標位置
		$m_distance = $_GET['dist'] ;		// 取得距離
		
		if ($m_distance == "") $m_distance = "150";
		if ($m_location == "") $m_location = "24.2557,120.7205";
		
		if ($m_search != "") {
			$m_query = DB_QUERY("SELECT * FROM $GLOBALS[DB_TEMPLE] WHERE m_name LIKE '%$m_search%'");
		} else {
			$m_query = DB_QUERY("SELECT * FROM $GLOBALS[DB_TEMPLE]");
		}
		$m_rows = mysql_num_rows($m_query);	// 取得資料筆數
		
		// 將資料組成以逗號相隔的字串
		$json['html_attributions'] = "寺廟瀏覽服務網";
		for ($i = 0; $i < $m_rows; $i++) {
			$m_q = mysql_fetch_array($m_query);
			
			$dist = (abs($m_location[0] - $m_q['m_lat']) + abs($m_location[1] - $m_q['m_lng']));
			//echo "$i. $dist<BR>";
			if ($dist <= $m_distance) {
						
				// 因 JSON 必須傳輸的資料必須是 UTF-8 編碼, 若資料有中文字會出現亂碼
				// 所以在 JSON 編碼前, 先經過 URL 編碼
				// http://phpwolf.blogspot.tw/2012/04/php-json.html
				$json['results'][$i]['name'] = urlencode($m_q['m_name']);
				$json['results'][$i]['god'] = urlencode($m_q['m_god']);
				$json['results'][$i]['address'] = urlencode($m_q['m_address']);
				$json['results'][$i]['geometry']['location'] = array(
					'lat' => (double)$m_q['m_lat'], 
					'lng' => (double)$m_q['m_lng']);
					
			}
		}
		
		$json['status'] = ($m_rows > 0 ? "OK" : "Empty");
		
		// Data -> JSON Encode -> URL Decode
		$m_result = urldecode(json_encode($json));
		echo $m_result;
		break;
		
}

?>