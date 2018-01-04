<?php include "inc_conn.php"; ?>
<?php
Class class_database{
    Public $m_connect = null;
    
    Public Function OpenConnect(){
		try{
			global $pub_dbpath;
			$this->m_connect = new PDO('sqlite:' . $pub_dbpath);
			if ( $this->m_connect  )
				$this->m_isconnect = True;
		}catch( PDOException  $e)
		{
			echo $e;
			exit();
		}
    }
	
	function create_new_db()
	{
		$tmpdb = "data/#tmp.db";
		
		$a = new PDO("sqlite:$tmpdb" );
		$a = null;
		
		if ( ! file_exists( $tmpdb ) )
		{
			echo "创建失败！";
			return;
		}
		
		global $pub_dbpath;
		if ( ! copy( $tmpdb, $pub_dbpath ) )
		{
			echo "创建失败！";
			return;
		}
			
		// 打开数据库
		$this->OpenConnect();
		
		//创建表
		$sql = 'CREATE TABLE [list] (
	[id] integer NOT NULL PRIMARY KEY AUTOINCREMENT, 
	[mailid] long DEFAULT 0, 
	[ip] varchar(50), 
	[client] longtext, 
	[email] varchar(255), 
	[date] datetime, 
	[randcode] long
);
CREATE INDEX [mailid]
	ON [list] ([mailid]);
	
CREATE TABLE [return] (
	[id] integer NOT NULL PRIMARY KEY AUTOINCREMENT, 
	[email] varchar(255), 
	[n] long DEFAULT 0, 
	[lasttime] datetime, 
	[mail_id] varchar(50)
);
CREATE INDEX [return_mail_id]
	ON [return] ([mail_id]);
CREATE INDEX [email]
	ON [return] ([email]);	

CREATE TABLE [setup] (
	[id] integer NOT NULL DEFAULT 0 PRIMARY KEY AUTOINCREMENT, 
	[buseblack] bit NOT NULL, 
	[busewhite] bit NOT NULL, 
	[blacklist] longtext, 
	[whitelist] longtext, 
	[mainpgsize] short DEFAULT 0, 
	[listpgsize] short DEFAULT 0
);
CREATE INDEX [setup_id]
	ON [setup] ([id]);
	
CREATE TABLE [stat] (
	[id] integer NOT NULL PRIMARY KEY AUTOINCREMENT, 
	[mail_id] varchar(50), 
	[mail_read] long DEFAULT 0
);
CREATE UNIQUE INDEX [mail_id]
	ON [stat] ([mail_id]);
CREATE INDEX [id]
	ON [stat] ([id]);
	
INSERT INTO setup (buseblack, busewhite, blacklist, whitelist, mainpgsize, listpgsize)
VALUES(0, 0, \'\', \'\', 10, 10 ); 
';
		$this->m_connect->exec( $sql );
		
		echo  "创建完成！";
		
		unlink( $tmpdb );
	}
    
    Public Function CloseConnect(){
        $this->m_connect = null;
    }
    
    Public Function OutputList( $amailid ){
        If ( $amailid == "" )
            $amailid = "[ALL]";
        
		header( "content-type:text/plain");
		
        switch( $amailid ){
            case "[NULL]":
				$this->getrecords_txt ( "select email from return where ifnull(mail_id, 1)=1 or mail_id='';", $m_rs );
				break;
            Case "[ALL]":
				$this->getrecords_txt ( "select email from return group by email;", $m_rs );
				break;
            default:
				$this->getrecords( "select email from return where mail_id=?;", array($amailid), $m_rs );
		}
        
        while ( $a = $m_rs->fetch() ) {
            echo $a["email"] . "\r\n";
		}
    }
    
    Public Function DeleteEmails(){
		try{
			$this->m_Connect->Execute("delete from return");
			return true;
		}catch(Exception $e){
			echo "删除失败：" . $e->getMessage();
		}
        return false;
    }
    
    Public Function InsertEmail( $aemail, $amailid ){
        //返回信息
        $rtnstr = "";
		$n=0;

		$this->getrecords( "select * from return where email=? and mail_id=?;", array($aemail, $amailid) , $db );
		If ( $a=$db->fetch() ){
			$mp = $this->m_connect->prepare( "UPDATE return SET n=?, lasttime=? WHERE email=? and mail_id=?;" );
			$n = $mp->execute( array( $a["n"]+1,  $this->now(), $aemail, $amailid) );
		}Else{
			$mp = $this->m_connect->prepare( "INSERT INTO return (email, mail_id, n, lasttime) VALUES (?,?, ?, ?);" );
			$n = $mp->execute( array( $aemail, $amailid, 1, $this->now() ) );
		}
		
		$rtnstr = $rtnstr . "<p><b>" . $aemail . "</b></p>" . Chr(13) . Chr(10);
		
		if ( $n == 0 )
		{
			$rtnstr = $rtnstr . "退订保存失败：" . e.getMessage();
			return $rtnstr;
		}

		//保存成功       
		$rtnstr .= file_get_contents("return_output.txt");
        return $rtnstr;
	}
    
    Public Function execsql( $sqltext, $p=null ){
		if ( isset($p) )
		{
			$mt = $this->m_connect->prepare( $sqltext );
			return $mt->execute( $p );
		}
        return $this->m_connect->exec( $sqltext );
    }
    
    Public Function getrecords( $sqlcmd, $p, &$ars ){
		if ( isset($p) )
		{
			$ars =  $this->m_connect->prepare( $sqlcmd );
			$ars->execute( $p );
		}else{
			$this->getrecords_txt( $sqlcmd, $ars );
		}
    }
    
    Public Function getrecords_txt( $sqlcmd, &$ars ){
		try {
			$ars = $this->m_connect->query( $sqlcmd );
		}catch( Exception  $e)
		{
			echo $e;
		}
    }
    
    Public Function checkisreadonly(){
        $rtn = true;
		try{
			$n = $this->m_connect->exec("UPDATE setup SET buseblack=buseblack;" );
			if ( $n>0 )
				$rtn = false;
		}catch(PDOException $e){
            $rtn = $e.getMessage();
        }
        return $rtn;
    }
	
	function getpagesize( $a ) {
		$rs_setup = null;
		$rtn = 0;
		
		if ( $a==1){
			$this->getrecords_txt ( "select mainpgsize from setup", $rs_setup );
			$a = $rs_setup->fetch();
			$rtn = $a["mainpgsize"];
		}
		
		if ( $a==2){
			$this->getrecords_txt ( "select listpgsize from setup", $rs_setup );
			$a = $rs_setup->fetch();
			$rtn = $a["listpgsize"];
		}
		if ($rtn<=0 or $rtn=="") {
			$rtn=30;
		}
		
		return $rtn;
	}
	
	function get_page_data( $cmd, $p, &$recordcount, &$page=null, &$pagesize=null, &$pagecount=null, $pagetype=2  ){
		$sql_cnt = "SELECT count(*) as cnt FROM (" . $cmd . ');';		
		$rs_cnt = $this->m_connect->prepare($sql_cnt);
		$rs_cnt->execute( $p );	
		$a = $rs_cnt->fetch();	
		$recordcount = $a["cnt"];
	
		$page = get_request(false, "page");

		if ($page=="")
			$page=get_request(true, "page");
	
		if ($page=="")
			$page = 1;
		
		$pagesize = $this->getpagesize($pagetype);
		$pagecount = floor($recordcount / $pagesize);
		if ( fmod( $recordcount , $pagesize ) > 0)
			$pagecount ++;
	}
	
	function sql_appent_pageinfo( $sql, $page, $pagesize )
	{
		$rtn = $sql;
		if ( is_numeric($page) and is_numeric($pagesize) )
		{
			if ($page>0 and $pagesize>0){
				$c1 = ( $page - 1 ) * $pagesize;
				$c2 = $pagesize;
				$rtn = "$sql limit $c1, $c2"; 
			}
		}
		
		return $rtn;
	}
	
	function get_a_value( $sql, $colname )
	{
		$selstr = $sql;
		$this->getrecords_txt( $selstr, $rs );
		$a = $rs->fetch();
		$rtn = $a[$colname];
		return $rtn;
	}
	
	function now(){
		$t = date('Y-m-d H:i:s',time());
		return $t;
	}
}
?>
