<?php
	include "c/functions.php";
	include "c/db.php";

	$vrfy=get_request( false, "vrfy");
	if ( $vrfy<>"" ){
		echo "Vrfy OK";
		exit;
	}
	pub_getstatparas( $mail_id, $email, $randcode );
	
	$berror = false;
	
	//' 记录统计
	if ( $mail_id<>"" and strlen($mail_id)>0 and strpos( $email, "@" )>1 ){
		//'读取黑白名单设置
		$badd = get_isadd( $mail_id );
		
		//' 需要添加记录
		if ( $badd ){
			$cls_db = new class_database;
			$cls_db->OpenConnect();
			
		    $cmd = "SELECT * FROM [stat] WHERE mail_id=?;";
            $p =  array ( $mail_id );
		    $cls_db->getrecords( $cmd, $p, $db );
			
			//'添加统计记录
			$rs = $db->fetch();
			if ( $rs )
			{
				$id = $rs["id"];
				$n = $rs["mail_read"];
				$n ++;
				$n = $cls_db->execsql( "UPDATE stat SET mail_read=$n WHERE id=$id" );
			}
			else{
				$mt = $cls_db->m_connect->prepare( "INSERT INTO stat(mail_id, mail_read) VALUES(?, 1)");
				$mt->execute( array( $mail_id ) );
			}
			
			// 重新获取ID
			$cmd = "SELECT * FROM [stat] WHERE mail_id=?;";
			$p =  array ( $mail_id );
		    $cls_db->getrecords( $cmd, $p, $db );
			$rs = $db->fetch();
			$id = $rs["id"];

			//'添加详细记录
			try{
				//var_dump( $_SERVER );
				$ip =  $_SERVER['REMOTE_ADDR'];
				$agent = $_SERVER["HTTP_USER_AGENT"];	
				$t = date('Y-m-d H:i:s',time());
				
				$mt = $cls_db->m_connect->prepare( "INSERT INTO list(mailid, ip, client, email, date, randcode) VALUES(?, ?, ?, ?, ?, ? );");
				$mt->execute( array( $id, $ip, $agent, $email, $t, $randcode) );
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				$berror = true;
			}
			
			$cls_db->CloseConnect();
		}
	}

	if ( ! $berror )
	{
		header( "location:blank.gif" );
		exit;
	}
?>