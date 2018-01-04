<?php
	$blogin;
	$blogin = get_islogin();
	if ( !$blogin ){
		header( "location: stat_login.php?howid=1");
		exit();
	}
?>