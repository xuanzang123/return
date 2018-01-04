<?php
/*' ============================================
' 返回IP信息
' ============================================*/
Function Look_Ip($ip){
	$area = convertip($ip, false);
	$area = iconv( 'gb2312', 'utf-8', $area );
	return $area;
}

Function get_ip_province($ip){
	$area = convertip($ip, false);
	$area = iconv( 'gb2312', 'utf-8', $area );
	
	//if ( ($a1!=false) || ($a2!=false) || ($a3!=false)  )
	return get_area_by_level($area, 2);
}

Function get_ip_country($ip){
	$area = convertip($ip, false);
	$area = iconv( 'gb2312', 'utf-8', $area );
	
	$a1 = strpos($area, '省');
	$a2 = strpos($area, '市');
	$a3 = strpos($area, '中国');
	
	//if ( ($a1!=false) || ($a2!=false) || ($a3!=false)  )
	return get_area_by_level($area, 1);
}

function get_area_by_level($area, $level){
	$p = '辽宁 吉林 黑龙江 河北 山西 陕西 甘肃 青海 山东 安徽 江苏 浙江 河南 湖北 湖南 江西 台湾 福建 云南 海南 四川 贵州 广东 内蒙古 新疆 广西 西藏 宁夏 北京 上海 天津 重庆 香港 澳门';
	$rtn = $area;
	$pn = explode(' ', $p);
	
	$b = false;
	for($x=0;$x<count($pn);$x++) {
		$city = $pn[$x];
		if ( is_int(strpos($area, $city )) ){
			// 国家
			if ($level == 1)
				$rtn = '中国';
			if ($level == 2)
				$rtn = $city;
			
			$b = true;
			break;
		}
	}
	
	// 如果是保留地址
	if ( is_int(stripos($area, '地址')) || 
		is_int(stripos($area, '局域网')) || 
		is_int(stripos($area, 'iana'))){
		$rtn = '其他';
		$b = true;
	}
	
	if ($level==2)
	{
		if ( $b==false)
			$rtn = '国外';
	}
	
	return $rtn;
}

function convertip($ip, $bdetail) {
    //IP数据文件路径
    $dat_path = 'qqwry.dat';
	
	$ipAddr1 = "";
	$ipAddr2 = "";

    //检查IP地址
    if(!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $ip)) {
        return 'IP Address Error';
    }
    //打开IP数据文件
    if(!$fd = @fopen($dat_path, 'rb')){
        return 'IP date file not exists or access denied';
    }

    //分解IP进行运算，得出整形数
    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

    //获取IP数据索引开始和结束位置
    $DataBegin = fread($fd, 4);
    $DataEnd = fread($fd, 4);
    $ipbegin = implode('', unpack('L', $DataBegin));
    if($ipbegin < 0) $ipbegin += pow(2, 32);
    $ipend = implode('', unpack('L', $DataEnd));
    if($ipend < 0) $ipend += pow(2, 32);
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
   
    $BeginNum = 0;
    $EndNum = $ipAllNum;

    //使用二分查找法从索引记录中搜索匹配的IP记录
	$ip1num = 0;
	$ip2num = 0;
    while($ip1num>$ipNum || $ip2num<$ipNum) {
        $Middle= intval(($EndNum + $BeginNum) / 2);

        //偏移指针到索引位置读取4个字节
        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if(strlen($ipData1) < 4) {
            fclose($fd);
            return 'System Error';
        }
        //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
        $ip1num = implode('', unpack('L', $ipData1));
        if($ip1num < 0) $ip1num += pow(2, 32);
       
        //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
        if($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }
       
        //取完上一个索引后取下一个索引
        $DataSeek = fread($fd, 3);
        if(strlen($DataSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if(strlen($ipData2) < 4) {
            fclose($fd);
            return 'System Error';
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if($ip2num < 0) $ip2num += pow(2, 32);

        //没找到提示未知
        if($ip2num < $ipNum) {
            if($Middle == $BeginNum) {
                fclose($fd);
                return 'Unknown';
            }
            $BeginNum = $Middle;
        }
    }

    //下面的代码读晕了，没读明白，有兴趣的慢慢读
    $ipFlag = fread($fd, 1);
    if($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if(strlen($ipSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }

    if($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if(strlen($AddrSeek) < 3) {
            fclose($fd);
            return 'System Error';
        }
        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }

        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr2 .= $char;

        $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
        fseek($fd, $AddrSeek);

        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;
            
    } else {
        fseek($fd, -1, SEEK_CUR);
        while(($char = fread($fd, 1)) != chr(0))
            $ipAddr1 .= $char;

        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while(($char = fread($fd, 1)) != chr(0)){
            $ipAddr2 .= $char;
        }
    }
    fclose($fd);

    //最后做相应的替换操作后返回结果
    if(preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }
	if ( $bdetail )
		$ipaddr = "$ipAddr1 $ipAddr2";
	else
		$ipaddr = "$ipAddr1";
	
    $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
    $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
    if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
        $ipaddr = 'Unknown';
    }

    return $ipaddr;
}
?>