<?php
// ****************** �N�����W���Ҧ� o_ �}�Y������ [�W��] �M [���e��] �s�J�� _SESSION[] ��
function usession($la_post_vars){
	// �T�w�ǤJ $HTTP_POST_VARS
	// �Ǧ^ �ҫإߪ��Ҧ� _SESSION ���ܼƦW�٤��}�C
	foreach($la_post_vars as $m_key=>$m_value){
		$m_chr = substr(strtolower(trim($m_key)),0,2) ;
		if($m_chr=="o_"){
			$m_session_name = 's_'.$m_key;
			session_register("$m_session_name") ;
			$_SESSION["$m_session_name"] = $m_value ;
		}
	}
}



// �N [����] �����Ҧ����󤧭ȼg�J�� session �}�C��(�i���wsession�}�C�W��)
// �`�N : �p�G�������� [�s�ժ���] (��:checkbox �Bradio ������) ��, 
//		  1. �� name �ݩʻݬO [�}�C] (��Y�b �W�٫᭱�[�W [] )
//		  2. �� id �ݩM name �ݩʤ��ȬۦP, �M��A�[�W [�y���s��] 	
//        3. value �ݩʭȫh�ݩM id �ݩʪ� [�y���s��] �ۦP, ��ƫ��A�i�� [�ƭ�(��ĳ)] �� [�r��] (�ӭȬO�n�s�� [��Ʈw] ������)
function uget_objvalue($la_post_vars, $m_session_name='allobj'){
	// $la_post_vars = �ЩT�w�ǤJ $HTTP_POST_VARS
	// $m_session_name = SESSION ���ܼƦW�� (���ǤJ�h�w�]�� allobj)

	unset($_SESSION["$m_session_name"]) ; // �ݥ������Ӫ�session�ܼ�, �_�h���ɷ|�M��Ӫ��ȲV�X�F, �Ӳ��ͤ��i�w�������~
	$m_num = 0 ;
	foreach($la_post_vars as $m_key=>$m_value){
		$m_chr = substr(strtolower(trim($m_key)),0,2) ;
		if($m_chr=="o_"){
			if(gettype($m_value)=="array"){
				// �B�z�s�ժ��󪺭� (��:�i�ƿ諸[�֨�����B�U�Ԧ�����] �� �h��@��[�I�磌��]��)
				foreach($m_value as $m_value2){ 
					$_SESSION["$m_session_name"][$m_num][0] = $m_key.$m_value2 ; // �}�C���Ĥ@���x�s ���󪺦W��
					$_SESSION["$m_session_name"][$m_num][1] = $m_value2  ; // �}�C���ĤG���x�s ���󪺭�(���@�w�O [�޿��])
					$m_num = $m_num + 1 ;
				}
			}
			else{
				// �B�z��@���󪺭�
				$_SESSION["$m_session_name"][$m_num][0] = $m_key ;	// �}�C���Ĥ@���x�s ���󪺦W��
				$_SESSION["$m_session_name"][$m_num][1] = $m_value ; // �}�C���ĤG���x�s ���󪺭�
				$m_num = $m_num + 1 ;
			}
		}
	}
}


// �N usession2() �Ҭ����b session �}�C�����ȼg�J�����������
function uset_objvalue($m_session_name='allobj'){
	// $m_session_name = SESSION ���ܼƦW�� (���ǤJ�h�w�]�� allobj)
	if(!isset($_SESSION["$m_session_name"])){
		umsg("$"."_SESSION['".$m_session_name."'] ���ܼƩ|���إ�", "",
			 "���楻��ƫe, �ݥ�������L usession2( ), �B���2�Ѽƪ��ǤJ�Ȼݭn�M����Ƥ@��") ;
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


// ****************** ������, ��ܤ@�� [�T�w] ����ܮ�
function umess(){
	//�ݦܤֶǤJ�@�ӥH�W 250 �ӥH�U���Ѽ�, �i�H�ǤJ [�}�C] �� [�ӧO���ܼƩΦr��] �� 2 �̲V�X (�@����� [�ܼƫ��A] �M [����])
	$m_msg = func_get_args() ;
	$m_alen = count($m_msg) ;
	$m_value = "" ;
	$m_chr13 = "" ;
	for($m_x = 0 ; $m_x < $m_alen ; $m_x++){
		if(gettype($m_msg[$m_x])=="array"){	//�ǤJ�ȬO [�}�C] ��
			foreach($m_msg[$m_x] as $m_val){
				$m_len = strval(strlen($m_val)) ;		// �ܼƪ����e������
				$m_type = "(".gettype($m_val).")"	;	// �ܼƪ���ƫ��A
				$m_value = ltrim($m_value.$m_chr13.$m_len.$m_type."__".$m_val) ;
				$m_chr13 = ' \n' ;
			}
		}
		else{	//�ǤJ�ȬO [�r��] ��		
			$m_len = strval(strlen($m_msg[$m_x])) ;		// �ܼƪ����e������
			$m_type = "(".gettype($m_msg[$m_x]).")"	;	// �ܼƪ���ƫ��A
			$m_value = ltrim($m_value.$m_chr13.$m_len.$m_type."__".$m_msg[$m_x]) ;
			$m_chr13 = ' \n' ;
		}
	}
	echo '<script>alert("'.$m_value.'");</script>' ; 
}



// ****************** �зǥ�, ��ܤ@�� [�T�w] ����ܮ� (���|��� [�ܼƫ��A] �M [����])
function umsg(){
	//�ݦܤֶǤJ�@�ӥH�W 250 �ӥH�U���Ѽ�, �i�H�ǤJ [�}�C] �� [�ӧO���ܼƩΦr��] �� 2 �̲V�X
	$m_msg = func_get_args() ;
	$m_alen = count($m_msg) ;
	$m_value = "" ;
	$m_chr13="" ;
	for($m_x = 0 ; $m_x < $m_alen ; $m_x++){
		if(gettype($m_msg[$m_x])=="array"){	//�ǤJ�ȬO [�}�C] ��
			foreach($m_msg[$m_x] as $m_val){
				$m_value = $m_value.$m_chr13.$m_val ;
				$m_chr13 = ' \n' ;
			}
		}	
		else{	//�ǤJ�ȬO [�r��] ��
			$m_value = ltrim($m_value.$m_chr13.$m_msg[$m_x]) ;
			$m_chr13 = ' \n' ;
		}
	}
	echo '<script>alert("'.$m_value.'");</script>' ; 
}



// ****************** �N���}��� [���w] ������
function uset_url($ln_type){
	// $ln_type 	�ഫ�������覡
	// $[lc_msg]	�ഫ�����e�n��ܪ��T��, �p�G�ǤJ"", �h���|��� [��ܮ�]
	// $[lc_url]	�n�ഫ���ؼ� [����] , �i�t�Τ��t���} (ln_type = 2 ��,���Ѽƥi�ǤJ�ť�, ��L�覡�h�n�ǤJ), ���ǤJ�h�w�]���}�� ""
	// $[lc_time]	�p�G ln_type = 1, �h���ѼƬ� [�Ȱ������] (ln_type = 2,3,4 ��,���Ѽƥi�ǤJ�ť�, ��L�覡�h�n�ǤJ), ���ǤJ�h�w�]�� 3 ��
	$m_args = func_get_args() ;
	$m_alen = count($m_args) ;
	$lc_url = "" ;
	$lc_time = 3 ;

	if($m_alen>=2){	// ���ǤJ��2�ӰѼƮ�
		if(!empty($m_args[1])){
			echo "<script> alert('".$m_args[1]."'); </script>" ;
			// �t�@�ؤ覡, ���O���覡����{���}�Y���U ob_start(), �����U ob_end_flush()
//			Header("refresh:3;URL=http://www.aiguli.com/test/tenco/insert.php") ;
		}
	}
	if($m_alen>=3){	// ���ǤJ��3�ӰѼƮ�
		$lc_url = $m_args[2] ;
	}

	if($m_alen>=4){	// ���ǤJ��4�ӰѼƮ�
		$lc_time = $m_args[3] ;
	}	

	switch($ln_type){
		case 1 : 
			if($m_alen < 4){
				umess("��1�ؼҦ�, �ݶǤJ��3�Ѽ�(�ؼк��}) �M ��4�Ѽ�(����ɶ�)") ;
			}
			else{	//�b�����W��ܰT����, ����w�� [�ɶ�] ���^�W�@�� (�Q�� HTML �y�k)
				echo $lc_msg.$lc_time."���|�۰ʪ�^" ;
				echo "<meta http-equiv='refresh' content='".$lc_time.";url=".$lc_url."'>" ;
			}
			break ;
		case 2 :
			if($m_alen >= 3){
				umess("��2�ؼҦ�, ���ݶǤJ��3�ӰѼ�(�ؼк��})") ;
			}
			else{	//��ܹ�ܮ�, �÷|�۰� [��^] ��W�@�� (�Q�� JaveScript �y�k)
				echo '<Script> 
						history.go(-1);
					  </Script>' ;
			}
			break ;
		case 3 :
			if($m_alen < 3){
				umess("��3�ؼҦ�, �ݶǤJ��3�ӰѼ�(�ؼк��})") ;
			}
			else{	//��ܹ�ܮ�, �÷|��� [���w] ������ (�Q�� JaveScript �y�k)
				echo "<script>
						window.location.href='".$lc_url."';
					  </script>" ;
			}
			break ;
		case 4 :
			if($m_alen < 3){
				umess("��4�ؼҦ�, �ݶǤJ��3�ӰѼ�(�ؼк��})") ;
			}
			else{
				// �ϥΥ���k (location.replace(URL)) ���ӥi�NURL���N�쥻�b�s�������檺�����A�ӵL�k�ϥΡu�W�@���v�����s�^��W�@���C
				echo '<Script> 
						window.location.replace("'.$lc_url.'") ;
					  </Script>' ;
			}
			break ;
		case 5 :
			if($m_alen < 3){
				umess("��5�ؼҦ�, �ݶǤJ��3�ӰѼ�(�ؼк��})") ;
			}
			else{
			// �ϥΥ��覡����{���}�Y���Uob_start(),�����Uob_end_flush(),���O�U�F ob_start(),�h�b Header()�P�@�϶�����umess() �N�|����
				Header("Location: $lc_url");
			}
			break ;
	}
}


// ****************** ���o [���}�C] �����r����
function uget_url($ln_type){
	// �Ҧp���}�C�� : htt://www.qksort.com/tecno/test.html
	// $ln_type 0 = ��Ӻ��}�Ǧ^ ( �����t http:// )
	//			1 = �u�� [�D��] �W�� (�Ҧp : www.qksort.com)
	//			2 = �u�� [�D��]�᭱���r�� (�Ҧp : /tecno/test.html)
	//			3 = �u���̫᭱���ɮצW�� (�Ҧp : test.html) (�ɦW�e�����|�[�W '/')
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

//�b�@�ӫ��w�� [�ӷ��r��] �� �j�M�Y�@�ӫ��w�� [�r��] ���� [�ӷ��r��] ���� +1 ��m
function found($lc_b1,$lc_b2,$lc_b3,$lc_b4)  {
//b1	��l�r��
//b2	�r���Ÿ�(��:'+','-','*','/')
//b3	�ĴX���X�{��b2
//b4	�� b3 ��m�䤣�� b2 �r�����B�z   �ǤJ1= �^�� -1     2=�^�ǳ̫�@�� b2 �r�� +1 ����m  
    $m_bb = $lc_b1;
	$m_aa = 1;  // �O�� �C�@�� �j�M�D�� b2 �r������m �ä��H�֥[
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


//****************** ���o�üƭ� 
function urand($ln_min,$ln_max){
//ln_min �n���Ͷüƪ��̤p��
//ln_max �n���Ͷüƪ��̤j��
	srand((double)microtime()*1000000);
	$m_return = rand($ln_min, $ln_max);
	return $m_return;
}



//****************** ���o�s�u ID , �j�����O�� uset_var() �ϥ�
function uget_varid($lc_ip){
// �жǤJ $REMOTE_ADDR �������ܼƭ� (���Ȧp�G�b����Ƥ����o, �h�|���� [�ť�], �ҥH�~�ݭn�b��ƥ~���ǤJ)
	$m_return = $lc_ip."_".strval(urand(10000000,99999999));
	return $m_return;
}

//****************** �N���w���ܼƤΨ䤺�e�g�J�� VAR_ ��Ʈw��
function uset_var($lc_varid){
// $lc_varid 	�� uget_varid($REMOTE_ADDR) �Ҩ��o���s�u ID
// �� 2 ���ܼƥH��Y���n�O�Ъ��ܼƦW�٩M���e, �ݰt��
// �d�� : uset_var(uget_varid($REMOTE_ADDR), "m_var1", "aaa", "m_var2", "bbb", "m_var3", "ccc") ;
	$a_pro=func_get_args();
	$m_count=count($a_pro);
	if(!(bcmod($m_count-1,2)==0)){
		die("�ĤG�ӰѼƥH��, ��Ѽƪ��ƥػݰt�� ( uset_var() )") ;
	}
	$m_var = "" ;
	for($m_i=1;$m_i<$m_count; $m_i=$m_i+2){
		$m_value = $a_pro[$m_i+1] ;
		$m_type = gettype($m_value) ;
		// ���ܼƤ��e�O�޿諬�A��,�ݱN�䤺�e�令 "true" �� "false"
		if($m_type =="boolean"){
			if($m_value){
				$m_value = "true";
			}
			else{
				$m_value = "false";
			}
		}
		//�զX�C�@�϶����r��
		$m_var = $m_var . "(CURDATE()"	// ����[�W ' ', �_�h���ॿ�T�g�J������
							 .",'".$lc_varid."'"
							 .",'".$a_pro[$m_i]."'"
							 .",'".$m_type."'"
							 .",'".$m_value."'".")"  ;
		if(!($m_i==$m_count- 2)) {
			$m_var = $m_var . ",";
		}
	}
	//�N�զX�ᤧ�r��g�J��Ʈw��
	$m_link = uconnect("aigulic2_COR000") ;
	ucode();
	mysql_query("INSERT INTO VAR_ VALUES $m_var")
					or die("VAR_ ��ƪ����� SQL �ɿ��~  ( uset_var() )") ;
	mysql_close($m_link) ;
}



//****************** �q���w����Ʈw�����X���w�ܼƪ���
function uget_var($lc_varid, &$la_value){
//$lc_varid �ǤJ�s�u��id
//$la_value �ݨϥ� [�ǧ}] ���覡�ǤJ���� [���w�ܼƪ���] ���}�C�W��
//�ĤT�ӰѼƥH��,�ݶǤJ�n���o�Ȫ��ܼƦW��
// �d�� : 	uget_var(uget_varid($REMOTE_ADDR), $a_array, "var1", "var2") ; �䤤���}�C���ݨƥ�����
//			$m_var1 = $a_array[0] ;	 �Ĥ@���ܼ� var1 �����e = $a_array[0] , �ĤG�ӧY�� $a_array[1] ..... �H������	
//			$m_var1 = $a_array[1] ;
	$a_array = func_get_args();
	$m_count=count($a_array);
	if($m_count<=2){
		die("�ݶǤJ�T�ӥH�W���Ѽƭ� ( uget_var() )");
	}
	// �զXWHERE������r��
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
							or die("VAR_ ��ƪ����� SQL �ɿ��~  ( uget_var() )") ;
	while($a_record=mysql_fetch_array($m_query)){
		if($a_record[VARTYPE]=="integer"){ 
			//�ܼƤ��e�O��Ʈɤ��B�z
			$m_return = intval($a_record[VARVALUE]);
		}
		else if($a_record[VARTYPE]=="double"){
			//�ܼƤ��e�O�B�I�Ʈɤ��B�z
			$m_return = doubleval($a_record[VARVALUE]);
		}
		else if($a_record[VARTYPE]=="boolean"){
			//�ܼƤ��e�O�޿�Ȯɤ��B�z
			if($a_record[VARVALUE]=="true"){
				$m_return=true;
			}
			else{
				$m_return=false;
			}
		}
		else{
			//�ܼƤ��e�O�r��ɤ��B�z
			$m_return = $a_record[VARVALUE];
		}
		
		//�B�z�����}�C����
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
//****************�@�������Ϥ���
function uset_image($lc_direction = "up",$lc_fast = 2,$lc_width = 500,$lc_height = 130){
//�Ĥ@�ӰѼƶǤJ��V  up down left right
//�ĤG�ӰѼƶǤJ�t��  �Ʀr�V�p�V�C  �Ʀr�V�j�V��
//�ĤT�ӰѼƶǤJ��ӽ����Ϥ����e��
//�ĥ|�ӰѼƶǤJ��ӽ����Ϥ�������
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
	for($m_j=0;$m_j<count($side);$m_j++){//count�Ǧ^�}�C�������Ӽ�
		if($lc_direction == "left"||$lc_direction == "right"){
			$slide=$slide.'�@'.$side[$m_j];
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
//$lc_query ��sql�y���Ǧ^���ܼ�  ��TABLE�����c�ݭn��  1.IMG_NAME 2.NO_  3.NAME1(�Ȥ�W��)
//$ln_second �X��I�stest2()���(���ǤJ,�h�w�]��2��)
//$ln_num1 ���������s�����`��
//���᪺�Ѽƫh�ǤJ�n�����Ϥ�������W��,�S���w�ǤJ�X��
	$m_imgobj = func_get_args() ;
	$m_alen = count($m_imgobj) ;
	$m_alen_count=$m_alen-3;
	if(gettype($m_imgobj[1])!=integer){//�P�_�ĤG�ӰѼƬO�_���ƭ�
		umsg("�жǤJ�ĤG�ӰѼ�,�βĤG�ӰѼƻݬ��ƭ�");
	}
	else{
		$m_num=sizeof($la_array);//�D�X��Ƶ���,�H�Q�o���}�C�����Ӽ�(�}�C�O�q0���_�l,�ҥH�`���ƭn-1,�~�O�}�C�������`�U��)
		echo "<script>
			a_array=new Array($m_num);
			for(m_i=0;m_i<$m_num;m_i++){
				a_array[m_i] = new Array(6) ;
			}
			</script>";//�ŧi�@javascript�}�C --> �s�Ϥ����W��
		//�NPHP�ǤJ���}�C�g�J��SCRIPT���}�C��
		
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
			  </script>";//�ŧi�@javascript�}�C --> �s�n�������Ϥ�����W��
		$m_m=0;
		for($m_j=3;$m_j<=($m_alen+1)/2;$m_j++){
			$m_z = $m_j+($m_alen_count/2);
			echo "<script>
					a_imgobj[$m_m][0]=\"$m_imgobj[$m_j]\";
					a_imgobj[$m_m][1]=\"$m_imgobj[$m_z]\";
				  </script>";//���wjavascript�}�C�� --> �����Ѽƭn�������Ϥ�����W��
			$m_m=$m_m+1;
		}
		
		echo "<script>setInterval('jset_img(a_array,a_imgobj,$m_alen_count,$m_num,$ln_num1)',$ln_second);</script>";
		
		//���I�sjavescript���ب��setInterval�p�ɾ�(�C$ln_second��,�I�stest2()��Ƥ@��)
	}	
	/*	la_array �ǤJ���q�W�� �Ϥ��s���� �Ϥ��W�٪��}�C		la_array[X][0] ���q�s�� la_array[X][1] �Ϥ��W�� la_array[X][2] ���q�W��
		la_imgobj �ǤJ�ϥΪ̶ǤJ��img����W�r		la_imgobj[x][0] �e�|�Ӫ��󪺦W�r la_imgobj[x][1] ��|�Ӫ��󪺦W�r
		ln_count �ǤJ�ϥΪ̶ǤJ�X��img����W�r
		ln_num �ǤJ��ƪ����
		ln_num1 �ǤJ���}���`��	*/
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

//****************************���Ϥ���A���y����,����w���ɶ����̦���������
function ureplace2($lc_query,$ln_second=2000,$ln_num1,$ln_tdname){
//$lc_query ��sql�y���Ǧ^���ܼ�  ��TABLE�����c�ݭn��  1.IMG_NAME 2.NO_  3.NAME1(�Ȥ�W��)
//$ln_second �X��I�stest2()���(���ǤJ,�h�w�]��2��)
//$ln_num1 ���������s�����`��
//���᪺�Ѽƫh�ǤJ�n�����Ϥ�������W��,�S���w�ǤJ�X��
	$m_img = func_get_args() ;
	$m_alen = count($m_img) ;
	$m_alen_count=$m_alen-4;
	if(gettype($m_img[2])!=integer){//�P�_�ĤG�ӰѼƬO�_���ƭ�
		umsg("�жǤJ�ĤG�ӰѼ�,�βĤG�ӰѼƻݬ��ƭ�");
	}
	else{
		$m_num=mysql_num_rows($lc_query);//�D�X��Ƶ���,�H�Q�o���}�C�����Ӽ�(�}�C�O�q0���_�l,�ҥH�`���ƭn-1,�~�O�}�C�������`�U��)
		$m_i = 0;
		echo "<script>a_array=new Array();</script>";//�ŧi�@javascript�}�C --> �s�Ϥ����W��
		echo "<script>a_par=new Array();</script>";//�ŧi�@javascript�}�C --> �s�Ȥ᪺�s��
		echo "<script>a_name=new Array();</script>";
		while($a_record = mysql_fetch_array($lc_query)){
			echo "<script>
					a_array[$m_i]='$a_record[IMG_NAME]';
					a_par[$m_i]=$a_record[NO_];
				  	a_name[$m_i]='$a_record[NAME1]';
				  </script>";//���wjavascript�}�C�� --> �Ҧ��Ϥ����W��
			$m_i=$m_i+1;
		}
		echo "<script>a_img=new Array();</script>";//�ŧi�@javascript�}�C --> �s�n�������Ϥ�����W��
		$m_m=0;
		for($m_j=4;$m_j<$m_alen;$m_j++){
			echo "<script>
					a_img[$m_m]='$m_img[$m_j]';
				  </script>";//���wjavascript�}�C�� --> �����Ѽƭn�������Ϥ�����W��
			$m_m=$m_m+1;
		}
		echo "<script>setInterval('jset_img(a_array,a_par,a_name,a_img,$m_alen_count,$m_num,$ln_num1,$ln_tdname)',$ln_second);</script>";
		//���I�sjavescript���ب��setInterval�p�ɾ�(�C$ln_second��,�I�stest2()��Ƥ@��)
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

//***************�p�ƾ�
function ucounter(){
//����ǤJ�Ѽ�     �T�w���K�i�ϤK���
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
//�N�Y�ƭ�$ln_frecno���e�m$ln_zero��ƪ��s
function ufrecno($ln_frecno,$ln_zero){
	
	$m_frecno=trim(strtr($ln_frecno,"-",""));//�N�t���h��,�B�h�ť�

	//���]�Ƕi���ȬOnull�Ȯ�,�w�]�ȳ]��1
	if($ln_frecno==null){
		$m_frecno=1;
	}
	$m_zero=str_repeat("0",$ln_zero);//����$ln_zero��0
	$m_number=intval("-".$ln_zero);//�[�W�t��
	$m_frecno2=substr("$m_zero"."$m_frecno",$m_number);//�q�k�V����5���
	return $m_frecno2;
}
//�ˬd�ݦ��r��O���O�褸�����
function ucheckdate($lc_date){
//$lc_date�����O�褸������r��,�Ҧp'2007/07/25',�Ϊ̬O'2007-07-25',20070725

	$m_date=str_replace(" ","",$lc_date);//�h�ť�
	$m_date=str_replace("/","",$m_date);//�N$lc_date�r�ꤤ��/�Ÿ��ন"",�Ҧp2007/05/25-->20070525
	$m_date=str_replace("-","",$m_date);//�N$m_date�r�ꤤ��-�Ÿ��ন"",�Ҧp2007-05-25-->20070525
	$m_date=str_replace(" ","",$m_date);//�h�ť�
	if(strlen($m_date)<>8){
		$m_checkdate=false;
	}
	else{
		//�Ҧp�����20060525
		$m_year = intval(substr($m_date,0,4));//�^����-->2006
		$m_month = intval(substr($m_date,4,2));//�^����-->05
		$m_day = intval(substr($m_date,6));//�^����-->25
		$m_checkdate=false;
		if(checkdate($m_month,$m_day,$m_year) && $m_year>=1911){//����O�_�����T���,�B�O�褸�~$m_year>=1911
			$m_checkdate=true;
		}
	}
return	$m_checkdate;
}
//�s��web�q�D��
function uconnect_web($lc_name,$lc_str){
//$lc_name���q�D�W��
//$ln_i���ѧO�N��;�t�p sql,mysql,odbc....��
	if($lc_str=="odbc"){//�N��odbc���q�D
		$m_link=odbc_connect($lc_name,"","");//���^�ǭ�
	}
	if($lc_str=="mysql"){//�N��odbc���q�D
		$servername = "localhost";
		$username = "moofeec2" ;
		$password = "9957!!@@##" ;
		//�s����Ʈw
		$m_link = mysql_connect($servername,$username,$password)
			or die("�L�k�s����Ʈw".mysql_error()) ;
		//��θ�Ʈw	
		$select = mysql_select_db($lc_name,$m_link)
			or die("�L�k��θ�Ʈw".mysql_error()) ;
	}
	return $m_link;
}
function uconnect_web2($lc_name,$lc_str){
//$lc_name���q�D�W��
//$ln_i���ѧO�N��;�t�p sql,mysql,odbc....��
	if($lc_str=="odbc"){//�N��odbc���q�D
		$m_link=odbc_connect($lc_name,"","");//���^�ǭ�
	}
	if($lc_str=="mysql"){//�N��odbc���q�D
		$servername = "localhost";
		$username = "enter30" ;
		$password = "ACE7880903zor" ;
		//�s����Ʈw
		$m_link = mysql_connect($servername,$username,$password)
			or die("�L�k�s����Ʈw".mysql_error()) ;
		//��θ�Ʈw	
		$select = mysql_select_db($lc_name,$m_link)
			or die("�L�k��θ�Ʈw".mysql_error()) ;
	}
	return $m_link;
}
//�s��sql�R�O���᪺���G
function ubrows($lc_sql){
//$lc_sql��mysql_query()�ҶǦ^���ܼ�
	$m_field=mysql_num_fields($lc_sql);//�D�X�d�ߪ�sql�R�O���@���X�����
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
			$m_color="FFFFFF";//��ƲĤ@��,�N�ϥΥզ�,�ĤG���L���,�ĤT���զ�,�H������
		}
		$m_table=$m_table.'<tr bgcolor="'.$m_color.'">';
		for($m_k=0;$m_k<$m_field;$m_k++){//$m_field����sql�R�O������`��
			$m_str=mysql_field_name($lc_sql,$m_k);
			$m_type=mysql_field_type($lc_sql,$m_k);//���w��쪺���A
			$m_len=mysql_field_len($lc_sql,$m_k);//���w��쪺����
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
	//window��������
	//menubar-->�\���C [�Ҧp] [�ɮ�] [�s��] [�˵�] [�ڪ��̷R] .. �� �o�ǫ��s,�Y�S���]�w,�h�w�]������
	//scrollbars-->�W�U�Ԧ����b �Y�S���]�w�h�]���u��ܵ����j�p�����e
	//resizable���O�_���\�ϥΪ̧��ܵ����j�p,�Y���]�w,�w�]�������\
	echo "<script>writeLeft();</script>";
	
}
//�N�U�ꪺ�����ഫ���x�W�x�_���ɶ�
function utime_tran($lc_country){
//lc_country �N����ഫ�ɮt�������W  (�ثe�u�䴩[�����F]�r�� "Los Angeles")
	$m_time = date("Y-m-j-H-i-s",mktime());//Y-m-j-H-i-s->�~���ɤ���

	$m_array=explode("-","$m_time");//�N$time(2007-01-01-05-5)�H[-]�Ÿ��j�}�����}�C
	$m_years=$m_array[0];//�~
	$m_months=$m_array[1];//��
	$m_days=$m_array[2];//��
	$m_hour=$m_array[3];//��
	$m_minute=$m_array[4];//��
	$m_second=$m_array[5];//��
	if($lc_country=="Los Angeles"){//Los Angeles�N�����F
		$m_number=16;
		if($m_months>=4 & $m_months<=10){
			$m_number=15;
		}
	}
	else{
		umess("����ƥثe�u�䴩[�����F]�ɮt�ഫ");
	}
	return date("Y-m-j H:i:s",mktime($m_hour+$m_number,$m_minute,$m_second,$m_months,$m_days,$m_years));//�^�ǭ�:�~-��-��-��-��-��
//	return date("Y-m-j-H-i-s",mktime($m_hour+$m_number,$m_minute,$m_second,$m_months,$m_days,$m_years));//�^�ǭ�:�~-��-��-��-��-��
}
//�N���w���Ϥ��վ㦨���w���j�p
//2009/07/28
//�_�ש�
function uimage_reduce($lc_src,$lc_dst,$ln_width=0,$ln_height=0){
	/* $lc_src �Ϥ��ӷ�
	   $lc_dst ���Y�᪺�Ϥ��s����|
	   $ln_width �]�w���e��
	   $ln_height �]�w������*/
	$src = imagecreatefromjpeg($lc_src);
	// ���o�ӷ��Ϥ����e
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
	// �إ��Y��
	$thumb = imagecreatetruecolor($thumb_w, $thumb_h);
	// �}�l�Y��
	$m_pled = imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
	// �x�s�Y�Ϩ���w thumb �ؿ�
	$m_jpeg = imagejpeg($thumb, $lc_dst);
	// �ƻs�W�ǹϤ�����w images �ؿ�
	if(!($m_pled)||!($m_jpeg)){
		return false ;
	}
	else{
		return true ; 
	}
//	copy($lc_src,$lc_dst);
}
?>