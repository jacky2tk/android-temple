<?php
#Query
function DB_QUERY($SQL) {
	#echo $SQL. "<BR>";
	mysql_connect($GLOBALS["SQL_HOST"], $GLOBALS["SQL_USER"], $GLOBALS["SQL_PASS"]) or DIE ("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><div align=center><h2>Server 忙錄中，請稍後再試；或請連絡管理人員</h2></div>");
	mysql_query("SET NAMES UTF8");
	$RES = mysql_db_query($GLOBALS["SQL_NAME"], $SQL) or DIE ("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><div align=center><h2>資料表錯誤，請連絡管理人員</h2></div>".mysql_error());
	return $RES;
}

#Insert
function DB_INSERT($Table, $Variable, $Value) {
	is_array($Variable) or DIE("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><div align=center><h2>資料錯誤：$Table 資料型態錯誤!!</h2></div>");
	$Value = Trims($Value);
	count($Variable) == count($Value) or DIE ("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><div align=center><h2>資料錯誤：$Table 欄位數 與 新增資料數不符!!</h2></div>".mysql_error());
	$Variable = join(",", $Variable);
	$Value = join("','", $Value);
	$SQL="INSERT INTO $Table ($Variable) VALUES ('$Value');";
	DB_QUERY($SQL);
}

#Update
function DB_UPDATE($Table, $Variable, $Value, $Where) {
	is_array($Variable) or DIE("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><div align=center><h2>資料錯誤：$Table 資料型態錯誤!!</h2></div>");
	$Value = Trims($Value);
	count($Variable) == count($Value) or DIE ("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><div align=center><h2>資料錯誤：$Table 欄位數 與 更新資料數不符!!</h2></div>".mysql_error());
	foreach($Variable AS $Key => $Var){
		$Update[$Key] = "`$Var`='". $Value[$Key]. "'";
	}
	$Update = join(",",$Update);
	$Where?$SQL = "UPDATE $Table SET $Update  WHERE $Where;":$SQL = "UPDATE $Table SET $Update;";
	DB_QUERY($SQL);
}

#Delete
function DB_DELETE($Table, $Where) {
	$SQL = "DELETE FROM $Table WHERE $Where";
	DB_QUERY($SQL);
	$SQL = "OPTIMIZE TABLE $Table";
	DB_QUERY($SQL);
}

#去反斜線
function Strip($row){
	if(!get_magic_quotes_gpc()) return $row;
	if(is_Array($row)){
	foreach($row AS $key=>$value){
		if(is_Array($row[$key])) $row[$key]=Strip($row[$key]);
		else $row[$key]=stripslashes($value);
	}
	}else $row=stripslashes($row);
	return $row;
}

#加反斜線
function Add($row){
	if(get_magic_quotes_gpc()) return $row;
	if(is_Array($row)){
	foreach($row AS $key=>$value){
		if(is_Array($row[$key])) $row[$key]=Add($row[$key]);
		else $row[$key]=AddSlashes($value);
	}
	}else $row=AddSlashes($row);
	return $row;
}

#去除前後空白
function Trims($row){
	if(is_Array($row)){
		foreach($row as $key => $value){
			if(is_Array($row[$key])) $row[$key]=Trims($row[$key]);
			else $row[$key]=Add(Trim($value));
		}
	}else Add(Trim($row));
	return $row;
}
?>
