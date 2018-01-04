<?php
	session_start();
	
	include "c/functions.php";
	include "c/db.php";

	header( "content-type:text/plain;charset=utf-8");

	include "data/#userpass.php";
	include "inc_checklogin.php";
	
	$mail_id = get_request( false, "mid" );
	
	//' 开启数据库
	$cls_db = new class_database;
	$cls_db->OpenConnect();
	
	$p = null;
	if ( $mail_id == "[ALL]" )
		$selstr = "select email, count(email) as cnt from return group by email";
	else{
		$selstr = "select email, count(email) as cnt from return where mail_id=? group by email";
		$p = array ( $mail_id );
	}
	
	$cls_db->get_page_data( $selstr, $p, $recordcount );
	$cls_db->getrecords( $selstr, $p, $db );
	
	if ( $recordcount > 0 ){
		header("Content-Disposition: attachment; filename=report.csv");
	    header("ContentType:application/octet-stream;charset=utf-8");

		WriteUTF8ROM();
				
		echo "邮件地址,退订次数\r\n";
	
		while ( $rs = $db->fetch() ){
			echo $rs["email"] . "," . $rs["cnt"] . "\r\n";
		}
	}
	else
		echo "无记录";

	$cls_db->CloseConnect();
?>