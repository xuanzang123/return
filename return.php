<?php
	include "c/functions.php";
	include "c/db.php";
	
	$email = get_request(false,"email");
	$how = get_request(false,"how");
	$mail_id = get_request(false,"mid");
	
	if ( $how=="vrfy" ){
		echo "Vrfy OK";
		exit;
	}
	
	pub_getstatparas( $mail_id, $email, $randcode );
	
	$cls_return = new class_database;
	$cls_return->OpenConnect();
	
	if ( $how=="list" )
		$cls_return->OutputList( $mail_id );
	
	//' 清空
	if ( $how=="clear" ){
		if ( $cls_return.DeleteEmails )
			echo "Delete OK";
	}
	//' 退订地址保存
	if ( $email<>"" ){
		if ( strpos( $email,  "RECEIVER_ADDRESS" ) <= 0 ){
			if ( $mail_id<>"" ){
				//'读取黑白名单设置
				$badd = get_isadd( $mail_id );
				if ( $badd )
					Output( $cls_return->InsertEmail( $email, $mail_id ) );
				else
					Output ("邮件标识不存在！");
			}
			else
				Output ("参数错误，缺少邮件标识信息！");
		}
	}
	
	$cls_return->CloseConnect();
	
	function Output( $txt ){
		echo '
<html>
<head>
	<meta http-equiv="Content-Language" content="zh-cn" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
</head>
<body>';
	echo $txt;
	echo '</body>
</html>';
	}
?>
