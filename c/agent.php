<?php
class checkuseragent {
    public $vos;
    public $vsoft;
    
    public function execute($text, $bnover=true) {
    	$this->vsoft = $this->get_broswer($text, $bnover);
    	$this->vos = $this->get_os($text, $bnover);
    }
    
    function get_broswer($sys, $bnover) {
    	$exp[0] = '';
    	$exp[1] = '';
    	
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1]; //获取火狐浏览器的版本号
            
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1]; //获取IE的版本号
            
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif (stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1]; //获取google chrome的版本号
            
        } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        } elseif (stripos($sys, "Safari") > 0) {
            preg_match("/Safari\/([\d\.]+)/", $sys, $safari);
            $exp[0] = "Safari";
            $exp[1] = $safari[1]; //获取苹果
        } else if (stripos($sys, 'AppleWebKit') > 0 ){
        	$exp[0] = "AppleWebKit";
        } else if (stripos($sys, 'Android') > 0 ){
        		$exp[0] = "Android";
        } else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        if ( $bnover )
        	return $exp[0];
        else{
        	if ( strlen($exp[1])>0 )
        		return $exp[0] . '(' . $exp[1] . ')';
        	else 		
        		return $exp[0];
        }
    }
    function get_os($agent, $bnover) {
        $os = false;
        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
		} else if (preg_match('/win/i', $agent) && preg_match('/nt 6.3/i', $agent)) {
            $os = 'Windows 8.1';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10'; 
		} else if (preg_match('/win/i', $agent) && preg_match('/nt 5.2/i', $agent)) {
            $os = 'Windows 2003';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        } else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        } else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
            $os = 'Macintosh';
        } else if (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        } else if (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        } else if (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        } else if (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        } else if (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        } else if (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        } else if (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        } else if (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        } else if (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        } else if (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        } else if (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        } else if (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        } else if (preg_match('/ipad;/i', $agent)) {
            $os = 'iPad';
        } else if (preg_match('/iphone/i', $agent)) {
            $os = 'iPhone';
        } else if (preg_match('/Mac OS X ([\d_]+)/i', $agent, $m)) {
            $os = 'Mac OS X';
        } else if (preg_match('/android;/i', $agent)) {
            	$os = 'Android';
        } else {
            $os = '未知操作系统';
        }
        
        // 合并 Windows
        if ( $bnover ){
        	if ( is_int(stripos($os, 'windows')) )
        		$os = 'Windows';
        }
      
        return $os;
    }
}
?>