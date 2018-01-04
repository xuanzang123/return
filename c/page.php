<?php
	function WritePageSelection( $aurl, $amax, $acur ){
		global $page;
		//获取页面参数
		$pageparas = get_urlparas("page");
		$pageparas = $pageparas.$aurl;
?>
<form action="<?php echo $pageparas ?>" method="post" id="form1" name="form1">
<div class="pagew" style="margin:auto; display:block; margin-top:10px;">
	共 <font color="red"><?php echo $amax ?></font> 页 第 <font color="red"><?php echo $acur ?></font> 页 直接转到第
	<input type="text" name="page" style="width: 30px;" value="<?php echo $acur ?>">
	页
	<input type="submit" value="GO" style="width: 40" id="submit1" name="submit1">
	选择页码：
<?php 
	
		if ( $amax>0 ) {
			echo "<a href=\"$pageparas&page=1\"><font face=\"webdings\">9</font></a>";
		} 
		echo '<b>';
		$min=$page-5;
		$max=$page+5;
		if ($min<1 )
			$min=1;
		
		if ( $max>$amax )
			$max = $amax;
		
		for ( $i=$min; $i<=$max; $i++){
			if ($page==$i)
				echo $i;
			else
				echo "<a href=\"$pageparas&page=$i \">$i</a>";
		
			echo " ";
		}
		echo '</b>';
		
		if ( $amax>0 )
			echo "<a href=\"$pageparas&page=$amax\"><font face=\"webdings\">:</font></a>";
		
		 echo " ";
		 
		 if ( $acur-1>=1 ) { 
			$p =  $acur-1;
			echo "[<a href=\"$pageparas&page=$p\">上一页</a>]";
		 }else{ 
			echo '[上一页]';
		 }
		if ( $acur+1<=$amax ) {
			$p =  $acur+1;
			echo "[<a href=\"$pageparas&page=$p\">下一页</a>]";
		 }else{ 
			echo '[下一页]';
		 }
?>
</div>
</form>
<?php
	}
?>