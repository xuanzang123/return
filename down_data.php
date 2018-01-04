<?php
	include "stat_inc.php";
	include "inc_checklogin.php";

	header("Content-Disposition: attachment; filename=database.db");
	header("ContentType:application/octet-stream");
	
	readfile( 'data/#stat.db' );
?>
