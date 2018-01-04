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
	writehead(3);
?>
	<div class="d_head_operline">
		<div style="width: 100px; float: left;">
			<b>操作：</b>
			<input value="刷新" type="button" onclick="window.location.reload();return false;">
		</div>
		<div style="overflow: hidden; float: left;">
		</div>
		<div style="width: 50px; text-align: center; float: right;">
			<input value="退出" style="float:right" type="button" onclick="javascript:gourl('stat_login.php?logout=true');return false;" />
		</div>
	</div>
	<br />
	<table class="pagew datagrid">
		<tr>
			<th style="width:50%;">
				综合设置
			</th>
			<th style="width:50%">
				下载数据
			</th>
		</tr>
		<tr>
			<td style="background-color: White; padding: 15px; border:1px solid #98bf21;" valign="top">
				<input value="常规设置" type="button" onclick="window.open('set_other.php','','width=480,height=400');return false;" />
				<input value="设置白名单" type="button" onclick="window.open('set_white.php','','width=480,height=400');return false;" />
				<input value="设置黑名单" type="button" onclick="window.open('set_black.php','','width=480,height=400');return false;" /><br />
				<br />
				<input value="清除所有统计记录" type="button" onclick="confirm_go('清除所有统计记录','stat_opt.php?del=true&db=stat');return false;" />
				<input value="清除所有退订记录" type="button" onclick="confirm_go('清除所有退订记录','stat_opt.php?del=true&db=return');return false;" />
			</td>
			<td style="background-color: White; padding: 15px; border:1px solid #98bf21;" valign="top">
				<input value="下载数据库" type="button" onclick="window.open('down_data.php?downtype=statdb');return false;" />
				<input value="导出统计记录" type="button" onclick="window.open('stat_export.php?mid=-1');return false;" />
				<input value="导出退订记录" type="button" onclick="window.open('return_export.php?mid=[ALL]');return false;" /><br />
				<br />
				如果点击按钮无法下载数据库文件，请手动通过 FTP 下载 “return/data/#stat.db”。<br />
				数据库为 SQLite 格式，请通过SQLite3 工具打开。
			</td>
		</tr>
		<tr>
			<th>
				组件测试
			</th>
			<th>
				数据库工具
			</th>
		</tr>
		<tr>
			<td style="background-color: White; padding: 15px; border:1px solid #98bf21;" valign="top">
<?php 
				echo '<span class="opt_title">数据库连接：</span>';
				$cls_db = new class_database;	
				$cls_db->OpenConnect();
				if ( $cls_db->m_connect )
					echo "<div class=\"ok\">支持</div>";
				else
					echo "<div class=\"ft\">不支持</div>";
				echo '<span class="opt_title">数据库状态：</span>';
				$rtn = $cls_db->checkisreadonly();
				if ( $rtn <> false )
					echo "<div class=\"ft\">数据库文件只读，会造成不能记录数据！请修改文件＂#stat.db＂和所在目录的权限。</div>";
				else
					echo "<div class=\"ok\">支持</div>";
				
				$cls_db->CloseConnect();
?>
			</td>
			<td style="border:1px solid #98bf21; padding: 15px;" valign="top">
				6.0以前的老版本数据库，点此可以不丢失数据更新到新版本<br />
				<br />
			    <input value="数据库更新" type="button" onclick="alert_url('set_checkdb.php?howid=0');return false;" />
			    <input value="压缩数据库" type="button" onclick="alert_url('set_checkdb.php?howid=1');return false;" />
				<input value="重建数据库" type="button" onclick="confirm_input_go('此操作会清空所有数据 ！', 'set_checkdb.php?howid=2');return false;" />
			</td>
		</tr>
	</table>
	<div style="display:none;" id="msg"></div>
</body>
</html>
