<?php
	session_start();
	
	include "c/functions.php";
	include "c/db.php";

	header( "content-type:text/plain;charset=utf-8");

	include "data/#userpass.php";
	include "inc_checklogin.php";

	$mail_id = get_request( false, "mid" );
	if ( ! Is_Numeric( $mail_id ) )
		exit( "mail_id 参数错误" );

	//' 开启数据库
	$cls_db = new class_database;
	$cls_db->OpenConnect();
	
	if ( $mail_id == -1 )
		$selstr = "select ip,client,email,date,randcode from list";
	else
		$selstr = "select ip,client,email,date,randcode from list where mailid=" . $mail_id . "";

	$cls_db->getrecords_txt( $selstr, $db );
	$cls_db->get_page_data( $selstr, null, $recordcount );
	
	if ( $recordcount>0 ){
		header("Content-Disposition: attachment; filename=report.csv");
	    header("ContentType:application/octet-stream;charset=utf-8");

		WriteUTF8ROM();
		include "c/agent.php";
		
		echo "邮件地址,日期,IP,地区,系统,浏览器,随机码\r\n";
		
		$cua =new checkuseragent;
		
		while ( $rs = $db->fetch()){
			echo $rs["email"] . ",";
			echo $rs["date"] . ",";
			echo $rs["ip"] . ",";
			echo findArea($rs["ip"]) . ",";
			try {
				$cua->execute ( $rs["client"] );
			}catch(Exception $e){}
			echo $cua->vos . ",";
			echo $cua->vsoft . ",";
			echo $rs["randcode"];
			echo "\r\n";
		}
	}
	else
		echo "无记录";

	$cls_db->CloseConnect();
?>