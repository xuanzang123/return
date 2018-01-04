<?php
	include "stat_inc.php";
	include "inc_checklogin.php";
?>
<?php
		$howid = get_request( false, "howid");
		
		if ( !Is_Numeric( $howid ) )
			$howstr = "参数错误！";
		
		switch( $howid ){
		case 2:
			$howstr = "重建数据库";
			break;
		case 1:
			$howstr = "压缩数据库";
			break;
		case 0:
			$howstr = "更新数据库";
			break;
		default:
			$howstr = "参数错误！";
			exit;
		}
		
		echo $howstr . "\r\n";

		If ( $howid == 0 )
			process_updatedb();
		
		If ( $howid == 1 )
			process_compactdb();
		
		If ( $howid == 2 )
			process_recreatedb();

		function process_updatedb(){
			echo "PHP 版数据库无需更新";
		}

		function process_compactdb(){
			$cls_db = New class_database;
			$cls_db->OpenConnect();
		    
			$n = $cls_db->m_connect->exec( "VACUUM" );
			echo "压缩完毕！";
			
			$cls_db->CloseConnect();
		}
		
		function process_recreatedb(){
			$cls_db = New class_database;
			$cls_db->create_new_db();
			$cls_db->CloseConnect();
		}
?>
