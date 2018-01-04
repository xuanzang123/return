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
	
	//' 读取网页参数
	$mailid = get_request( false, "mid" );
	$email = get_request( false, "q" );
	$how = get_request( true, "how");
	
	echo '<form action="" method="get" id="fm_query" name="fm_query">';
	writehead(2);
?>
	<div class="d_head_operline">
		<div style="width: 200px; float: left;">
			<b>操作：</b>
<?php
		if ( strlen($email)>0 or $mailid<>"" ){
			echo '<input value="返回" type="button" onclick="javascript:gourl(\'return_view.php\')">';
			echo ' ';
			echo '<input value="删除" type="button" onclick="javascript:fm_delsel_submit();">';
		}
		else
			echo '<input value="刷新" type="button" onclick="javascript:gourl( window.location );return false;">';
?>		
		</div>
<?php	
		if ( $email<>""){
			echo '<div style="overflow: hidden; float: right; line-height:20px;">';
			echo "搜索结果：Email为<font color=\"red\">$email</font>的记录";
			echo '</div>';
		}
		else {
			echo '<div style="overflow: hidden; float: left;">
				邮件地址&nbsp;<input type="text" name="q">&nbsp;<input type="submit" value="搜索"></div>
			<div style="width: 200px; text-align: center; line-height: 20px; float: right;">';
			if ( $mailid<>"" )
				echo "邮件标识：$mailid";
			else
				echo '<input value="退出" style="float:right" type="button" language="javascript" onclick="javascript:gourl(\'stat_login.php?logout=true\');return false;">';
			
			echo '</div>';	
		}
?>
	</div>
<?php
	echo '<br />';
	echo '</form>';

	$cls_return = new class_database;
	$cls_return->OpenConnect();

	if ( $how=="del" ){
		//' 执行删除
		$delidarray = explode( ",", get_request( true, "selid") );
		$n = 0;
		if ( count($delidarray)>0 ){
			$selstr = "where ( id=" . $delidarray[0];
			$n++;
		}
		for ( $i=1; $i<Count($delidarray); $i++)
		{
			if ( Is_Numeric( $delidarray[i] ) )
			{
				$selstr .= " or id=" . $delidarray[i];
				$n ++;
			}
		}
		$selstr .= " )";

		if ( $n > 0 ){
			try{
				$cls_return ->execsql("delete from return " . $selstr );
			}catch(Exception $e){
				echo "<center><H2>处理错误</H2><br />";
				echo $e->GetMessage() . "<br /><center/></body></html>";
				exit;
			}
		}
		else{
			echo "<center><h2>未选中任何记录</h2></center></body></html>";
			exit;
		}
	}
   //'显示类型
    $viewtype = 0;
    
    //' 排序
	$sortsql = get_sort_paras( $sortby, $sc, "email", "mail_id,email,n,lasttime" ); 
	$p = null;
	//' 如果Q参数有数据，则判定为查询
	if ( strlen($email)>0 ){
		$sortsql = get_sort_paras( $sortby, $sc, "email", "mail_id,email,n,lasttime" );
		$cmd = "select * from return where email=? " . $sortsql;
		$p = array($email);
		$viewtype = 2;
	}
	else{
		if ( $mailid == "" ){
			$sortsql = get_sort_paras( $sortby, $sc, "mail_id", "mail_id,n" ) ;
			$cmd = "select mail_id, n from (select mail_id, count(*) as n from return group by mail_id )" . $sortsql;
		}
		else{
			$sortsql = get_sort_paras( $sortby, $sc, "email", "mail_id,email,n,lasttime" );
			if ( $mailid == "[NULL]" ){
				$cmd = "select * from return where IsNull(mail_id)=true " . $sortsql;
			}
			else {
				$cmd = 'SELECT * FROM [return] WHERE [mail_id]=? ' . $sortsql;
				$p = array ( $mailid );
			}
			$viewtype = 1;
		}
	}

	//'  设置页数
	$cls_return->get_page_data( $cmd, $p, $recordcount, $page, $pagesize, $pagecount, 2);
	// 添加 PAGE  信息到SQL语句
	$cmd = $cls_return->sql_appent_pageinfo( $cmd, $page, $pagesize );
	$cls_return->getrecords( $cmd, $p, $rs );
	
	$num = 0;
	if ( $viewtype == 0 ){
		echo '<table align="center" class="pagew datagrid">
		<tr height="20" bgcolor="MintCream">
			<th align="center" width="200" class="datacell">';
		echo write_sorthead("邮件标识","mail_id", $sc, $sortby);
		echo '</th>
			<th align="center" width="100" class="datacell">';
		echo write_sorthead("地址数","n", $sc, $sortby);
		echo '</th>
			<th align="center" width="300" class="datacell">
				操作
			</th>
		</tr>';
		if ( $recordcount>0 ){
			$num = 0;
			while ( $a=$rs->fetch() )
			{
				$num++;	
				$sid = $a["mail_id"];	
				$rid = urlencode($sid);
				if ( is_null($rid) or $rid=="" )
					$rid = "[NULL]";
				if ( fmod( $num, 2 ) == 0 )
					echo '<tr class="alt">';
				else
					echo '<tr>';
				
				echo "<td class=\"datacell\">$sid</td>";
				echo "<td style=\"padding-right: 10px; text-align: right;\" class=\"datacell\">". $a["n"] .
				"</td>
				<td style=\"text-align: center;\" class=\"toolbar\">
					<a class=\"toolbt\" title=\"详细记录\" href=\"?mid=$rid\">详细记录</a>
					<a class=\"toolbt\" title=\"导出地址\" href=\"return_export.php?mid=$rid\">导出地址</a>
					<a class=\"toolbt\" title=\"删除此邮件标识所有记录\" href=\"\" onclick=\"confirm_go('删除此邮件标识的所有记录','stat_opt.php?db=return&del=true&mid=$rid');return false;\">删除</a>
				</td>
			</tr>";
			}
		}
	}
	if ( $viewtype == 1 or $viewtype==2 ){
		echo '
			<form action="" method="post" name="fm_delsel" language="javascript" onsubmit="return fm_delsel_onsubmit();">
			<input type="hidden" name="how" value="">
			<table align="center" class="pagew datagrid">
				<tr height="20" bgcolor="MintCream">
					<th align="center" width="40" class="datacell">
						<input type="checkbox" style="border-color: white" id="m_selall" language="javascript"
							onclick="return m_selall_onclick()">
					</th>';
					if ( $viewtype==2 ){
						echo'<th align="center" width="120" class="datacell">';
						echo write_sorthead("邮件地址", "email", $sc, $sortby );
						echo '</th>';
						echo '<th align="center" width="120" class="datacell">';
						echo write_sorthead("邮件标识", "mail_id", $sc, $sortby );
						echo '</th>';
					}
					else {
						echo '<th align="center" width="240" class="datacell">';
						echo write_sorthead("邮件地址", "email", $sc, $sortby );
						echo '</th>';
					}
					echo '<th align="center" width="80" class="datacell">';
					echo write_sorthead("退订次数", "n", $sc, $sortby );
					echo '</th>
					<th align="center" width="140" class="datacell">';
					echo write_sorthead("最后退订", "lasttime", $sc, $sortby );
					echo '</th>';
				echo '</tr>';

		if ( $recordcount > 0 ){
			$num = 0;
			while ( $a=$rs->fetch() )
			{
				$num++;	
				$rid = $a["mail_id"];
				if ( is_null($rid) or $rid=="" )
					$rid = "[NULL]";
				if ( fmod( $num, 2 ) == 0 )
					echo '<tr class="alt">';
				else
					echo '<tr>';
				
				echo '
						<td align="center" class="datacell">
							<input type="checkbox" style="border-color: white" id="selid' . $num . '" name="selid"
								value="';
				echo $a["id"] . '"></td>';
				echo "<td class=\"datacell\">" . $a["email"] . "</td>";
				
				if ( $viewtype==2 )
					echo '<td class="datacell">' . $rid . '</td>';
						
				echo '<td class="datacell">' . $a["n"] . '</td>';
				echo '<td class="datacell">' . $a["lasttime"] . '</td>';
				echo '</tr>';
			}
		}
	}
	
	echo '</table>';
	echo '</form>';
	
	include  "c/page.php";
	WritePageSelection ( "", $pagecount, $page );

?>
<script type="text/javascript">
function fm_delsel_onsubmit() {
	if ( window.confirm("你确定要删除选择的记录吗？") )
	{
	    fm_delsel.how.value="del";
	    fm_delsel.action=window.location;

		return true;
	}
	return false;
}

function fm_delsel_submit()
{
	if  ( fm_delsel_onsubmit() )
		fm_delsel.submit();
}

function m_selall_onclick() {
	var a = window.fm_delsel.m_selall.checked;
<?php
	for ( $i=1; $i<=$num; $i++ )
		echo "window.fm_delsel.selid$i.checked=a;\r\n";
	
?>
}
</script>
<?php 	$cls_return->CloseConnect(); ?>
<div style="display:none;" id="msg"></div>
</body>
</html>
