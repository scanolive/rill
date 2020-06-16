<?php
function connect_db()
{
	$conn = mysql_connect(DBHOST.":".DBPORT,DBUSERNAME,DBPASSWD);
	if (!$conn)
	{
		die('Could not connect: ' . mysql_error());	
	}
	else
	{
		$use_db = @mysql_select_db(DBNAME);
		if ( ! $use_db )
		{
			echo "NO DB ".DBNAME;
		}
	}	
}

function closedb()
{
	mysql_close ();
}

function getrs($sql)
{	
	connect_db();
	mysql_query("SET NAMES 'UTF8'");
	mysql_query("SET CHARACTER SET UTF8");
	mysql_query("SET CHARACTER_SET_RESULTS=UTF8'");
	$result = mysql_query ( $sql);
	$rs_a = array();
	if ($result)
	{
		while ( $rs = mysql_fetch_array ( $result ) )
		{
			$rs_a[] = $rs;
		}
	}	
	return $rs_a;
	closedb();
}

function do_sql($sql)
{
	connect_db();
	mysql_query("SET NAMES 'UTF8'");
	mysql_query("SET CHARACTER SET UTF8");
	mysql_query("SET CHARACTER_SET_RESULTS=UTF8'");
	mysql_query($sql);
	closedb();
}

function save_do($uid,$typelevel,$content)
{
	$sql = "insert into history set Content = '$content',  Uid='$uid', TypeLevel = '$typelevel'";
	do_sql($sql);
}

function alert_go($msg,$url)
{
	echo "<script charset='UTF-8' language='javascript'>";
	echo "alert('";
	echo $msg;
	echo "');window.location.href='";
	echo $url;
	echo "';</script>";
}

function alert($msg)
{
	echo "<script charset='UTF-8' language='javascript'>";
	echo "alert('".$msg."')";
	echo ";</script>";
}

function check_ip($ip,$uid)
{
	$sql = "select ipinfo.ip from ipinfo,ipgroup,userofgroup,users where ipinfo.GroupId = ipgroup.id and  Uid=users.id and Gid=ipgroup.id and users.id='$uid' and ipinfo.Ip ='$ip'";
	$ip_rs = getrs($sql);
	if (count($ip_rs) == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_config($cfgtype,$keyname,$uid,$DEFAULT)
{
	$pri_sql = "select KeyValue,id from sys_config where  Uid ='$uid' and KeyName = '$keyname' and CfgType = '$cfgtype'";
	$value_rs = getrs($pri_sql);
	if ($value_rs and  strlen($value_rs[0][1]) !== 0)
	{
		$value = $value_rs[0][0];
	}	
	else
	{
		if ( $uid == "NoUid")
		{
			$value = $DEFAULT[$keyname];
		}
		else
		{
			$pub_sql = "select KeyValue,id from sys_config where  Uid ='NoUid' and KeyName = '$keyname' and CfgType = 'public'";
			$value_rs = getrs($pub_sql);
			if (strlen($value_rs[0][1]) !== 0)
			{
				$value = $value_rs[0][0];
			}
			else
			{	
				$value = $DEFAULT[$keyname];
			}	
		}	
	}
	return $value;
}

function update_config($cfgtype,$keyname,$keyvalue,$uid)
{
	$sql = "select KeyValue,id from sys_config where  Uid ='$uid' and KeyName = '$keyname' and CfgType = '$cfgtype'";	
	$value_rs = getrs($sql);
	if (count($value_rs) == 0)
	{
		$update_sql = "insert sys_config set Uid = '$uid', CfgType = '$cfgtype',KeyName = '$keyname',KeyValue='$keyvalue'";
	}
	else
	{
		$id = $value_rs[0][1];
		$update_sql = "update sys_config set Uid = '$uid', CfgType = '$cfgtype',KeyName = '$keyname',KeyValue='$keyvalue'  where id = '$id'";
	}
	do_sql($update_sql);
}

function get_client_status($ip)
{
	$sql = "select ClientStatus from ipinfo where ip='$ip'";
	$client_status_rs = getrs($sql);
	if (count($client_status_rs) >= 1)
	{
		return $client_status_rs[0][0];
	}
	else 
	{
		return 0;
	}	
}
function close_window()
{
	echo "<script>window.opener=null;window.open('','_self');window.close();</script>";
}
function randstr($len) 
{
	$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
	mt_srand((double)microtime()*1000000*getmypid());
	$password='';
	while(strlen($password)<$len)
		$password.=substr($chars,(mt_rand()%strlen($chars)),1);
	return $password;
}

function GetIP(){
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
$ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
$ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
$ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
$ip = $_SERVER['REMOTE_ADDR'];
else
$ip = "unknown";
return($ip);
} 
function  request($request)
{

		if (isset($request))
		{
			return $request;
		}		
		else
		{
			return "";
		}	
}	

$sql = "select * from sys_config where Uid='NoUID'";
$sys_rs = getrs($sql);
$SYSCONFIG = array();
for( $i=0;$i<count($sys_rs);$i++ )
{       
		        $SYSCONFIG[$sys_rs[$i][3]] = $sys_rs[$i][4];
}           
$TIMEOUT = $SYSCONFIG['php_timeout'];
set_time_limit($TIMEOUT);
$SERVER_SOCKET_PORT = $SYSCONFIG['python_server_port'];
$SERVER_IP = $SYSCONFIG['python_server_ip'];

$OCT_CMD_PRE = $SYSCONFIG['python_oct_cmd_pre'];
$END_CMD_STR = $SYSCONFIG['python_end_cmd_str'];
$END_STR = $SYSCONFIG['python_end_str'];
$SEP_STR = $SYSCONFIG['python_sep_str'];
$SEP_STR_SE = $SYSCONFIG['python_sep_str_se'];

$UPFILE_DIR = $SYSCONFIG['upfile_dir'];
$WEB_ROOT = realpath(dirname(__FILE__).'/../');
$UPFILE_ALLPATH = $WEB_ROOT."/".$UPFILE_DIR;
$MAX_UPFILE_SIZE = $SYSCONFIG['max_upfile_size'];
$TITLE_NAME = $SYSTEM_NAME = $SYSCONFIG['system_name'];
$NORUN_CMD_ARR = explode(',',$SYSCONFIG['norun_cmd']);

/*
$OCT_CMD_PRE = "OLIVE_CTRL_CENTER_CMD";
$END_CMD_STR = "OLIVE_EOC";
$END_STR = "OLIVE_EOS";
$SEP_STR = "@!@";
$SEP_STR_SE = "R!I@L#L";
 */


if(!isset($_SESSION))
{
		session_start();
}
?>
