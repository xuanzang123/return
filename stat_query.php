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
	
	$cls_db = new class_database;
	$cls_db->OpenConnect();
	
	$email = get_request( false, "q" );
	$qtype = get_request( false, "qtype");
	$mailid = get_request( false, "mid");
	
	if ( ! Is_Numeric( $qtype ) )
		$qtype = 0;
	
	$email = str_replace( "%", "" , $email);
	
	$sortsql = get_sort_paras( $sortby, $sc, "mail_id", "mail_id,email,ip,date" ) ;

	$db_column_len = 255;

	switch( $qtype ){
	case 1:
		$selstr = "select * from list,stat where stat.id=list.mailid and ip like ? ";
		$db_column_len = 50;
		break;
	default:
		$selstr = "select * from list,stat where stat.id=list.mailid and email like ? ";
		break;
	}
	
	if ( Is_Numeric( $mailid ) )
		$selstr .= "AND list.mailid=$mailid";
	
	$selstr .= $sortsql;

    $emaillike =  str_replace( "*", "%", $email );
	
	//'创建 Command 对象
	//' 参数
	$p = array ( $emaillike );
	
	// 设置页数
	$cls_db->get_page_data( $selstr, $p, $recordcount, $page, $pagesize, $pagecount, 2);
	// 添加 PAGE  信息到SQL语句
	$selstr = $cls_db->sql_appent_pageinfo( $selstr, $page, $pagesize );
	
	$cls_db->getrecords ( $selstr, $p, $db );
	
	writehead(1);
?>
	<div class="d_head_operline">
		<div style="width: 200px; float: left;">
			<b>操作：</b>
			<input type="button" value="首页" onclick="javascript:gourl('stat_view.php');">
			<input value="返回上一级" type="button" onclick="gopre();">
		</div>
		<div style="width: 450px; line-height: 20px; text-align: center; float: right;">
<?php
	echo "搜索结果：";
	if (  $qtype==1 )
		echo "IP";
	else 
		echo "Email";
	
	echo "为<font color=red>$email</font>的记录";
?>
		</div>
	</div>
	<br />
	<table class="pagew datagrid">
		<tr height="20" bgcolor="MintCream">
			<th class="datacell" style="width:80px">
				<?php echo write_sorthead("邮件标识","mail_id", $sc, $sortby); ?>
			</th>
			<th class="datacell" style="width:180px">
				<?php echo write_sorthead("邮件地址","email", $sc, $sortby); ?>
			</th>
			<th class="datacell" style="width:60px">
				随机码
			</th>
			<th class="datacell" style="width:110px">
				<? echo write_sorthead("IP","ip", $sc, $sortby); ?>
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
	if ( $recordcount >0 ){
		$num = 0;
		$cua=new CheckUserAgent;
		while ( $rs = $db->fetch() ){
			$num ++;
		
			$cua->execute( $rs["client"] );
?>
		<tr height="20"<?php if ( fmod( $num, 2) ==0 ) { echo ' class="alt" '; } ?>>
			<td class="datacell">
				<?php echo $rs["mail_id"]; ?>
			</td>
			<td class="datacell">
				<?php echo $rs["email"]; ?>
			</td>
			<td class="datacell">
				<?php echo $rs["randcode"]; ?>
			</td>
			<td class="datacell">
				<?php echo $rs["ip"]; ?>
			</td>
			<td class="datacell">
				<?php echo findArea($rs["ip"]); ?>
			</td>
			<td class="datacell">
				<?php echo $rs["date"]; ?>
			</td>
			<td class="datacell">
				<?php echo $cua->vos; ?>&nbsp;
			</td>
			<td class="datacell">
				<?php echo $cua->vsoft; ?>&nbsp;
			</td>
		</tr>
<?php
		}
	}
?>
	</table>
	<br />
<?php
	include "c/page.php";
	WritePageSelection ("", $pagecount, $page);
	$cls_db->CloseConnect();
?>
</body>
</html>