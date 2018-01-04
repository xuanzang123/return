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

	$sortsql = get_sort_paras_defsc( $sortby, $sc, "id", "id,mail_id,mail_read", "DESC" ) ;
	$selstr = "select * from stat" . $sortsql;

	$cls_db = new class_database;	
	$cls_db->OpenConnect();
	$rs = null;
	$cls_db->get_page_data( $selstr, null, $recordcount, $page, $pagesize, $pagecount, 1);

	// 添加 PAGE  信息到SQL语句
	$selstr = $cls_db->sql_appent_pageinfo( $selstr, $page, $pagesize );
	// 读取数据
	$cls_db->getrecords_txt( $selstr, $rs );	
?>
	<form action="stat_query.php" method="get">
	<?php writehead(1); ?>
	<div class="d_head_operline">
		<div style="width: 100px; float: left;">
			<b>操作：</b>
			<input value="刷新" type="button" onclick="window.location.reload();return false;">
		</div>
		<div style="overflow: hidden; float: left;">
			<input type="radio" value="0" name="qtype" checked="checked" style="border: 0px;" />Email
			<input type="radio" value="1" name="qtype" style="border: 0px;" />IP
			<input type="text" name="q" />
			<input type="submit" value="搜索" />
		</div>
		<div style="width: 50px; text-align: center; float: right;">
			<input value="退出" type="button" style="float:right" onclick="javascript:gourl('stat_login.php?logout=true');return false;" />
		</div>
	</div>
	<br />
	</form>
	<table class="pagew datagrid">
		<tr height="20" bgcolor="MintCream">
			<th align="center" style="width: 60px" class="datacell">
				<?php echo write_sorthead("ID","id", $sc, $sortby); ?>
			</th>
			<th align="center" style="width: 200px" class="datacell">
				<?php echo write_sorthead("邮件标识","mail_id", $sc, $sortby); ?>
			</th>
			<th align="center" style="width: 100px" class="datacell">
				<?php echo write_sorthead("总阅读次数","mail_read", $sc, $sortby); ?>
			</th>
			<th align="center" style="width: 100px" class="datacell">
				已阅读地址数
			</th>
			<th align="center" style="width: 300px" class="datacell">
				操作
			</th>
		</tr>
<?php	
	if ( $recordcount>0 ){
		$num = 0;
		while ( $a = $rs->fetch() ){
			$num ++;
			
			if ( fmod( $num, 2 ) ==0 ) 
				echo '<tr class="alt">';
			else
				echo '<tr>';
			
			$rid = $a["id"];

			echo '<td align="left" class="datacell">';
				echo $a["id"];
			echo '</td>';
			echo '<td align="left" class="datacell">';
				echo $a["mail_id"];
			echo '</td>';
			echo '<td style="padding-right: 10px; text-align: right;" class="datacell">';
				echo $a["mail_read"];
			echo '</td>';
			echo '<td style="padding-right: 10px; text-align: right;" class="datacell">';
				$sql2 =  "SELECT COUNT(email) AS cnt FROM (SELECT email FROM (SELECT email FROM list WHERE (mailid = " . $rid . ")) GROUP BY email)";
				$cls_db->getrecords_txt($sql2, $rs2 );
				if ( $rs2 )
				{
					$a2 = $rs2->fetch();
					echo $a2["cnt"];
				}		
			echo '</td>';
			echo '<td align="center" class="toolbar">';
				echo "<a class=\"toolbt\" title=\"查看详细记录\" href=\"stat_lists.php?mid=$rid\">详细</a>";
				echo "<a class=\"toolbt\" title=\"查看访问次数\" href=\"stat_times.php?mid=$rid\">次数</a>";
				echo "<a class=\"toolbt\" title=\"日期统计\" href=\"stat_byday.php?mid=$rid\">日期</a>";
				echo "<a class=\"toolbt\" title=\"时间统计\" href=\"stat_byday.php?mid=$rid&tjby=1\">时间</a>";
				echo "<a class=\"toolbt\" title=\"查看按地区、系统、客户端统计图表\" href=\"stat_area.php?mid=$rid\">图表</a>";
				echo "<a class=\"toolbt\" title=\"导出Email地址\" href=\"stat_export.php?mid=$rid\">导出</a>";
				echo "<a class=\"toolbt\" title=\"删除此标识所有记录\" href=\"\" onclick=\"confirm_go('删除此邮件标识的所有记录','stat_opt.php?del=true&mid=$rid');return false;\">删除</a>";
			echo '</td>';
		echo '</tr>';

		}
	}
?>
	</table>
<?php
	include "c/page.php";
	WritePageSelection ( "", $pagecount, $page );	
	$cls_db->CloseConnect();
?>
<div style="display:none;" id="msg"></div>
</body>
</html>
