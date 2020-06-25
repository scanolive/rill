<?php
session_start();
include 'include/config.php';
include 'include/is_monitor.php';
$OCT_CMD_PRE = $_SESSION['python_oct_cmd_pre'];
$END_CMD_STR = $_SESSION['python_end_cmd_str'];
$END_STR = $_SESSION['python_end_str'];
$SEP_STR = $_SESSION['python_sep_str'];
$SEP_STR_SE = $_SESSION['python_sep_str_se'];
$NORUN_CMD_ARR = $_SESSION['norun_cmd_arr'];

$s_u_id = trim($_SESSION['rlll_olive_scan_userid']);
$s_u_level = trim($_SESSION['rlll_olive_scan_level']);
$s_u_name = trim($_SESSION['rlll_olive_scan_username']);
$s_s_server_ip = $_SESSION['python_server_ip'];
$s_s_server_port = $_SESSION['python_server_port'];
?>
<html>
<head >
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache;must-revalidate"> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Type" content="Connection; close" />
</head>
<body>
<table width="100%" class="result" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
<?php
//error_reporting(0);  

if (!empty($_POST['cmd']) and !empty($_POST['ip']) and !empty($_POST['verify_str']))
{
	$ip = trim($_POST['ip']);
	$cmdstr = $_POST['cmd'];
    $cmdstr = str_replace("r#o#s_syh",'"',$cmdstr);
    $cmdstr = str_replace("r#o#s_dyh","'",$cmdstr);
    $cmdstr = str_replace("r#o#s_fh",';',$cmdstr);
	$cmdstr = str_replace("r#o#s_fxg",'\\',$cmdstr);
	$cmdstr = str_replace('!a@N#d$','&',$cmdstr);
	$cmd = $cmdstr;
	save_do($s_u_id,trim($s_u_level),"用户".$s_u_name." 操作服务器".$ip."执行命令".$cmd);
	if(get_magic_quotes_gpc())
	{
		$cmd=stripslashes($cmd);
	}
	
	$verify_str = $_POST['verify_str'];
}
else
{
	//$ip = $SERVER_IP;
	$ip = $s_s_server_ip;
	$cmd = "pwd";
	$verify_str = $_SESSION['verify_str'];
}
if ((strstr($ip," ")) or ($ip == "OLIVE_SERVER"))
{
	$client_status = 1;
	$own_ip_yorn = true;
}
else
{	
	$client_status = get_client_status($ip);
	$own_ip_yorn = check_ip($ip,$s_u_id);
}	
 
$main_cmd = $cmd;
$main_cmd_arr = explode(" ",$cmd);
if (!in_array($main_cmd_arr[0],$NORUN_CMD_ARR) and ($client_status == "1") and ($s_u_level < 3  or $own_ip_yorn) and $verify_str == $_SESSION['verify_str'])
{
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket < 0)
	{
 		echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
	}
	else
	{
 		echo date("Y-m-d H:i:s",time());
   		echo "  run   ";
	}
	$result = socket_connect($socket, $s_s_server_ip , $s_s_server_port);
	$cmd_arr = explode($SEP_STR_SE,$cmd);
	if (count($cmd_arr)>1)
		echo $cmd_arr[2];
	else
		echo $cmd;
	echo "<br />";

	$cmd = $OCT_CMD_PRE.$SEP_STR.$ip.$SEP_STR.$cmd.$SEP_STR.$END_CMD_STR;
	socket_write($socket, $cmd, strlen($cmd));
	$data = "";
	
	while(true)
	{
		$buf = socket_read($socket,1024);
		if((!$buf) or($buf == $END_STR))
		{ 
			break; 
		}
		$data.=$buf;
		if(trim($buf) == $END_STR)
		{
			break ;
		}
	}
	$data = str_replace(" ","&nbsp;&nbsp;",$data);
	$data = str_replace("\n","<br />",$data);
	$data = substr($data,0,strlen($data)-strlen($END_STR));
	
	echo  $data;
	socket_close($socket);
	save_do($s_u_id,trim($s_u_level),"用户".$s_u_name." 操作服务器".$ip."执行命令".$main_cmd);
}

else if (in_array($main_cmd_arr[0],$NORUN_CMD_ARR))
{
	echo $cmd."不允许执行";
}
else if ($client_status != "1")
{
	echo $ip." CLIENT ERR!";
}
else if (( $s_u_level > 2 ) and !($own_ip_yorn))
{
	echo "你没有此IP的权限";
}
else if  ($verify_str != $_SESSION['verify_str'])
{
	echo "Get out!";
}
else
{
	echo "UNKOWN ERR!";
}
?></td>
  </tr>
</table>
</body>
</html>
