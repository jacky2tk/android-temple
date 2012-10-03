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
		$m_distance = (float)$_GET['dist'] ;		// 取得距離
		
		if ($_GET['locat'] == "") $m_location = preg_split('/,/', "24.2557,120.7205");
		if ($m_distance == "") $m_distance = (float)200.0;
		
		if ($m_search != "") {
			$m_query = DB_QUERY("SELECT m_name,m_god,m_address,m_lat,m_lng FROM $GLOBALS[DB_TEMPLE] WHERE m_name LIKE '%$m_search%'");
		} else {
			$m_query = DB_QUERY("SELECT m_name,m_god,m_address,m_lat,m_lng FROM $GLOBALS[DB_TEMPLE]");
		}
		$m_rows = mysql_num_rows($m_query);	// 取得資料筆數
		
		// 將資料組成以逗號相隔的字串
		$json['html_attributions'] = "寺廟瀏覽服務網";
		$json['results'] = array();
		$itemCount = 0;		// 計算找到的地標數量
		for ($i = 0; $i < $m_rows; $i++) {
			$m_q = mysql_fetch_array($m_query);
			
			// 書本使用的兩點計算方式 (仍有問題)
			//$dist = (abs((float)$m_location[0] - (float)$m_q['m_lat']) + abs((float)$m_location[1] - (float)$m_q['m_lng']));
			// 網路找到的兩點計算方式 (單位: 米)
			//$dist = 100;
			$dist = GetDistance($m_location[0], $m_location[1], $m_q['m_lat'], $m_q['m_lng']);
			
			// echo ($i+1). ". ". $m_q['m_name']. "&nbsp;&nbsp; $dist M &nbsp;&nbsp;&nbsp;(". $m_location[0]. ", ". $m_location[1]. ") - (".
									   	 // $m_q['m_lat']. ", ". $m_q['m_lng']. ")<BR>";

			if ($dist <= $m_distance) {
				// 因 JSON 必須傳輸的資料必須是 UTF-8 編碼, 若資料有中文字會出現亂碼
				// 所以在 JSON 編碼前, 先經過 URL 編碼
				// http://phpwolf.blogspot.tw/2012/04/php-json.html
				$temp['name'] = urlencode($m_q['m_name']);
				$temp['god'] = urlencode($m_q['m_god']);
				$temp['address'] = urlencode($m_q['m_address']);
				$temp['geometry']['location'] = array(
					'lat' => (double)$m_q['m_lat'], 
					'lng' => (double)$m_q['m_lng']);
					
				array_push($json['results'], $temp);
				$itemCount++;
			}
			
		}
		
		$json['status'] = ($itemCount > 0 ? 'OK' : 'Empty');
		
		// Data -> JSON Encode -> URL Decode
		$m_result = urldecode(json_encode($json));
		echo $m_result;
		break;
		
}

/**
* 計算兩組經緯度座標間的距離
* params:lat1緯度1,lng1經度1,lat2緯度2,lng2經度2,len_type(1:m|2:km);
* Echo GetDistance($lat1,$lng1,$lat2,$lng2).'米';
*/
function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2) {
	$EARTH_RADIUS = 6378.137;	 //地球半徑,假設地球是規則的球體
	//$PI = 3.1415926;	 //圓周率
	
	$radLat1 = $lat1 * pi() / 180.0;
	$radLat2 = $lat2 * pi() / 180.0;
	$a = $radLat1 - $radLat2;
	$b = ($lng1 * pi() / 180.0) - ($lng2 * pi() / 180.0);
	$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
	$s = $s * $EARTH_RADIUS;
	$s = round($s * 1000);
	
	if ($len_type > 1){
		$s /= 1000;
	}
	
	return round($s, $decimal);
}

?>