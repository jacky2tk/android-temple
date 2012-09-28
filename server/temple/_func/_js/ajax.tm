var xmlHttp=false;
try{
	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");// ·sª©ªº IE
}
catch(e){
	try{
		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");// ÂÂª©ªº IE
	}
	catch(e){
		xmlHttp=false;
	}
}


if(!xmlHttp && typeof XMLHttpRequest!='undefiend'){
	xmlHttp=new XMLHttpRequest();
}
function jajax(url){
	xmlHttp.open('post',url,false);
	xmlHttp.setRequestHeader("Content-Type","text/xml;charset=utf-8");
	try{
		xmlHttp.send("");
		if(xmlHttp.readyState==4){
			return xmlHttp.responseText;
		}
		else return null;
	}
	catch(e){
		alert("server busy");
	}
}