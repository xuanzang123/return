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
	<h3>综合设置</h3>
<?php
	$cls_db = new class_database;
	$cls_db->OpenConnect();

	$how=get_request( false, "how");
	if ( $how=="save" ){
		$mainpgsize= get_request( true,"mainpgsize");
		$listpgsize=get_request( true,"listpgsize");
		
		$editpass = get_request( true,"editpass");
		$username = get_request( true,"username") ;
		$password = get_request( true,"password1");
		
		$bcheckok=true;
		if ( $editpass=="on" ){
			if ( strLen( $username )<1 ){
				echo "新用户名不能为空";
				$bcheckok=false;
			}
			if ( strLen( $password )<5 ){
				echo "新密码长度不能小于 5 个字符";
				$bcheckok=false;
			}
		}
		
		if ( $mainpgsize=="" or $listpgsize=="" or !Is_Numeric($mainpgsize) or !Is_Numeric($listpgsize) ){
			echo "请填写完整，并且每页显示数量必须填写数字"	;
			$bcheckok=false;
		}
		
		if ( $bcheckok ){
			if ( ($mainpgsize<1 or $mainpgsize>1000) or ( $listpgsize<1 or $listpgsize>1000) ){
				echo "请设置1～1000的每页显示数量。页面数量过大会造成显示速度变化，加重服务器负担。";
				$bcheckok=false;
			}
		}

		if ( $bcheckok ){
			$cmd = "UPDATE SETUP SET mainpgsize=?, listpgsize=?";
			$db = $cls_db->m_connect->prepare($cmd);
			$n = $db->execute( array( $mainpgsize, $listpgsize ) );
			
			if ( $n<=0 )
				echo "<h3>设置保存失败!</h3>";
			else
				echo "<h3>设置保存完毕!</h3>";
			
			//'修改密码
			if ( $editpass=="on" ){
				$a = fopen("data/#userpass.php", "w");
				
				if ( !$a )
					echo "<h3>帐户信息保存失败</h3>";
				else{
					$bok = true;
					try{
						fwrite( $a, "<?php\r\n" );
						fwrite( $a, "//登录用户名密码\r\n" );
						fwrite( $a, '$cuser' . "=\"$username\";\r\n" );
						fwrite( $a, '$cpass' . "=\"" . md5($password) . "\";\r\n" );
						fwrite( $a, "?>");
						fclose( $a);
					}catch( Exception $e ){
						echo "<h3>帐户信息保存失败：" . $e->getMessage() . "</h3>";
						$bok = false;
					}
					if ( $bok )
						echo "<h3>帐户信息保存完毕。</h3>";
				}
			}
		}
	}
	//else{
		$cls_db->getrecords_txt ( "SELECT * FROM setup", $db );
		$rs = $db->fetch();
?>
	<script type="text/javascript">
<!--

function fm_other_onsubmit() {
	if ( fm_other.editpass.checked==true )
	{
		if ( fm_other.username.value.length<=0 )
		{
			window.alert("新用户名不能为空！" );
			return false;
		}
		if ( fm_other.password1.value.length< 5 )
		{
			window.alert("密码长度不能小于 5 个字符！" );
			return false;
		}
		
		if ( fm_other.password1.value != fm_other.password2.value )
		{
			window.alert("两次输入的密码不一致！" );
			return false;
		}
	}
}

//-->
	</script>

	<form method="post" action="?how=save" name="fm_other" onsubmit="javascript:return fm_other_onsubmit()">
	邮件标识列表每页显示数量：<input type="text" name="mainpgsize" style="width: 60px;" value="<?php echo $cls_db->getpagesize(1); ?>"><br />
	其他统计列表每页显示数量：<input type="text" name="listpgsize" style="width: 60px;" value="<?php echo $cls_db->getpagesize(2); ?>"><br />
	<br />
	<div style="border: 1px #ccddff solid; background: #eef4ff;">
		<div style="margin: 4px;">
			新的用户名：<input type="text" name="username" value="<?php echo $cuser ?>"><br />
			输入新密码：<input type="password" name="password1"><br />
			新密码确认：<input type="password" name="password2"><br />
			<br />
			<input type="checkbox" name="editpass" value="on" style="border: 0px white none"
				id="m_editpass">
			<label for="m_editpass">
				保存用户名、密码</label><br />
		</div>
	</div>
	<br />
	<input type="submit" value="保存设置"><input type="reset" value="恢复">
	</form>
<?php
	//}
	$cls_db->CloseConnect();
?>
</body>
</html>
