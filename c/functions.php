<?php include "char.php";?>
<?php
	include "ip.php";
	
	date_default_timezone_set("Asia/Shanghai"); 
	//' ********************************************************
	//'                自 定 义 函 数 和 子 程 序
	//' ********************************************************

	//' 找到IP地址对应的地区
	function findArea($vIP){
		If (is_file("qqwry.dat" ) == false ){
			return '<span class=\"agent\" title=\"请检查QQWry.dat文件\">无IP库</span>';
		}    
		return Look_Ip( $vIP );
	}
	
	//' 读取页面参数，除了 nottag, Notag 可以包含多个参数，以逗号分割
	function get_urlparas( $nottag ){
		//' 获取页面参数
		$pageparas = "";
		$i;
		$pname;
		$wps_num = 0;
		$notags = preg_split( "/,/", $nottag );

		$bnot;
		$keys = array_keys($_GET);
		for ( $i = 0; $i<count($keys); $i++ ){
			$pname = $keys[$i];
			$bnot = false;
			
			$tag;
			foreach ( $notags as $tag ){
				if ( strcmp( $pname, $tag ) == 0 ){
					$bnot = true;
					break;
				}
			}
			
			if ( $bnot == false ){
				if ( $wps_num>0 ){
					$pageparas = $pageparas . "&";
				}
				$pageparas = $pageparas . $pname;
				$pageparas = $pageparas . "=";
				$pageparas = $pageparas . $_GET[$pname];
				
				$wps_num++;
			}
		}
		
		if ( strlen($pageparas)==0){
			$pageparas = "a=a";
		}
		
		$pageparas = "?" . $pageparas;
		
		return $pageparas;
	}
	
	function write_sorturl( $name, $bcur, $asc ){
		$rtn;
		$rtn = get_urlparas( "sortby,sc" );
		$rtn = $rtn . "&sortby=" . $name;
		$rtn = $rtn . write_sortpara($bcur, $asc);
		return $rtn;
	}
	
	function write_sortsign( $bcur ){
		global $sc;
		if ( $bcur ){
			if ( $sc == "DESC" ){
				$rtn = "<img src=\"images/down.png\" />";
			}else{
				$rtn = "<img src=\"images/up.png\" />";
				//rtn = "<span style=""font-family: Webdings"">5<span>"
			}
		}else{
			$rtn = "<img src=\"images/updown.png\" />";
		}
		return $rtn;
	}
	
	function write_sortpara( $bcur, $asc ){
		$rtn;
		if ( $bcur ){
			if ( $asc == "DESC" ){
				$rtn = "&sc=ASC";
			}else{
				$rtn = "&sc=DESC";
			}
		}else {
			$rtn = "&sc=ASC";
		}
		
		return $rtn;
	}
	
	function write_sorthead( $colname, $dataname, $asc, $curname ){
		$bcur = false;
		if ( strcmp( $dataname, $curname ) == 0 ){
			$bcur = true;
		}
		
		$rtn = "<a href=\"";
		$rtn = $rtn . write_sorturl( $dataname, $bcur, $asc );
		$rtn = $rtn . "\">" . $colname . "</a>";
		$rtn = $rtn . write_sortsign( $bcur );
		return $rtn;
	}
	
	function get_sort_paras( &$sortby, &$sc, $deftag, $tags ){
		return get_sort_paras_defsc( $sortby, $sc, $deftag, $tags, "ASC" );
	}
	
	function get_sort_paras_defsc( &$sortby, &$sc, $deftag, $tags, $defsc ){
		$sortby = get_request( false, "sortby");
		if ( $sortby == "" ){
			$sortby = $deftag;
		}else{
			$array_tags;
			$array_tags = preg_split( "/,/", $tags );
			
			$tag;
			$bok = false;
			foreach ( $array_tags as $tag ){
				if ( strcmp( $sortby, $tag ) == 0 ){
					$bok = true;
					break;
				}
			}
			
			if ( !$bok )
				$sortby = $deftag;	
		}
		
		$sc = get_request( false, "sc");
		if ( $sc<>"ASC" and $sc<>"DESC" )
			$sc = $defsc;
		
		return " order by [" . $sortby . "] " . $sc;
	}
	
	function get_request( $bform, $name ){
		$rtntext = "";
		if ( $bform ){
			if  ( isset( $_POST[$name] ) )
				$rtntext = $_POST[$name];
		}else{
			if  ( isset( $_GET[$name] ) )
				$rtntext = $_GET[$name];
		}
		$rtntext = trim($rtntext);
		
		return $rtntext;
	}
	
	function get_cookie($name)
	{
		if  ( isset( $_COOKIE[$name] ) )
				return $_COOKIE[$name];
		return "";
	}
	
	function get_isadd( $amail_id ){
		$cls_db = new class_database;
		$cls_db->OpenConnect();
		
		$rs;
		
		$cls_db->getrecords_txt ( "select * from setup", $db );
		$rs = $db->fetch();
		
		//读取黑白名单设置
		$list_white;
		$list_black;
		$b_useblack;
		$b_usewhite;
		$blacks;
		$whites;
		
		$b_useblack = $rs["buseblack"];
		$b_usewhite = $rs["busewhite"];
		$whites =$rs["whitelist"];
		$blacks = $rs["blacklist"];
		
		if ( is_null($whites)==false )
			$list_white = explode( "\r\n", $whites );
		
		if ( is_null( $blacks)==false )
			$list_black = explode( "\r\n", $blacks );
		
		$cls_db->CloseConnect();
		
		$bfind = false;
		$badd = false;
		
		//检查白名单
		if ( $b_usewhite ){
			$badd = false;
			for ( $i=0; $i<Count($list_white); $i ++ ){
				if ( StrCmp( $amail_id, $list_white[$i])==0){
					$bfind = true;
					break;
				}
			}
			if ( $bfind )
				$badd = true;
		}else{
			//检查黑名单
			$badd=true;
			if ( $b_useblack ){
				for ( $i=0; $i<count($list_black); $i++ ){
					if ( StrCmp( $amail_id, $list_black[i] )==0){
						$bfind = true;
						break;
					}
				}
				if ( $bfind)
					$badd = false;
				
			}
		}
		
		return $badd;
	}
	
	function WriteUTF8ROM(){
		echo chr( 0xEF ) . chr(0xBB) . chr(0xBF);
	}
	
	function pub_getstatparas( &$amailid, &$aemail, &$arandcode ){
		$p = get_request( false, "p" );
		
		if  (strlen($p)>0 ){
			//解密 BASE64
			$p = base64_decode( $p );
			$paras = explode( "?", $p );
			$npara = count( $paras )-1;
			
			$arandcode=0;
			if ( $npara >= 1 ){
				$amailid = $paras[0];
				$aemail = $paras[1];
				if ( $npara >= 2 ){
					$arandcode = $paras[2];
				}
			}
			
			$amailid = URLDecode( $amailid );

			if ( ! is_numeric( $arandcode ) )
				$arandcode = 0;
			
			//防止标识超过长度限制
			if ( strLen( $amailid)>20 )
				$amailid = substr( $amailid, 0, 20 );
		}
	}
	
	function get_islogin(){
		//检查是否已经登录
		$seid1;
		$seid2;
		try{
			if ( !isset($_COOKIE["seid_stat_login"] ) )
				return false;
			
			$seid1 = $_COOKIE["seid_stat_login"];
			$seid2 = $_SESSION["seid_stat_login"];
		}
		catch( Exception $e)
		{
			return false;
		}
		
		$blogin;
		if ( $seid1<>"" and $seid1 == $seid2 )
			$blogin=True;
		else{
			$blogin=false;
			
			if ( isset($_COOKIE["user"]) and isset($_COOKIE["pass"]) ){
				if ( $_COOKIE["user"]==$cuser and md5($_COOKIE["pass"])==$cpass )
					$blogin=true;	
			}
		}
		
		return $blogin;
	}
?>