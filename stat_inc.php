<?php session_start(); ?>
<?php include "c/functions.php"; ?>
<?php include "c/db.php"; ?>
<?php
	//用户名、密码设置， 可以修改下面的用户名、密码
	$cuser = "";
	$cpass = "";
?>
<?php include "data/#userpass.php"; ?>
<?php
	$g_headertext;
	$g_headertext = "<meta http-equiv=\"Content-Language\" content=\"zh-cn\">\r\n";
	$g_headertext .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n";
	$g_headertext .= "<link href=\"css/style.css\" rel=\"Stylesheet\" type=\"text/css\" />\r\n";
	$g_headertext .= "<title>邮件统计退订管理</title>\r\n";
	$g_headertext .= "<script type=\"text/javascript\" src=\"js/main.js\"></script>\r\n";
	$g_headertext .= "<script type=\"text/javascript\" src=\"js/jquery-1.8.3.min.js\"></script>\r\n";
?>
