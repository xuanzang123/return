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
<?php
	include "inc_title.php";
	include "inc_head.php";
	include "c/agent.php";
	
	$mail_id = get_request( false, "mid" );
	if ( ! Is_Numeric( $mail_id ) ){
		exit ( "mail_id 参数错误" );
	}

	//开启数据库
	$cls_db = new class_database;	
	$cls_db->OpenConnect();
	
	// 邮件标识
	$selstr = "select * from stat where id=" . $mail_id;
	$mail_idname = $cls_db->get_a_value( $selstr, "mail_id" );
	
		
	// 数据SQL
	$sortsql = get_sort_paras( $sortby, $sc, "email", "email,ip,date" ) ;
	$selstr = "select * from list where mailid=" . $mail_id . $sortsql;
	// 设置页数
	$cls_db->get_page_data( $selstr, null, $recordcount, $page, $pagesize, $pagecount, 2);
	// 添加 PAGE  信息到SQL语句
	$selstr = $cls_db->sql_appent_pageinfo( $selstr, $page, $pagesize );
	// 读取数据
	$cls_db->getrecords_txt( $selstr, $rs );	
?>
	<?php writehead(1); ?>
	<div class="d_head_operline">
		<div style="width: 200px; float: left;">
			<b>操作：</b>
			<input type="button" value="首页" onclick="javascript:gourl('stat_view.php');">
			<input value="返回" type="button" onclick="gopre();">
		</div>
		<div style="width: 200px; line-height: 20px; text-align: center; float: right;">
			邮件标识：<?php echo $mail_idname; ?>
		</div>
	</div>
	<br />
	<table class="pagew datagrid">
		<tr height="20" bgcolor="MintCream">
			<th class="datacell" style="width:180px">
				<?php echo write_sorthead("邮件地址","email", $sc, $sortby); ?>
			</th>
			<th class="datacell" style="width:60px">
				随机码
			</th>
			<th class="datacell" style="width:110px">
				<?php echo write_sorthead("IP","ip", $sc, $sortby); ?>
			</th>
			<th class="datacell" style="width:80px">
				地区
			</th>
			<th class="datacell" style="width:150px">
				<?php echo write_sorthead("查看日期","date", $sc, $sortby); ?>
			</th>
			<th class="datacell">
				操作系统
			</th>
			<th class="datacell">
				浏览器
			</th>
		</tr>
<?php		
	if ($recordcount>0 ){
		$num = 0;
		$cua =new checkuseragent;
		while ( $a = $rs->fetch() ){
			$num++;		
			
			try {
				$cua->execute ( $a["client"] );
			}catch(Exception $e){}
				
			if ( fmod( $num, 2 ) == 0 )
				echo '<tr class="alt">';
			else
				echo '<tr>';
			
			echo '<td class="datacell">' . $a["email"] . '</td>';
			echo '<td class="datacell">' . $a["randcode"] . '</td>';
			echo '<td class="datacell">' . $a["ip"] . '</td>';
			echo '<td class="datacell">' . findArea($a["ip"]) . '</td>';
			echo '<td class="datacell">' . $a["date"] . '</td>';
			echo '<td class="datacell"><span class="agent" title="' . $cua->vos . '">' . substr( $cua->vos, 0, 20) . '&nbsp;</span></td>';
			echo '<td class="datacell"><span class="agent" title="' . $cua->vsoft . '">' . substr($cua->vsoft, 0,15) . '&nbsp;</span></td>';
			echo '</tr>';
		}
	}
?>
	</table>
<?php
	include "c/page.php";
	WritePageSelection("", $pagecount, $page);
	$cls_db->CloseConnect();
?>
</body>
</html>