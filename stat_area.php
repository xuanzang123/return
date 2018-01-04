<?php
	include "stat_inc.php";
	include "inc_checklogin.php";
	include "c/agent.php";
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
	
	$mail_id = get_request( false, "mid" );
	if ( ! Is_Numeric( $mail_id ) ){
		exit ( "mail_id 参数错误" );
	}
	
	$showtype = get_request( true, "showtype");
	$noversion = get_request( true, "noversion");
	if ( $noversion <> "1" )
		$noversion = 0;
	
	//开启数据库
	$cls_db = new class_database;	
	$cls_db->OpenConnect();
	// 邮件标识
	$selstr = "select * from stat where id=" . $mail_id;
	$mail_idname = $cls_db->get_a_value( $selstr, "mail_id" );

	$vname;
	$charttitle;
	if ( $showtype=="os"){
		$vname = "client";
		$charttitle = "客户端系统统计";
	}
	elseif ( $showtype=="client" ){
		$charttitle = "客户端软件统计";
		$vname = "client";
	}
	elseif ( $showtype == "country" ){
		$charttitle = "国家分布统计";
		$vname = "ip";
	}
	else{
		$showtype = "area";
		$charttitle = "地区分布统计";
		$vname = "ip";
	}

	$selstr = "select " . $vname . " from list where mailid=" . $mail_id ;
	$cls_db->getrecords_txt($selstr, $rs);
	
	// 数据量
	$cls_db->get_page_data( $selstr, null, $recordcount, $page, $pagesize, $pagecount, 1);
	
	writehead (1);
	echo '
	<div class="d_head_operline">
		<div style="width: 200px; float: left;">
			<b>操作：</b>
			<input type="button" value="首页" onclick="javascript:gourl(\'stat_view.php\');">
			<input value="返回" type="button" onclick="javascript:gourl(\'stat_view.php\');">
			</td>
		</div>';
	echo '<div style="width: 200px; line-height: 20px; text-align: center; float: right;">邮件标识：' . $mail_idname . '</div>';
	echo '
	</div>
	<br />
	<table align="center" class="pagew datagrid">
		<tr height="20" bgcolor="MintCream">
			<th class="datacell">
				<form action="" method="post">';
					echo '<input type="radio" value="country" name="showtype" ';
					if ( $showtype=="country" ) echo "checked";
					echo ' />国家';
					echo '<input type="radio" value="area" name="showtype" ';
						if ( $showtype=="area" ) echo "checked";
					echo ' />地区';
					echo '<input type="radio" value="os" name="showtype" ';
						if ( $showtype=="os" ) echo "checked";
					echo ' />系统';
					echo '<input type="radio" value="client" name="showtype" ';
						if ( $showtype=="client" ) echo "checked"; 
					echo ' />客户端';
					echo '<input type="checkbox" name="noversion" value="1" ';
						if ( $noversion==1 ) echo "checked";
					echo ' />不区分版本号';
					//echo '&nbsp;';
					echo '<input type=submit value="查询" />	
				</form>
			</th>
		</tr>
		<tr>
			<td>
				<div id="chartPie" style="width: 100%; height:500px; float: left">
				</div>
			</td>
		</tr>
	</table>';
	
	$percentageStr="";
	if ( $recordcount > 0 ){
		
		$num = 0;
		$d = array();
        
		$cua = new checkuseragent;
		
		while ( $a = $rs->fetch() ) {
			$num ++;
		    
		    if ( $showtype == "os" ){
				$cua->execute ( $a["client"], $noversion );
				$v = $cua->vos;
			}
			elseif ( $showtype == "client" ){
				$cua->execute ( $a["client"], $noversion );
				$v = $cua->vsoft;
			}
			elseif ( $showtype == "country" ){
				$v = get_ip_country( $a["ip"] );
			}
			else {
				$v = get_ip_province( $a["ip"] );
		    }
			$v = trim($v);
			
			if  ( isset( $d[$v] ) )
				$d[$v] = $d[$v] + 1;
			else
				$d[$v] = 1;
		}
		
		// 排序
		array_multisort( $d );
		
	    //' 输出
		$i = 0;
	    foreach ( $d as $key => $value ) {
	        $percentageStr .= "['" . $key . "'," . $value . "]";
	        if ( $i <> count($d) )
				$percentageStr = $percentageStr . ",";
	        
			$i ++;
	    }
	}
	
	$cls_db->CloseConnect();
?>
	<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/expoting.js"></script>
	<script type="text/javascript">
		var chart;
		$(function() {
			var data = [<?php echo $percentageStr; ?>];

			var piechart = new Highcharts.Chart({
					chart: {
						renderTo: 'chartPie',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: '<?php echo $charttitle; ?>'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name + ':</b> [' + this.point.y +'] '+ Math.round(this.percentage*100)/100 +' %';
						}
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							shadow: false,
							cursor: 'pointer',
							dataLabels: {
								enabled: true
							},
							showInLegend: false
						}
					},
					series: [{
						type: 'pie',
						name: '<?php echo $charttitle; ?>',
						data: data
					}]
				});
		})
	</script>
	</body>
</html>