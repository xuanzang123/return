<?php
	function writehead( $headtype ){
		echo '<div class="pagew" style="margin:auto; display:block; height:25px; z-index:100;">';
		$ba;
		if ( $headtype==1 ) { $ba=false; } else { $ba=true; }
		writehead_sub("统计管理", "stat_view.php", $ba);
		if ( $headtype==2 ) { $ba=false; } else { $ba=true; }
		writehead_sub("退订管理", "return_view.php", $ba);
		if ( $headtype==3 ) { $ba=false; } else { $ba=true; }
		writehead_sub("系统设置", "options.php", $ba);
		echo "</div>";
	}
	
	function writehead_sub( $name, $alink, $ba ){
		if ( $ba==true )
			echo '<div class="border_nosel"><a href="' . $alink . '">' . $name . '</a></div>';
		
		if ( $ba==false )
			echo '<div class="border_cusel">' . $name . '</div>';
	}
?>