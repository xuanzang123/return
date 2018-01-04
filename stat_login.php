<?php include "stat_inc.php"; ?>
<?php
	$blogin = false;
	$blogin = get_islogin();
	
	$outtext = "";
	$bshowloginform = false;
	
	//是否注销
	$blogout = false;
	$blogout = get_request( false, "logout" );
	if ( $blogout == true ) {
		$_SESSION["seid_stat_login"]="";
		setcookie( "seid_stat_login", "");
		setcookie( "user", "");
		setcookie( "pass", "");
		
		$outtext  .= "<script type=\"text/javascript\">window.onload=function(){window.alert(\"您已成功退出登录~\");}</script>";
		$blogin = false;
	}
	
	if (get_request( false, "howid" )==1){
		$outtext  .= "<script type=\"text/javascript\">window.onload=function(){window.alert(\"登录超时，请重新登录！\");}</script>";
	}
	
	if ( $blogin == false ){
		$user = "";
		$pass = "";
		
		$user = get_request( true, "user" );
		$pass = get_request( true, "pass" );
		
		if (strlen($user)==0 || strlen($pass)==0 ){
			$user = get_cookie("user");
			$pass = get_cookie("pass");
		}
		
		if  ($user==$cuser && md5($pass)==$cpass ){
			//创建 SEID 随机20位字母
			$seid = "";
			for ( $i=1; $i<=20; $i++)
			{
				$seid = $seid . chr(mt_rand(26, 26+97 ));
			}

			setcookie("seid_stat_login", $seid);
			$_SESSION["seid_stat_login"]=$seid;
			
			if ( get_request( true, "savepass" ) == "true" ){
				setcookie("user", $user, time()+7*24*3600);
				setcookie("pass", $pass, time()+7*24*3600);
			}
			
			header ("location: stat_view.php");
			exit;
		}else{
			// 显示登录输入窗口
			if ($user<>"" and $pass<>""){
				$outtext  .=  "<script type=\"text/javascript\">window.onload=function(){window.alert(\"用户名或密码错误，请重新输入！\");}</script>";
			}
			
			$bshowloginform = true;
		}
	}
	else{
	    header("location: stat_view.php" );
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $g_headertext; ?>
</head>
<body>
<?php
	include "inc_title.php";

	echo $outtext;
	if ( $bshowloginform )
			WriteLoginForm();

	function WriteLoginForm()
	{
		echo '<div style="border-collapse: collapse; border: solid 1px gray; border-radius: 5px; margin: auto; width: 350px; margin-top: 20px;">
		<div style="background-color: gold; height: 30px; line-height: 30px; vertical-align: middle; font-size: 14px; font-weight: bold; text-align: center; border-radius: 5px 5px 0px 0px;">输入用户名、密码验证管理身份</div>
		<div style="padding: 10px;">
		<style type="text/css">
			.lgfm
			{
				line-height: 160%;
				padding-left: 10px;
			}
			.lgfm span
			{
				width: 70px;
				float: left;
			}
			.lgfm li
			{
				height: 30px;
			}
		</style>
		<form action="stat_login.php" method="POST" id="form1" name="form1">
		<div class="lgfm">
			<ul style="list-style: none;">
				<li><span>用户名：</span><input type="text" name="user" style="width: 160px;" /></li>
				<li><span>密码：</span><input type="password" name="pass" style="width: 160px;" /></li>
				<li><span>自动登录</span><input type="checkbox" name="savepass" value="true" style="" /></li>
				<li><span>&nbsp;</span><input type="submit" value="登录" style="width: 80px" /></li>
			</ul>
		</div>
		</form>
		</div>
		</div>';
	}
?>
</body>
</html>