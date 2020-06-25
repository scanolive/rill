<?php
include 'include/db_config.php';

if(!function_exists('mysql_connect')){
    function mysql_connect($dbhost, $dbuser, $dbpass){
        global $dbport;
        global $dbname;
        global $mysqli;
        $mysqli = mysqli_connect("$dbhost:$dbport", $dbuser, $dbpass, $dbname);
        return $mysqli;
        }
    function mysql_select_db($dbname){
        global $mysqli;
        return mysqli_select_db($mysqli,$dbname);
        }
    function mysql_fetch_array($result){
        return mysqli_fetch_array($result);
        }
    function mysql_fetch_assoc($result){
        return mysqli_fetch_assoc($result);
        }
    function mysql_fetch_row($result){
        return mysqli_fetch_row($result);
        }
    function mysql_query($query){
        global $mysqli;
        return mysqli_query($mysqli,$query);
        }
    function mysql_escape_string($data){
        global $mysqli;
        return mysqli_real_escape_string($mysqli, $data);
        }
    function mysql_real_escape_string($data){
        return mysql_real_escape_string($data);
        }
    function mysql_close(){
        global $mysqli;
        return mysqli_close($mysqli);
        }
    function mysql_errno(){
        global $mysqli;
        return mysqli_errno($mysqli);
        }
    function mysql_error(){
        global $mysqli;
        return mysqli_error($mysqli);
        }

}


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
	if ($msg !== "")
	{
		echo "alert('".$msg."');";	
	}
	echo "window.location.href='".$url."';</script>";
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
function get_sysconfig_arr()
{
	$sql = "select * from sys_config where Uid='NoUID'";
	$sys_rs = getrs($sql);
	$SYSCONFIG = array();
	for( $i=0;$i<count($sys_rs);$i++ )
	{       
		$SYSCONFIG[$sys_rs[$i][3]] = $sys_rs[$i][4];
	}   
	return $SYSCONFIG;
}	

if(!isset($_SESSION))
{
		session_start();
}
if (isset($_SESSION['php_timeout']))
{
		set_time_limit($_SESSION['php_timeout']);
}
?>
