<?php
	include "stat_inc.php";
	include "inc_checklogin.php";
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $g_headertext; ?>
</head>
<body>
	<h3>设置邮件标识黑名单</h3>
<?php
	$cls_db = new class_database;
	$cls_db->OpenConnect();
	
	$how=get_request(false, "how");
	if ( $how=="save" ){
		$cls_db->getrecords_txt( "select * from setup", $db );
		$rs = $db->fetch();

		$buseblack = false;
		if ( get_request(true, "buseblack")=="on" )
			$buseblack=true;
		
		$mailids = get_request( true, "mailids");
		//'去掉多余的空行
		$lines = preg_split( "/\r\n/", $mailids );
		$mailids="";
		$n = 0;
		foreach( $lines as $line ){
			$line = str_replace( "\r", "", $line);
			$line = str_replace( "\n", "", $line) ;
			$line = trim($line);
			
			$n ++;
			if ( strLen( $line ) >0 ){
				$mailids .= $line;
				if ($n<count( $lines ) )
					$mailids .= "\r\n";
			}
		}
		//'保存
		$cmd = "UPDATE setup SET buseblack=?, blacklist=?";
		$mt = $cls_db->m_connect->prepare($cmd);
		$n = $mt->execute( array( $buseblack, $mailids ));
		
		if ( $n>0 )
			echo "<script type='text/javascript'>window.alert('设置保存完毕!');</script>";
		else
			echo "<script type='text/javascript'>window.alert('设置保存失败!');</script>";
	}
	
	$cls_db->getrecords_txt( "select * from setup", $db );
	$rs = $db->fetch();
?>
	说明：设置邮件标识黑名单，可以让以后遇到此类邮件标识不再统计。
	<form method="post" action="?how=save">
	<input type="checkbox" name="buseblack" style="border-color: white" <?php if ( $rs["buseblack"] ) echo "Checked"; ?>>启用邮件标识黑名单<br />
	<textarea name="mailids" style="width: 200px; height: 120px"><?php echo $rs["blacklist"]; ?></textarea><br />
	上边边输入邮件标识，每行一个<br />
	<br />
	<input type="submit" value="保存设置"><input type="reset" value="恢复">
	</form>
</body>
</html>
<?php $cls_db->CloseConnect(); ?>
