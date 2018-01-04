function showdlg(msg, url) {
	if (window.confirm("你确定要执行“" + msg + "”操作吗？") == true) {

		url = url + "&url=" + window.location.toString().replace("&", "%26");
		location.href = (url);
	}
}
function gopre() {
	window.history.back();
}
function gourl(aurl) {
	location.href = aurl;
}

function confirm_input_go(msg, url ) {
	if ( window.prompt("警告！！"+ msg +"请输入[OK]确认您了解正在进行的操作") == "OK" )
	{
		alert_url( url );
	}
}

function confirm_go( msg, url )
{
	if (window.confirm("你确定要执行“" + msg + "”操作吗？") == true) {
		alert_url( url );
	}
}

function alert_url( url ){
	$("#msg").load(url, function(response,status,xhr) {
			alert(response);
			location.href = location.href;
		});
}