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
	
	$mail_id = get_request( false, "mid" );
	if ( ! Is_Numeric( $mail_id ) ){
		exit ( "mail_id 参数错误" );
	}
	
	//开启数据库
	$cls_db = new class_database;	
	$cls_db->OpenConnect();
	
	//' 统计依据
	//' 0 按日期统计
	//' 1 按小时统计
	$tjby = get_request( false, "tjby");
	if (! Is_Numeric( $tjby ) )
		$tjby = 0;
	else
		if ( $tjby >1 or $tjby<0 )
			$tjby = 0;
	
	// 邮件标识
	$selstr = "select * from stat where id=" . $mail_id;
	$mail_idname = $cls_db->get_a_value( $selstr, "mail_id" );

	if ( $tjby == 1 ) 
		$selstr = "SELECT max(times) as maxtimes FROM(SELECT count(datestr) as times, datestr FROM (SELECT mailid, strftime('%H', date) as datestr FROM list WHERE mailid=$mail_id) GROUP BY datestr);";
	else
		$selstr = "SELECT max(times) as maxtimes FROM(SELECT count(datestr) as times, datestr FROM (SELECT mailid, strftime('%Y%m%d', date) as datestr FROM list WHERE mailid=$mail_id ) GROUP BY datestr);" ;
	
	$maxtimes = $cls_db->get_a_value($selstr, "maxtimes");

	$fromstr = "";
	if ( $tjby == 1 ){
		$fromstr = "FROM (SELECT mailid, strftime('%H', date) as datestr FROM list WHERE mailid=$mail_id) GROUP BY datestr ORDER BY datestr";
		$selstr = "SELECT count(datestr) as times, datestr " . $fromstr;
	}
	else{
		$fromstr = "FROM (SELECT mailid, strftime('%Y%m%d', date) as datestr FROM list WHERE mailid=$mail_id) GROUP BY datestr ORDER BY datestr";
		$selstr = "SELECT count(datestr) as times, datestr " . $fromstr;
		// 分页
	}
	
	//分页信息，只为获取$recordcount
	$cls_db->get_page_data( $selstr, null, $recordcount, $page, $pagesize, $pagecount, 2);
	// 按日期时，才需要真正的分页
	if ( $tjby <> 1){
		// 添加 PAGE  信息到SQL语句
		$selstr = $cls_db->sql_appent_pageinfo( $selstr, $page, $pagesize );
	}

	
	writehead (1 );
?>
	<div class="d_head_operline">
		<div style="width: 200px; float: left;">
			<b>操作：</b>
			<input type="button" value="首页" onclick="javascript:gourl('stat_view.php');" id="button4"
				name="button2">
			<input value="返回" type="button" onclick="gopre();">
		</div>
		<div style="width: 200px; line-height: 20px; text-align: center; float: right;">
			邮件标识：<?php echo $mail_idname; ?>
		</div>
	</div>
	<br />
<?php
	$cls_db->getrecords_txt( $selstr, $rs );
			
	echo '<table align="center" class="pagew datagrid">';
	echo '<tr><th style="width:600px;" class="datacell"></th>';
	echo '<tr><td><div id="chartPie" style="width: 100%; height: 500px; float: left"></div></td></tr></table>';
	
	// 图表设置
	$chart_title;
	$chart_type;
	$chart_x_title;
	if ( $tjby == 0 ){
		$chart_title = "日期统计图";
		$chart_type = "bar";
		$chart_x_title = "日期";
	}
	if ( $tjby == 1 ){
		$chart_title = "时间统计图";
		$chart_type = "line";
		$chart_x_title = "时间（0:00-23:00）";
	}
	
	
	$cdata = array();
	if ($recordcount>0 ){
		$n = 0;
		$c;
		$x;
		
		while ($a=$rs->fetch() ){			
			if ( $tjby == 1 ){
				$d=$a["datestr"];

				for ( $x = $n; $x<=$d; $x++ ){
					
					if ( $x == $d )
						$c = $a["times"];
					else
						$c = 0;			
					$n = $x + 1;
					$cdata[$x] = $c;
				}
				for ( $x=$n; $x<=23; $x ++ ){
					$c = 0;
					$cdata[ $x ] = $c;
				}
			}
			if ( $tjby == 0 ){
				$d=$a["datestr"];
				$c = $a["times"];
				$cdata[ $d] = $c;
			}							
		}
		
		 //' 输出
		$i = 0;
		$percentageStr = '';
		$chart_xaxis = '';
	    foreach ( $cdata as $key => $value ) {
	        $percentageStr .= "['" . $key . "'," . $value . "]";
	        $chart_xaxis .= "'" . $key . "'";
	        if ( $i <> count($cdata) -1 ){
				$percentageStr = $percentageStr . ",";
				$chart_xaxis .= ",";
	        }
	        
			$i ++;
	    }
	}
		
	// 输出 Chart
?>
	<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/expoting.js"></script>
	<script type="text/javascript">
		var chart;
		$(function() {
			var data =[<?php echo $percentageStr ?>];

			var piechart = new Highcharts.Chart({
					chart: {
						renderTo: 'chartPie',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: '<?php echo $chart_type ?>'
					},
					title: {
						text: '<?php echo $chart_title ?>',
					},
					xAxis: {
						categories: [<?php echo $chart_xaxis ?>],
						title: {
							text: '<?php echo $chart_x_title ?>'
						}
					},
					yAxis: {
						title: {
							text: '查看次数'
						}
					},
					tooltip: {
						formatter: function() {
							return '<b>' + this.point.name +'</b>:' + this.point.y;
						}
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						bar: {
							allowPointSelect: true,
							cursor: 'pointer',
							shadow: false,
							dataLabels: {
								enabled: true
							}
						},
						line: {
							allowPointSelect: true,
							shadow: false,
							cursor: 'pointer',
							dataLabels: {
								enabled: true
							}
						}
					},
					series: [{
						type: '<?php echo $chart_type ?>',
						name: '<?php echo $chart_title ?>',
						data: data,
						showtitle: false
					}]
				});
		})
	</script>
<?php
	if ( $tjby == 0 ){
		include "c/page.php";
		WritePageSelection("", $pagecount, $page);
	}
	
	$cls_db->CloseConnect();
?>
</body>
</html>