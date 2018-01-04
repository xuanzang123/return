<?php
	include "stat_inc.php";
	include "inc_checklogin.php";

	$dbname = get_request( false, "db");
	$mail_id = get_request( false, "mid" );
	
	//' 开启数据库
	$cls_db = new class_database;
	$cls_db->OpenConnect();
	$del = get_request( false, "del" );
	if ( strcmp($del, "true")==0 ){
		if ( $mail_id<>"" ){
			if ( $dbname=="return" ){
				$selstr = "delete from return where mail_id=?;";
				$p = array($mail_id);
				$n = $cls_db->execsql($selstr, $p);
			}
			else{
				$selstr = "delete from stat where id=?;";
				$p = array($mail_id);
				$n = $cls_db->execsql($selstr, $p);
				
				$selstr = "delete from list where mailid=?;";
				$p = array($mail_id);
				$n .= "," . $cls_db->execsql($selstr, $p);
			}
			echo "数据已清除！ $n\r\n";
		}
		else{
			if ($dbname=="stat")
			{
				$selstr = "delete from stat";
				$cls_db->execsql($selstr);
			}
			elseif ($dbname=="return")
			{
				$selstr = "delete from return";
				$cls_db->execsql($selstr);
			}
			else{
				$selstr = "delete from stat";
				$cls_db->execsql( $selstr );
				$selstr = "delete from list";
				$cls_db->execsql( $selstr );
			}
			echo "数据已清除！";
		}
	}
	else{
		echo "未知操作！";
	}

	$cls_db->CloseConnect();
?>
